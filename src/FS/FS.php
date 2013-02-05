<?php

  namespace FS;
  
  use Guzzle\Http\Client as Guzzle;
  
  /**
   * Main FamilySearch Class
   */
  class Client {
  
    private $baseUrl;
    private $devKey;
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
        $this->baseUrl = 'https://sandbox.familsearch.org';
      }
      
      // Instantiate HTTP client
      $this->client = new Guzzle($this->baseUrl);
      
    }
    
    /**
     * Begin OAuth2 Authorization by redirecting the user
     * to the authorize endpoint with the necessary parameters
     */
    public function OAuth2Authorize($redirectUri) {
      header('Location: ' . getOAuth2AuthorizeUrl($redirectUri));
      exit;
    }
    
    /**
     * Return the url for the authorize endpoint with
     * the necessary parameters for beginning OAuth2
     */
    public function getOAuth2AuthorizeUrl($redirectUri) {
      $discovery = $this->getDiscovery();
      $authorizeUrl = $discovery['http://oauth.net/core/2.0/endpoint/authorize'];
      return $authorizeUrl . '?response_type=code&client_id=' 
          . $this->devKey . '&redirect_uri=' . $redirectUri;
    }
    
    /**
     * Finish OAuth2 authentication by retrieving an access token.
     */
    public function getOAuth2AccessToken($code) {
      $discovery = $this->getdiscovery();
      $tokenUrl = $discovery['http://oauth.net/core/2.0/endpoint/token']
          . '?grant_type=authorization_code&client_id=' . $this->devKey 
          . '&code=' . $code;
      return $this->client
        ->get($tokenUrl)
        ->send()
        ->json();
    }
    
    /**
     * Get the Discovery resource
     */
    public function getDiscovery() {
      if( !$this->discovery ) {
        $this->discovery = $this->client
          ->get('/.well-known/app-meta')
          ->addHeader('application/x-gedcomx-atom+json')
          ->send()
          ->json();
      } 
      return $this->discovery();
    }
  
  }

?>