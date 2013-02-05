<?php
  
  $dir = dirname(__FILE__) . '/';
  
  $files = array(
    'FS.php',
    'Person.php',
    'Utils.php'
  );
  
  foreach( $files as $f ) {
    include $dir . $f;
  }
  
?>