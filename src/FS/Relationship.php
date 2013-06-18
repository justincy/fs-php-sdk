<?php

  namespace FS;
  
  class Relationship extends \Org\Gedcomx\Conclusion\Relationship {
  
    private $client;
    
    private $spouse;
  
    public function __construct($data, $client) {
      
      parent::__construct($data);
      
      $this->client = $client;
      
    }
  
    public function getSpouse() {
      return $this->getPerson($this->spouse->resource);
    }
    
    public function setSpouse($spouse) {
      $this->spouse = $spouse;
    }
    
    public function getPerson1() {
      return $this->getPerson($this->person1->resource);
    }
    
    public function getPerson2() {
      return $this->getPerson($this->person2->resource);
    }
    
    private function getPerson($resource) {
      return $this->client->getFSJson($resource)->getPerson();
    }
    
  }
  
  class ChildAndParentsRelationship extends \Org\Familysearch\Platform\Ct\ChildAndParentsRelationship {
  
    private $client;
  
    public function __construct($data, $client) {
      
      parent::__construct($data);
      
      $this->client = $client;
      
    }
    
    public function hasFather() {
      return isset($this->father);
    }
    
    public function hasMother() {
      return isset($this->mother);
    }
    
    public function getFather() {
      if( $this->father ) {
        return $this->getPerson($this->father->resource);
      } else {
        return null;
      }
    }
    
    public function getMother() {
      if( $this->mother ) {
        return $this->getPerson($this->mother->resource);
      } else {
        return null;
      }
    }
    
    public function getChild() {
      return $this->getPerson($this->child->resource);
    }
    
    private function getPerson($resource) {
      return $this->client->getFSJson($resource)->getPerson();
    }
  
  }

?>