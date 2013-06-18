<?php

  namespace FS;

  class Fact extends \Org\Gedcomx\Conclusion\Fact {
  
    public function __construct($data, $response) {
    
      parent::__construct($data);
      
      // Convert date
      if( $this->getDate() ) {
        $this->setDate(new Date($this->getDate()->toArray()));
      }
      
      
      if( $this->getPlace() ) {
      
        // If a place reference exists, remove the # and use the 
        // place reference to lookup the place value from the response
        if( $this->getPlace()->getDescriptionRef() ) {
          $placeId = substr($this->getPlace()->getDescriptionRef(), 1);
          foreach($response->getPlaces() as $place) {
            if( $place->getId() == $placeId ) {
              $this->setPlace(new Place($place->toArray()));
            }
          }
        }
        
        // If the place ref doesn't exist, take the original value
        // and convert it into a place
        else {
          $this->setPlace(new Place( array( 'names' => array( array( 'value' => $this->getPlace()->getOriginal())))));
        }
        
      }
      
    }
    
    public function __toString() {
      $parts = array();
      if( $this->getValue() ) {
        $parts[] = $this->getValue();
      }
      if( $this->getDate() ) {
        $parts[] = $this->getDate();
      }
      if( $this->getPlace() ) {
        $parts[] = $this->getPlace();
      }
      return implode(' in ', $parts);
    }
  
  }
  
?>