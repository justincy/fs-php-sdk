<?php

  namespace FS;
  
  class Relationship extends \Org\Gedcomx\Conclusion\Relationship {
  
    private $client;
  
    public function __construct($data, $client) {
      
      parent::__construct($data);
      
      $this->client = $client;
      
    }
  
    public function getSpouse() {
      return $this->getPerson($this->spouse->resource);
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
    
    public function getFather() {
      return $this->getPerson($this->father->resource);
    }
    
    public function getMother() {
      return $this->getPerson($this->mother->resource);
    }
    
    public function getChild() {
      return $this->getPerson($this->child->resource);
    }
    
    private function getPerson($resource) {
      return $this->client->getFSJson($resource)->getPerson();
    }
  
  }

?>