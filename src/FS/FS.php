<?php

  namespace FS;
  
  use FS\Person;
  use Guzzle\Http\Client as Guzzle;
  
  /**
   * Main FamilySearch Class
   */
  class Client {
  
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
      
      // Instantiate HTTP client
      $this->client = new Guzzle($this->baseUrl);
      
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
      $discovery = $this->getdiscovery();
      $tokenUrl = $discovery['http://oauth.net/core/2.0/endpoint/token']['href']
          . '?grant_type=authorization_code&client_id=' . $this->devKey 
          . '&code=' . $code;
      $response = $this->client
        ->post($tokenUrl)
        ->send()
        ->json();
      return $response['access_token'];
    }
    
    /**
     * Get the Discovery resource
     */
    public function getDiscovery() {
      if( !$this->discovery ) {
        $response = $this->client
          ->get('/.well-known/app-meta')
          ->addHeader('Accept', 'application/x-gedcomx-atom+json')
          ->send()
          ->json();
        $this->discovery = $response['links'];
      } 
      return $this->discovery;
    }
    
    /**
     * Get the current user's person in the tree
     */
    public function getCurrentUserPerson() {
      $discovery = $this->getDiscovery();
      return $this->getPerson($discovery['current-user-person']['href']);
    }
    
    /**
     * Get a person
     */
    public function getPerson($personUri) {
      $response = $this->client
        ->get($personUri)
        ->addHeader('Accept', 'application/x-fs-v1+json')
        ->addHeader('Authorization', 'Bearer ' . $this->accessToken)
        ->send()
        ->json();
      return new Person($response);
    }
    
  }

?>