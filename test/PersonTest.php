<?php

  include_once(dirname(__FILE__) . "/FSTest.php");
  
  class PersonTest extends FSTest {
  
    public function testPersonGet() {
      $id = "PPPP-200";
      $response = self::$client->getPerson($id);
      $person = $response->getPerson();
            
      $this->assertEquals($id, $person->id);
      
      //
      // Names
      //
      
      $preferredName = $person->getPreferredName();
      $preferredNameForms = $preferredName->getNameForms();
      $nameForm0 = $preferredNameForms[0];
      $nameForm1 = $preferredNameForms[1];
      
      $this->assertCount(1, $person->getNames());
      $this->assertEquals('http://gedcomx.org/BirthName', $preferredName->getType());
      $this->assertCount(2, $preferredNameForms);
      
      // First name form
      $this->assertEquals('Anastasia Aleksandrova', $nameForm0->getFullText());
      $this->assertCount(2, $nameForm0->getNameParts());
      $this->assertEquals('Anastasia', $nameForm0->getGivenName());
      $this->assertEquals('Aleksandrova', $nameForm0->getSurname());
      
      /* UTF-8 Problems
      $this->assertEquals('Анастасия Александрова', $nameForm1->getFullText());
      $this->assertCount(2, $nameForm1->getNameParts());
      $this->assertEquals('Анастасия', $nameForm1->getGivenName());
      $this->assertEquals('Александрова', $nameForm1->getSurname());
      */
      
      // TODO: Test attribution and ID of name
      
      //
      // Facts
      //
      
      // Birth
      $birth = $person->getBirth();
      $birthDate = $birth->getDate();
      $birthPlace = $birth->getPlace();
      $this->assertEquals('http://gedcomx.org/Birth', $birth->getType());
      $this->assertEquals('3 Apr 1836', $birthDate->getOriginal());
      $this->assertEquals('Moscow, Russia', $birthPlace->getValue());
      // TODO: Test attribution and ID
      
      // Adoption
      $adoptionFacts = $person->getFacts('http://gedcomx.org/Adoption');
      $this->assertCount(1, $adoptionFacts);
      $adoption = $adoptionFacts[0];
      $adoptionDate = $adoption->getDate();
      $adoptionPlace = $adoption->getPlace();
      $this->assertEquals('http://gedcomx.org/Adoption', $adoption->getType());
      $this->assertEquals('13 Apr 1836', $adoptionDate->getOriginal());
      $this->assertEquals('Moskva, Moscow, Russia', $adoptionPlace->getValue());
      
    }
  
  }

?>