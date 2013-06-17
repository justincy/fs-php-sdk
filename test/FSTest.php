<?php

  include dirname(__FILE__) . "/../lib/guzzle.phar";
  include dirname(__FILE__) . "/../lib/FS.phar";
  include dirname(__FILE__) . "/TestConfig.php";
  
  class FSTest extends PHPUnit_Framework_TestCase {
  
    protected static $client;
    
    public static function setUpBeforeClass() {
      self::$client = new FS\Client(null, 'mock-api', TestConfig::$apiHost);     
    }
  
  }

?>