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
      $this->_person = $personData['persons'];
      $this->_places = $personData['places'];
    }
    
    /**
     * Get the display name of the person
     */
    public function getName() {
      return $this->_person['display']['name'];
    }
    
    /**
     * Get the gender of the person
     */
    public function getGender() {
      return $this->_person['display']['gender'];
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
      return FS\getAttrOrNull( $this->_person, array('display', 'birthPlace') );
    }
    
    /**
     * Get the birth date of the person
     */
    public function getBirthDate() {
      return FS\getAttrOrNull( $this->_person, array('display', 'birthDate') );
    }
  
  }

?>