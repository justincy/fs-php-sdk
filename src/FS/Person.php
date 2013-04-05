<?php

  namespace FS;
  
  class Person extends \Org\Gedcomx\Conclusion\Person {
  
    private $preferredName;
    private $alternateNames = array();
    
    private $birth;
    private $christening;
    private $death;
    private $burial;
    
    // Stores facts that aren't birth, christening, death, or burial
    private $nonVitals = array();
  
    public function __construct($data, $response) {
      
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
      
      // Convert the facts and find the vital events
      foreach($this->facts as $i => $fact) {
      
        $this->facts[$i] = $fact = new Fact($fact->toArray(), $response);
        
        switch($fact->type) {
          case 'http://gedcomx.org/Birth':
            $this->birth = $fact;
            break;
          case 'http://gedcomx.org/Christening':
            $this->christening = $fact;
            break;
          case 'http://gedcomx.org/Death':
            $this->death = $fact;
            break;
          case 'http://gedcomx.org/Burial':
            $this->burial = $fact;
            break;
          default:
            $this->nonVitals[] = $fact;
            break;
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
    
    public function getBirth() {
      return $this->birth;
    }
    
    public function getChristening() {
      return $this->christening;
    }
    
    public function getDeath() {
      return $this->death;
    }
    
    public function getBurial() {
      return $this->burial;
    }
    
    public function getFacts($types = null) {
      
      // If a type was specfied, filter the facts
      // by the given type
      if($types) {
      
        $filteredFacts = array();
        
        // Allow for multiple types to be given.
        // If an array isn't given, convert it to an array
        if( !is_array($type) ) {
          $types = array($types);
        }
        
        foreach($this->facts as $fact) {
          if( in_array($fact->type, $types) ) {
            $filteredFacts[] = $fact;
          }
        }
        
        return $filteredFacts;
      }
      
      // Return all facts if no type was given
      else {
        return $this->facts;
      }
      
    }
    
    public function getNonVitalFacts() {
      return $this->nonVitals;
    }
    
  }
  
?>