<?php
  
  $dir = dirname(__FILE__) . '/';
  
  $files = array(
    'Enunciate-Models.php',
    'Person.php',
    'Name.php',
    'Fact.php',
    'Relationships.php',
    'Response.php',
    'AtomResponse.php',
    'Client.php'
  );
  
  foreach( $files as $f ) {
    include $dir . $f;
  }
  
?>