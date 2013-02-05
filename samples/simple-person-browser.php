<?php
  
  require_once 'guzzle.phar';
  require_once '/home/frontier/fs-php-sdk/src/FS/Bootstrap.php';
  
  session_start();
  
  $fs = new FS\Client('ABCD-EFGH-JKLM-NOPQ-RSTU-VWXY-0123-4567', 'sandbox');
  
  // If we receive a 401, logout
  $fs->getEventDispatcher()->addListener('request.error', function(Event $event) {
    if ($event['response']->getStatusCode() == 401) {
      unset($_SESSION['fs-session']);
      header('Location: ' . __FILE__);
      exit;
    }
  });
  
  // If we're returning from the oauth2 redirect, capture the code
  if( isset($_REQUEST['code']) ) {
    $_SESSION['fs-session'] = $fs->getOAuth2AccessToken($_REQUEST['code']);
  } 
  
  // Start a session if we haven't already
  else if( !isset($_SESSION['fs-session']) ) {
    $fs->startOAuth2Authorization('http://localhost/fs/test.php');
  }
  
  // If we reach here, it means we have a session
  // so we're going to give the access token to
  // the FS client so that API requests will be
  // authenticated.
  $fs->setAccessToken($_SESSION['fs-session']);
  
  // If a person was requested, fetch and display them
  if( isset($_REQUEST['person']) ) {
    $person = $fs->getPersonWithRelationships($_REQUEST['person']);
  } 
  
  // Otherwise, get and display the current person with their relationships
  else {
    $person = $fs->getCurrentUserPerson();
    $person = $fs->getPersonWithRelationships($person->getUri());
  }
  
  function person_link($personUri) {
    return '<a href="test.php?person=' . urlencode($personUri) . '">' . $personUri . '</a>';
  }

?>
<html>
<body>

<h1><? echo $person->getName(); ?></h1>
<div><label>Birth Date:</label> <? echo $person->getBirthDate(); ?></div>
<div><label>Birth Place:</label> <? echo $person->getBirthPlace(); ?></div>

<h2>Parents</h2>
<? foreach( $person->getParents() as $parents ) { ?>
<div class="parents-relationship">
  <div><label>Mother:</label> <? echo person_link($parents['mother']); ?></div>
  <div><label>Father:</label> <? echo person_link($parents['father']); ?></div>
</div>
<? } ?>

<h2>Spouses</h2>
<? foreach( $person->getSpouses() as $spouse ) { ?>
<div class="parents-relationship">
  <div><label>Spouse:</label> <? echo person_link($spouse['spouse']); ?></div>
</div>
<? } ?>

<h2>Children</h2>
<? foreach( $person->getChildren() as $child ) { ?>
<div class="parents-relationship">
  <div><label>Child:</label> <? echo person_link($child['child']); ?></div>
</div>
<? } ?>

</body>
</html>