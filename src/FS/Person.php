<?php

  namespace FS;
  
  /**
   * FamilySearch Person Class
   */
  class Person {
  
    private $_person;
    private $_places;
  
    /**
     * Constructor for the Person Class
     */
    public function __construct($personData) {
      $this->_person = $personData['persons'][0];
      $this->_places = $personData['places'];
    }
    
    /**
     * Get the display name of the person
     */
    public function getName() {
      return getAttrOrNull($this->_person, array('display', 'name'));
    }
    
    /**
     * Get the gender of the person
     */
    public function getGender() {
      return getAttrOrNull($this->_person, array('display','gender'));
    }
    
    /**
     * Get the birth info of the person
     */
    public function getBirth() {
      return array(
        'birthDate' => $this->getBirthDate(),
        'birthPlace' => $this->getBirthPlace()
      );
    }
    
    /**
     * Get the birth place of the person
     */
    public function getBirthPlace() {
      return getAttrOrNull($this->_person, array('display', 'birthPlace'));
    }
    
    /**
     * Get the birth date of the person
     */
    public function getBirthDate() {
      return getAttrOrNull($this->_person, array('display', 'birthDate'));
    }
  
  }

?>