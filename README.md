# FamilySearch PHP SDK

This is a PHP SDK for the [FamilySearch APIs](https://familysearch.org/developers).

View the [sample code](https://github.com/justincy/php-sample-person-browser) that has been deployed to http://fs-php-sample-app.aws.af.cm/.

## Before Using the SDK

Visit https://familysearch.org/developers to sign up and gain access to the development environment before you can begin using the APIs.

## Requirements

1. PHP 5.3.2+ compiled with the cURL extension
2. A recent version of cURL 7.16.2+ compiled with OpenSSL and zlib
3. Install [Guzzle](http://guzzlephp.org)

## Usage

```php
<?php

  // Include Guzzle and the FS SDK
  require 'guzzle.phar';
  require 'FS.phar';
  
  // Instantiate an FS client
  $fs = new FS\Client('YOUR-DEV-KEY', 'sandbox');
  
  // If we're returning from the oauth2 redirect, capture the code
  if( isset($_REQUEST['code']) ) {
    $_SESSION['fs-session'] = $fs->getOAuth2AccessToken($_REQUEST['code']);
    // Reload the page without the oauth2 parameters
    header('Location: index.php');
    exit;
  } 
  
  // Start a session if we haven't already
  else if( !isset($_SESSION['fs-session']) ) {
    $fs->startOAuth2Authorization($OAUTH2_REDIRECT_URI);
  }
  
  // If we reach here, it means we have a session
  // so we're going to give the access token to
  // the FS client so that API requests will be
  // authenticated.
  $fs->setAccessToken($_SESSION['fs-session']);
  
  // Get the current user person
  $response = $fs->getCurrentUserPerson();
  $person = $response->getPerson();
  
  // Read and display the person's name and birth information
  echo $person['display']['name'];
  echo $person['display']['birthDate'];
  echo $person['display']['birthPlace'];

?>
```