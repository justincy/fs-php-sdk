<?php

  namespace FS;
  
  /**
   * FamilySearch Person Class
   */
  class Person {
  
    private $_person;
    private $_uri;
    private $_places;
    private $_parents;
    private $_children;
    private $_spouses;
  
    /**
     * Constructor for the Person Class
     */
    public function __construct($personData) {
      // Save the person
      $this->_person = $personData['persons'][0];
      
      // Save the person uri
      // Because there isn't always a link in the person object to itself,
      // we remove the "/notes" ending of the notes uri.
      if( isset($this->_person['links']['person']['href']) ) {
        $this->_uri = $this->_person['links']['person']['href'];
      } else {
        $this->_uri = str_replace('/notes', '', $this->_person['links']['notes']['href']);
      }
      
      // Save associated places
      $this->_places = $personData['places'];
      
      // Process parentChildRelationships if they're available
      $this->_parents = array();
      $this->_children = array();
      if( isset($personData['parentChildRelationships']) ) {
        foreach( $personData['parentChildRelationships'] as $rel ) {
          // If this person is the child, store this in parent relationships
          if( $rel['child'] == $this->_uri ) {
            $this->_parents[] = $rel;
          }
          // Otherwise, store the relationship in the childrens array
          else {
            $this->_children[] = $rel;
          }
        }
      }
      
      // Process the spouse relationships
      $this->_spouses = array();
      if( isset($personData['relationships']) ) {
        foreach( $personData['relationships'] as $rel ) {
          // Set the spouse attribute to the url of whichever person is the spouse
          // This saves consumers from having to do it themselves
          if( $rel['person1'] == $this->_uri ) {
            $rel['spouse'] = $rel['person2'];
          } else {
            $rel['spouse'] = $rel['person1'];
          }
          $this->_spouses[] = $rel;
        }
      }
    }
    
    /**
     * Return the uri of this person object
     */
    public function getUri() {
      return $this->_uri;
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
    
    /**
     * Return a list of parent relationships
     */
    public function getParents() {
      return $this->_parents;
    }
    
    /**
     * Return a list of child relationships
     */
    public function getChildren() {
      return $this->_children;
    }
    
    /**
     * Return a list of spouse relationships
     */
    public function getSpouses() {
      return $this->_spouses;
    }
  
  }

?>