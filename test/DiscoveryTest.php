<?php

  include_once(dirname(__FILE__) . "/FSTest.php");
  
  class DiscoveryTest extends FSTest {
  
    public function testDiscoveryGet() {
      $discovery = self::$client->getDiscovery();
      $this->assertEquals($discovery['user-collections']->getHref(), TestConfig::$apiHost . '/platform/sources/collections');
    }
  
  }

?>