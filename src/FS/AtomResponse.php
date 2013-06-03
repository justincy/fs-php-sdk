<?php

  namespace FS;
  
  /**
   * Custom response object for JSON Atom feeds. Adds helper methods
   * to the structure defined by the application/x-gedcomx-atom+json type.
   */
  class AtomResponse extends \Org\Gedcomx\Atom\Feed {
  
    /**
     * $response is an associative array obtained by parsing a
     * JSON response from the APIs
     */
    public function __construct($response, $client) {
      
      parent::__construct($response);
      
      $this->client = $client;
      
      // Convert all of the entries[x].content.gedcomx attributes 
      // to Response objects
      foreach($this->entries as $i => $entry) {
        $this->entries[$i]->content->gedcomx = new Response($entry->content->gedcomx->toArray(), $client);
      }
      
    }
    
    /**
     * Get a list of the entries in the feed
     */
    public function getEntries() {
      return $this->entries;
    }
  
  }

?>