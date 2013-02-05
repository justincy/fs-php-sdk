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
  
    /**
     * Constructor for the FS Class
     */
    public function __construct($devKey, $reference = 'production', $baseUrl = null) {
      
      $this->devKey = $devKey;      
      
      if( $baseUrl != null ) {
        $this->baseUrl = $baseUrl;
      } else if( $reference == 'production' ) {
        $this->baseUrl =  'https://familysearch.org';
      } else {
        $this->baseUrl = 'https://sandbox.familsearch.org';
      }
      
      $this->client = new Guzzle($this->baseUrl);
      
    }
    
    /**
     * Get the Discovery resource
     */
    public function discovery() {
      return $this->client->get('/.well-known/app-meta.json')->send()->json();
    }
  
  }

?>