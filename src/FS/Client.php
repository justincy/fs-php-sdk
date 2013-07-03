<?php

  namespace FS;
  
  use Guzzle\Http\Client as Guzzle;
  
  /**
   * Main FamilySearch Class
   */
  class Client extends Guzzle {
  
    private $reference;
    private $baseUrl;
    private $devKey;
    private $accessToken;
    private $client;
    private $discovery;
  
    /**
     * Constructor for the FS Class
     */
    public function __construct($devKey, $reference = 'production', $baseUrl = null) {
      
      $this->reference = $reference;
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
        $response = $this->getFS('/.well-known/app-meta');
        $this->discovery = $response->getLinks();
        
        // If we're using the mock api, update the discovery links
        // so that they have the correct domain
        if( $this->reference == 'mock-api' ) {
          foreach($this->discovery as $rel => $link) {
            if($link->getTemplate()) {
              $this->discovery[$rel]->setTemplate(str_replace('https://familysearch.org', $this->baseUrl, $link->getTemplate()));
            }
            if($link->getHref()) {
              $this->discovery[$rel]->setHref(str_replace('https://familysearch.org', $this->baseUrl, $link->getHref()));
            }
          }
        }
      } 
      return $this->discovery;
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
      $authorizeUrl = $link->getHref();
      return $authorizeUrl . '?response_type=code&client_id=' 
          . $this->devKey . '&redirect_uri=' . $redirectUri;
    }
    
    /**
     * Finish OAuth2 authentication by retrieving an access token.
     */
    public function getOAuth2AccessToken($code) {
      $link = $this->getDiscoveryLink('http://oauth.net/core/2.0/endpoint/token');
      $tokenUrl = $link->getHref()
          . '?grant_type=authorization_code&client_id=' . $this->devKey 
          . '&code=' . $code;
      $response = $this->post($tokenUrl)
        ->send()
        ->json();
      return $response['access_token'];
    }
    
    /**
     * Get the current user's profile information
     */
    public function getCurrentUser() {
      return $this->getDLink('current-user')->getUsers()[0];
    }
    
    /**
     * Get the current user's person in the tree
     */
    public function getCurrentUserPerson() {
      return $this->getDLink('current-user-person');
    }
    
    /**
     * Search for a person
     */
    public function personSearch($queryParams) {
      $query = $this->processSearchQueryParams($queryParams);
      return $this->getDLink('person-search', array('q' => $query));
    }
    
    /**
     * Search for matches based on query parameters
     */
    public function personMatchesQuery($queryParams) {
      $query = $this->processSearchQueryParams($queryParams);
      return $this->getDLink('person-matches-query', array('q' => $query));
    }
    
    /**
     * Get a list of matches for an existing person
     */
    public function getPersonMatches($personId) {
      return $this->getDLink('person-matches-template', array('pid' => $personId));
    }
    
    /**
     * Get a person
     */
    public function getPerson($personId) {
      return $this->getDLink('person-template', array('pid' => $personId));
    }
    
    /**
     * Get a person and all of their relationships
     */
    public function getPersonWithRelationships($personId) {
      return $this->getDLink('person-with-relationships-query', array('person' => $personId));
    }
    
    /**
     * Get a list of spouse relationships for a person
     */
    public function getSpouseRelationships($personId) {
      return $this->getDLink('spouse-relationships-template', array('pid' => $personId));
    }
    
    /**
     * Wraps both getFS() and getDiscoveryLink() so that you can do
     * $this->getDLink($rel) instead of $this->getFS($this->getDiscoveryLink($rel))
     */
    public function getDLink($rel, $templateVars = null) {
      return $this->getFS($this->getDiscoveryLink($rel), $templateVars);
    }
    
    /**
     * Fetch a FamilySearch resource
     */
    public function getFS($url, $templateVars = null) {
      
      // If $url is link object, then pull out the 'href' or 'template'.
      // This allows for link objects from the Discovery resource to
      // be passed in as the $url parameter.
      if( $url instanceof \Org\Gedcomx\Links\Link ) {
        if( $url->getHref() ) {
          $url = $url->getHref();
        } else if( $url->getTemplate() ) {
          $url = $url->getTemplate();
        } else {
          throw new \Exception('Invalid link object given');
        }
      }

      // Setup the params array when using uri templates
      if( $templateVars && is_array($templateVars) ) {
        $getParams = array($url, $templateVars);
      } else {
        $getParams = $url;
      }
      
      $request = $this->get($getParams)
        ->addHeader('Accept', 'application/x-fs-v1+json, application/x-gedcomx-atom+json');
      if( $this->accessToken ) {
        $request->addHeader('Authorization', 'Bearer ' . $this->accessToken);
      }
      
      $response = $request->send();
      $returnType = $response->getHeader('Content-Type');
      $responseJson = $response->json();
      
      // Convert response to an object so we can add method extentions
      if( $returnType == 'application/x-fs-v1+json' ) {
        return new Response($responseJson, $this);
      } elseif( $returnType == 'application/x-gedcomx-atom+json' ) {
        return new AtomResponse($responseJson, $this);
      }
      
    }
    
    /**
     * Processes search and match query parameters from an array
     * and returns a proper query string
     */
    private function processSearchQueryParams($queryParams) {
    
      // Create a list of parameters in the name=value format
      $formattedParams = array();
      foreach($queryParams as $name => $value) {
        $formattedParams[] = $name . ':' . (strpos($value, '"') === false ? '"' . $value . '"' : $value);
      }
      
      // Concatenate the list with &
      return implode('&', $formattedParams);
    
    }
    
  }

?>