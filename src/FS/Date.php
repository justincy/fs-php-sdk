<?php

  namespace FS;

  class Date extends \Org\Gedcomx\Conclusion\DateInfo {
      
    public function getNormalized() {
      return $this->getNormalizedExtensions() ? $this->getNormalizedExtensions()[0]->getValue() : null;
    }
    
    public function __toString() {
      return $this->getNormalized() ? $this->getNormalized() : $this->getOriginal();
    }
  
  }

?>