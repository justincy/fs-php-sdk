<?php

  namespace FS;
  
  class Relationship extends \Org\Gedcomx\Conclusion\Relationship {
  
    private $client;   
    private $spouse;
  
    public function __construct($data, $client) {
      
      parent::__construct($data);
      
      $this->client = $client;
    
    }
    
    public function setSpouse($spouse) {
      $this->spouse = $spouse;
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
  
  }

?>