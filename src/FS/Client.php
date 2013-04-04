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
      $link = $this->getDiscoveryLink('http://oauth.net/core/2.0/endpoint/authorize');
      $authorizeUrl = $link->href;
      return $authorizeUrl . '?response_type=code&client_id=' 
          . $this->devKey . '&redirect_uri=' . $redirectUri;
    }
    
    /**
     * Finish OAuth2 authentication by retrieving an access token.
     */
    public function getOAuth2AccessToken($code) {
      $link = $this->getDiscoveryLink('http://oauth.net/core/2.0/endpoint/token');
      $tokenUrl = $link->href
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
      if( isset($discovery[$rel]) ) {
        return $discovery[$rel];
      } else {
        return null;
      }
    }
    
    /**
     * Get the Discovery resource
     */
    public function getDiscovery() {
      if( !$this->discovery ) {
        $response = $this->getAtomJson('/.well-known/app-meta');
        $this->discovery = $response->links;
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
      
      // If $url is link object, then pull out the 'href' or 'template'.
      // This allows for link objects from the Discovery resource to
      // be passed in as the $url parameter.
      if( $url instanceof \Org\Gedcomx\Links\Link ) {
        if( isset($url->href) ) {
          $url = $url->href;
        } else if( isset($url->template) ) {
          $url = $url->template;
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
      return new Response($response);
      
    }
    
    /**
     * Returns true if the given string is a URI
     */
    public static function isURI($str) {
      return strpos($str, '/') !== false;
    }
    
  }

?>