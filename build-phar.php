<?php
  
  $phar = new Phar('lib/FS.phar',0,'FS.phar');
  
  $stub = "<?php 
  Phar::mapPhar('FS.phar');
  include('phar://FS.phar/FS/Bootstrap.php');
  __HALT_COMPILER();";
  
  $phar->setStub($stub);
  
  $phar->buildFromDirectory(dirname(__FILE__) . '/src');
  
?>