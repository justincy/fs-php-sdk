<?php

  namespace FS;

  /**
   * Custom response object so we can add methods to response for
   * easy processing of and access to response data. Extends the
   * object which represents the application/x-fs-v1+json media type
   * so we are essentially adding helper methods to the response's
   * document object.
   */
  class Response extends \Org\Familysearch\Platform\FamilySearchPlatform {
    
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
  
    /**
     * $response is an associative array obtained by parsing a
     * JSON response from the APIs
     */
    public function __construct($response, $client) {
      if( !is_array($response) ) {
        throw new \Exception("Invalid response data; array expected");
      }
      
      parent::__construct($response);
      
      $this->client = $client;
      
      // Replace person objects with our wrapper object
      foreach($this->persons as $i => $person) {
        $this->persons[$i] = new Person($person->toArray(), $this, $client);
      }
      
      // Convert relationships
      foreach($this->relationships as $i => $relationship) {
        $this->relationships[$i] = new Relationship($relationship->toArray(), $client);
      }
      foreach($this->childAndParentsRelationships as $i => $relationship) {
        $this->childAndParentsRelationships[$i] = new ChildAndParentsRelationship($relationship->toArray(), $client);
      }
    }
    
    /**
     * Returns the person in the response. Throws an exception
     * if the response doesn't contain exactly 1 person
     */
    public function getPerson() {
      $numPersons = count($this->persons);
      if( $numPersons != 1 ) {
        throw new Exception("Only allowed to use the method when the response contains exactly 1 person; response contains $numPersons");
      }
      return $this->persons[0];      
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
      
      foreach($this->childAndParentsRelationships as $rel) {
        
        // If the current person the child then store the relationship
        // as a parent relationship
        if( $person->id == $rel->child->resourceId ) {
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
      
      foreach($this->relationships as $rel) {
        if( $rel->type == 'http://gedcomx.org/Couple' ) {
          if( $rel->person1->resourceId == $person->id ) {
            $rel->spouse = $rel->person2;
          } else if( $rel->person2->resourceId == $person->id ) {
            $rel->spouse = $rel->person1;
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