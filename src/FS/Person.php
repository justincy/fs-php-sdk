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
    
    private $response;
  
    public function __construct($data, $response) {
      
      $this->response = $response;
      
      parent::__construct($data);
            
      // Convert the names and separate the
      // preferred name from the alternate names
      $names = $this->getNames();
      foreach($names as $i => $name) {
        $names[$i] = $name = new Name($name->toArray());
        if( $name->getPreferred() ) {
          $this->preferredName = $name;
        } else {
          $this->alternateNames[] = $name;
        }  
      }
      $this->setNames($names);
      
      // Convert the facts and find the vital events
      $facts = parent::getFacts();
      foreach($facts as $i => $fact) {
      
        $facts[$i] = $fact = new Fact($fact->toArray(), $response);
        
        switch($fact->getType()) {
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
      $this->setFacts($facts);
      
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
        if( !is_array($types) ) {
          $types = array($types);
        }
        
        foreach(parent::getFacts() as $fact) {
          if( in_array($fact->getType(), $types) ) {
            $filteredFacts[] = $fact;
          }
        }
        
        return $filteredFacts;
      }
      
      // Return all facts if no type was given
      else {
        return parent::getFacts();
      }
      
    }
    
    public function getNonVitalFacts() {
      return $this->nonVitals;
    }
    
    /**
     * Get a list of possible matches for this person
     */
    public function getMatches() {
      return $this->response->getClient()->getPersonMatches($this->id);
    }
    
  }
  
?>