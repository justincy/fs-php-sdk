<?php

  include dirname(__FILE__) . "/../lib/guzzle.phar";
  include dirname(__FILE__) . "/../lib/FS.phar";
  include dirname(__FILE__) . "/TestConfig.php";
  
  class PersonTest extends PHPUnit_Framework_TestCase {
  
    protected static $client;
    
    public static function setUpBeforeClass() {
      self::$client = new FS\Client(null, 'mock-api', TestConfig::$apiHost);     
    }
  
    public function testDiscoveryGet() {
      $discovery = self::$client->getDiscovery();
      $this->assertEquals($discovery['user-collections']->href, TestConfig::$apiHost . '/platform/sources/collections');
    }
  
  }

?>