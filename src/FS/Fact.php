<?php

  namespace FS;

  class Fact extends \Org\Gedcomx\Conclusion\Fact {
  
    public function __construct($data, $response) {
    
      parent::__construct($data);
      
      // Convert date
      if( $this->date ) {
        $this->date = new Date($this->date->toArray());
      }
      
      
      if( $this->place ) {
      
        // If a place reference exists, remove the # and use the 
        // place reference to lookup the place value from the response
        if( $this->place->descriptionRef ) {
          $placeId = substr($this->place->descriptionRef, 1);
          foreach($response->places as $place) {
            if( $place->id == $placeId ) {
              $this->place = new Place($place->toArray());
            }
          }
        }
        
        // If the place ref doesn't exist, take the original value
        // and convert it into a place
        else {
          $this->place = new Place( array( 'names' => array( array( 'value' => $this->place->original ) ) ) );
        }
        
      }
      
    }
    
    public function getType() {
      return $this->type;
    }
    
    public function getDate() {
      return $this->date;
    }
    
    public function getPlace() {
      return $this->place;
    }
    
    public function __toString() {
      $parts = array();
      if( $this->value ) {
        $parts[] = $this->value;
      }
      if( $this->date ) {
        $parts[] = $this->date;
      }
      if( $this->place ) {
        $parts[] = $this->place;
      }
      return implode(' in ', $parts);
    }
  
  }
  
?>