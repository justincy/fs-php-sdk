<?php

  namespace FS;
  
  use Guzzle\Http\Client as Guzzle;
  
  /**
   * Main FamilySearch Class
   */
  class Client extends Guzzle {
  
    private $baseUrl;
    private $devKey;
    private $accessToken;
    private $client;
    private $discovery;
  
    /**
     * Constructor for the FS Class
     */
    public function __construct($devKey, $reference = 'production', $baseUrl = null) {
      
      // Save the devkey
      $this->devKey = $devKey;      
      
      // Set the base url
      if( $baseUrl != null ) {
        $this->baseUrl = $baseUrl;
      } else if( $reference == 'production' ) {
        $this->baseUrl =  'https://familysearch.org';
      } else {
        $this->baseUrl = 'https://sandbox.familysearch.org';
      }
      
      // Instantiate Guzzle HTTP client
      parent::__construct($this->baseUrl);
      
    }
    
    /**
     * Store the access token obtained via OAuth2 so that
     * subsequent API requests can be authenticated
     */
    public function setAccessToken($accessToken) {
      $this->accessToken = $accessToken;
    }
    
    /**
     * Begin OAuth2 Authorization by redirecting the user
     * to the authorize endpoint with the necessary parameters
     */
    public function startOAuth2Authorization($redirectUri) {
      header('Location: ' . $this->getOAuth2AuthorizeUrl($redirectUri));
      exit;
    }
    
    /**
     * Return the url for the authorize endpoint with
     * the necessary parameters for beginning OAuth2
     */
    public function getOAuth2AuthorizeUrl($redirectUri) {
      $discovery = $this->getDiscovery();
      $authorizeUrl = $discovery['http://oauth.net/core/2.0/endpoint/authorize']['href'];
      return $authorizeUrl . '?response_type=code&client_id=' 
          . $this->devKey . '&redirect_uri=' . $redirectUri;
    }
    
    /**
     * Finish OAuth2 authentication by retrieving an access token.
     */
    public function getOAuth2AccessToken($code) {
      $discovery = $this->getDiscovery();
      $tokenUrl = $discovery['http://oauth.net/core/2.0/endpoint/token']['href']
          . '?grant_type=authorization_code&client_id=' . $this->devKey 
          . '&code=' . $code;
      $response = $this->post($tokenUrl)
        ->send()
        ->json();
      return $response['access_token'];
    }
    
    /**
     * Get the current user's person in the tree
     */
    public function getCurrentUserPerson() {
      return $this->getFSJson($this->getDiscoveryLink('current-user-person'));
    }
    
    /**
     * Get a person and all of their relationships
     */
    public function getPersonWithRelationships($personId) {
      return $this->getFSJson($this->getDiscoveryLink('person-with-relationships-query'), array('person' => $personId));
    }
    
    /**
     * Get a link or template from the Discovery resource
     */
    public function getDiscoveryLink($rel) {
      $discovery = $this->getDiscovery();
      return isset($discovery[$rel]) ? $discovery[$rel] : null;
    }
    
    /**
     * Get the Discovery resource
     */
    public function getDiscovery() {
      if( !$this->discovery ) {
        $response = $this->getAtomJson('/.well-known/app-meta');
        $this->discovery = $response['links'];
      } 
      return $this->discovery;
    }
    
    /**
     * Fetch a resource using the "application/x-gedcomx-atom+json" media type
     */
    public function getAtomJson($url, $templateVars = null) {
      return $this->getResource($url, 'application/x-gedcomx-atom+json', $templateVars);
    }
    
    /**
     * Fetch a resource using the "application/x-fs-v1+json" media type
     */
    public function getFSJson($url, $templateVars = null) {
      return $this->getResource($url, 'application/x-fs-v1+json', $templateVars);
    }
    
    /**
     * Fetch a resource
     */
    public function getResource($url, $mediaType, $templateVars = null) {
      
      // If $url is an array, then pull out the 'href' or 'template'.
      // This allows for link objects from the Discovery resource to
      // be passed in as the $url parameter.
      if( is_array($url) ) {
        if( isset($url['href']) ) {
          $url = $url['href'];
        } else if( isset($url['template']) ) {
          $url = $url['template'];
        } else {
          throw new Exception('Invalid link object given');
        }
      }

      // Setup the params array when using uri templates
      if( $templateVars && is_array($templateVars) ) {
        $getParams = array($url, $templateVars);
      } else {
        $getParams = $url;
      }
      
      $request = $this->get($getParams)
        ->addHeader('Accept', $mediaType);
      if( $this->accessToken ) {
        $request->addHeader('Authorization', 'Bearer ' . $this->accessToken);
      }
      
      $response = $request->send()->json();
      
      // Convert response to an object so we can add method extentions
      return new FSResponse($response);
      
    }
    
    /**
     * Returns true if the given string is a URI
     */
    public static function isURI($str) {
      return strpos($str, '/') !== false;
    }
    
  }
  
  /**
   * Custom response object so we can add methods to response for
   * easy processing of and access to response data
   */
  class FSResponse implements \ArrayAccess {
  
    // Associative array of the response data obtained
    // by deserializing the JSON response. The array is
    // specified when calling the constructor.
    private $data;
    
    // Lists of the relationships
    private $spouses = array();
    private $children = array();
    private $parents = array();
    
    // Have the couple relationships been processed to set 
    // the 'spouse' attribute and separate them from other 
    // relationships?
    private $processedSpouses = false;
    
    // Have the child-and-parents relationships been processed
    // to separate the children from the parent relationships?
    private $processedChildAndParents = false;
  
    public function __construct($response) {
      if( !is_array($response) ) {
        throw new Exception("Invalid response data; array expected");
      }
      
      $this->data = $response;
    }
    
    //
    // ArrayAccess methods
    // This allows us to index the object like an array
    //

    public function offsetGet($offset) {
      return array_key_exists($offset, $this->data) ? $this->data[$offset] : null;
    }

    public function offsetSet($offset, $value) {
      throw new Exception("Not a good idea to modify the response data!");
    }

    public function offsetExists($offset) {
      return array_key_exists($offset, $this->data);
    }

    public function offsetUnset($offset) {
      unset($this->data[$offset]);
    }
    
    //
    // FS helper methods
    //
    
    /**
     * Returns the person in the response. Throws an exception
     * if the response doesn't contain exactly 1 person
     */
    public function getPerson() {
      $numPersons = count($this->data['persons']);
      if( $numPersons != 1 ) {
        throw new Exception("Only allowed to use the method when the response contains exactly 1 person; response contains $numPersons");
      }
      return $this->data['persons'][0];      
    }
    
    /**
     * Returns the relationships of the person to their parents
     */
    public function getParents() {
      $this->processChildAndParents();
      return $this->parents;
    }
    
    public function getChildren() {
      $this->processChildAndParents();
      return $this->children;
    }
    
    private function processChildAndParents() {
      if( $this->processedChildAndParents ) {
        return;
      }
      
      $person = $this->getPerson();
      
      foreach($this->data['childAndParentsRelationships'] as $rel) {
        
        // If the current person the child then store the relationship
        // as a parent relationship
        if( $person['id'] == $rel['child']['resourceId'] ) {
          $this->parents[] = $rel;
        } 
        
        // If the person is not the child then store the relationship
        // as a child relationship
        else {
          $this->children[] = $rel;
        }
      }
      
      $this->processedChildAndParents = true;
    }
    
    /**
     * Returns couple relationships after setting the 'spouse' attribute
     */
    public function getSpouses() {
      $this->processSpouses();
      return $this->spouses;
    }
    
    private function processSpouses() {
      if( $this->processedSpouses ) {
        return;
      }
      
      $person = $this->getPerson();
      
      foreach($this->data['relationships'] as $rel) {
        if( $rel['type'] == 'http://gedcomx.org/Couple' ) {
          if( $rel['person1']['resourceId'] == $person['id'] ) {
            $rel['spouse'] = $rel['person2'];
          } else if( $rel['person2']['resourceId'] == $person['id'] ) {
            $rel['spouse'] = $rel['person1'];
          } else {
            throw new Exception("Found a spouse relationship that is not applicable to this person");
          }
          $this->spouses[] = $rel;
        }
      }
      
      $this->processedSpouses = true;
    }
  
  }

?>