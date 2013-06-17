<?php

  namespace FS;
  
  class Name extends \Org\Gedcomx\Conclusion\Name {
  
    public function __construct($data) {
      parent::__construct($data);
      
      // Convert the name forms
      foreach($this->nameForms as $i => $nameForm) {
        $this->nameForms[$i] = new NameForm($nameForm->toArray());
      }
    }
    
    public function getType() {
      return $this->type;
    }
  
    public function getFullText() {
      return $this->nameForms[0]->fullText;
    }
    
    public function getNameForms() {
      return $this->nameForms;
    }
    
    public function getNamePart($type) {
      return $this->nameForms[0]->getNamePart($type);
    }
    
    public function getGivenName() {
      return $this->getNamePart('http://gedcomx.org/Given');
    }
    
    public function getSurname() {
      return $this->getNamePart('http://gedcomx.org/Surname');
    }
    
    public function __toString() {
      return $this->getText();
    }
  
  }
  
  class NameForm extends \Org\Gedcomx\Conclusion\NameForm {
  
    public function __construct($data) {
      parent::__construct($data);
      
      // Convert name parts
      foreach($this->parts as $i => $namePart) {
        $this->parts[$i] = new NamePart($namePart->toArray());
      }
    }
    
    public function getNameParts() {
      return $this->parts;
    }
    
    public function getGivenName() {
      return $this->getNamePart('http://gedcomx.org/Given');
    }
    
    public function getSurname() {
      return $this->getNamePart('http://gedcomx.org/Surname');
    }
  
    public function getNamePart($type) {
      foreach($this->parts as $namePart) {
        if( $namePart->type == $type ) {
          return $namePart;
        }
      }
      return null;
    }
    
    public function getFullText() {
      return $this->fullText;
    }
  
  }
  
  class NamePart extends \Org\Gedcomx\Conclusion\NamePart {
  
    public function __toString() {
      return $this->value;
    }
  
  }
  
?>