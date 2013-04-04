<?php

  namespace FS;
  
  class Person extends \Org\Gedcomx\Conclusion\Person {
  
    private $preferredName;
    private $alternateNames = array();
  
    public function __construct($data) {
      
      parent::__construct($data);
            
      // Convert the names and separate the
      // preferred name from the alternate names
      foreach($this->names as $i => $name) {
        $this->names[$i] = $name = new Name($name->toArray());
        if( $name->preferred ) {
          $this->preferredName = $name;
        } else {
          $this->alternateNames[] = $name;
        }
      }
      
    }
  
    /**
     * Returns the list of all names for a person
     */
    public function getNames() {
      return $this->names;
    }
    
    /**
     * Returns the preferred name for a person
     */
    public function getPreferredName() {
      return $this->preferredName;
    }
    
    /**
     * Returns the list of alternate names for a person
     */
    public function getAlternateNames() {
      return $this->alternateNames;
    }
    
  }
  
?>