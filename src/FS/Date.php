<?php

  namespace FS;

  class Date extends \Org\Gedcomx\Conclusion\DateInfo {
  
    public function getOriginal() {
      return $this->original;
    }
    
    public function getNormalized() {
      return $this->normalizedExtensions ? $this->normalizedExtensions[0]->value : null;
    }
    
    public function __toString() {
      return $this->getNormalized() ? $this->getNormalized() : $this->getOriginal();
    }
  
  }

?>