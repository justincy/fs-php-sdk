<?php

  include_once(dirname(__FILE__) . '/FSTest.php');
  
  class UserTest extends FSTest {
  
    public function testCurrentUserGet() {
      $user = self::$client->getCurrentUser();
      
      $this->assertEquals('cis.MMM.RX9', $user->getId());
      $this->assertEquals('Pete Townsend', $user->getContactName());
      $this->assertEquals('PXRQ-FMXT', $user->getTreeUserId());
      $this->assertEquals('KYMF-G5T', $user->getPersonId());
    }
  
  }

?>