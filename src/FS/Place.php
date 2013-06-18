<?php

  namespace FS;

  class Place extends \Org\Gedcomx\Conclusion\PlaceDescription {
  
    public function __toString() {
      return $this->getValue();
    }
    
    public function getValue() {
      return $this->getNames()[0]->getValue();
    }
  
  }

?>