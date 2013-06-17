<?php

  include_once(dirname(__FILE__) . "/FSTest.php");
  
  class PersonTest extends FSTest {
  
    public function testDiscoveryGet() {
      $discovery = self::$client->getDiscovery();
      $this->assertEquals($discovery['user-collections']->href, TestConfig::$apiHost . '/platform/sources/collections');
    }
  
  }

?>