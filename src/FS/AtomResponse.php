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
      $entries = $this->getEntries();
      foreach($entries as $i => $entry) {
        $entries[$i]->getContent()->setGedcomx(new Response($entry->getContent()->getGedcomx()->toArray(), $client));
      }
      $this->setEntries($entries);
      
    }
  
  }

?>