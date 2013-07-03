<?php

  include_once(dirname(__FILE__) . '/FSTest.php');
  
  class PersonWithRelationships extends FSTest {
  
    public function testPersonWithRelationshipsGet() {
      $personId = 'PW8J-GZ0';
      $response = self::$client->getPersonWithRelationships($personId);
      
      //
      // Person
      //
      $person = $response->getPerson();
      $this->assertEquals($personId, $person->getId());
      $this->assertEquals($person->getGender()->getType(), 'http://gedcomx.org/Male');
      $this->assertEquals($person->getNames()[0]->getNameForms()[0]->getFullText(), 'Daniel Earl Bishop');
      
      //
      // Couple relationships
      //
      $this->assertCount(1, $response->getRelationships());
      
      $coupleRel = $response->getRelationships()[0];
      $this->assertEquals($coupleRel->getId(), 'C123-ABC');
      $this->assertEquals($coupleRel->getPerson1()->getResourceId(), 'PW8J-GZ0');
      $this->assertEquals($coupleRel->getPerson2()->getResourceId(), 'PA65-HG3');
      
      //
      // Parent-child relationships
      //
      $childRels = $response->getChildAndParentsRelationships();
      $this->assertCount(2, $childRels);
      
      if($childRels[0]->getId() == 'PPPX-PP0') {
        $childRel1 = $childRels[0];
        $childRel2 = $childRels[1];
      } else {
        $childRel1 = $childRels[1];
        $childRel2 = $childRels[0];
      }
      
      // First child relationship
      $this->assertEquals($childRel1->getId(), 'PPPX-PP0');
      $this->assertEquals($childRel1->getChild()->getResourceId(), 'PW8J-GZ0');
      $this->assertEquals($childRel1->getFather()->getResourceId(), 'PW8J-GZ1');
      $this->assertEquals($childRel1->getMother()->getResourceId(), 'PW8J-GZ2');
      
      $fatherFacts1 = $childRel1->getFatherFacts();
      $this->assertCount(1, $fatherFacts1);
      $this->assertEquals($fatherFacts1[0]->getType(), 'http://gedcomx.org/AdoptiveParent');
      $this->assertEquals($fatherFacts1[0]->getId(), 'C.1');
      
      $motherFacts1 = $childRel1->getMotherFacts();
      $this->assertCount(1, $motherFacts1);
      $this->assertEquals($motherFacts1[0]->getType(), 'http://gedcomx.org/BiologicalParent');
      $this->assertEquals($motherFacts1[0]->getId(), 'C.2');
      
      // Second child relationship
      $this->assertEquals($childRel2->getId(), 'PPPX-PP1');
      $this->assertEquals($childRel2->getChild()->getResourceId(), 'PS78-GH4');
      $this->assertEquals($childRel2->getFather()->getResourceId(), 'PW8J-GZ0');
      
      $fatherFacts2 = $childRel2->getFatherFacts();
      $this->assertCount(1, $fatherFacts2);
      $this->assertEquals($fatherFacts2[0]->getType(), 'http://gedcomx.org/AdoptiveParent');
      $this->assertEquals($fatherFacts2[0]->getId(), 'C.1');
      
    }
  
  }

?>