<?php

  namespace FS;
  
  class Name extends \Org\Gedcomx\Conclusion\Name {
  
    public function __construct($data) {
      parent::__construct($data);
      
      // Convert the name forms
      $nameForms = $this->getNameForms();
      foreach($nameForms as $i => $nameForm) {
        $nameForms[$i] = new NameForm($nameForm->toArray());
      }
      $this->setNameForms($nameForms);
    }
  
    public function getFullText() {
      return $this->getNameForms()[0]->getFullText();
    }
    
    public function getNamePart($type) {
      return $this->getNameForms()[0]->getNamePart($type);
    }
    
    public function getGivenName() {
      return $this->getNamePart('http://gedcomx.org/Given');
    }
    
    public function getSurname() {
      return $this->getNamePart('http://gedcomx.org/Surname');
    }
    
    public function __toString() {
      return $this->getFullText();
    }
  
  }
  
  class NameForm extends \Org\Gedcomx\Conclusion\NameForm {
  
    public function __construct($data) {
      parent::__construct($data);
      
      // Convert name parts
      $parts = $this->getParts();
      foreach($parts as $i => $namePart) {
        $parts[$i] = new NamePart($namePart->toArray());
      }
      $this->setParts($parts);
    }
    
    public function getGivenName() {
      return $this->getNamePart('http://gedcomx.org/Given');
    }
    
    public function getSurname() {
      return $this->getNamePart('http://gedcomx.org/Surname');
    }
  
    public function getNamePart($type) {
      foreach($this->getParts() as $namePart) {
        if( $namePart->getType() == $type ) {
          return $namePart;
        }
      }
      return null;
    }
  
  }
  
  class NamePart extends \Org\Gedcomx\Conclusion\NamePart {
  
    public function __toString() {
      return $this->getValue();
    }
  
  }
  
?>