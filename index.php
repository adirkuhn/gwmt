<?php
require_once __DIR__ . "/bootstrap.php";

use Symfony\Component\Yaml\Yaml;

session_start();

$client_id = '233505435139-8coprarurh9s56ijp7rsml6jtcl8osuu.apps.googleusercontent.com';
$client_secret = 'sAoknq0OsOQez8qd1nxZ2APH';
echo $redirect_uri = "http://localhost:8080/";
$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setScopes(array('email', 'https://www.googleapis.com/auth/webmasters', 'https://www.googleapis.com/auth/webmasters.readonly'));
/************************************************
  If we're logging out we just need to clear our
  local access token in this case
 ************************************************/
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}
/************************************************
  If we have a code back from the OAuth 2.0 flow,
  we need to exchange that with the authenticate()
  function. We store the resultant access token
  bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  echo $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}
/************************************************
  If we have an access token, we can make
  requests, else we generate an authentication URL.
 ************************************************/
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $authUrl = $client->createAuthUrl();
}
/************************************************
  If we're signed in we can go ahead and retrieve
  the ID token, which is part of the bundle of
  data that is exchange in the authenticate step
  - we only need to do a network call if we have
  to retrieve the Google certificate to verify it,
  and that can be cached.
 ************************************************/
if ($client->getAccessToken()) {
  $_SESSION['access_token'] = $client->getAccessToken();
  $token_data = $client->verifyIdToken()->getAttributes();
}

if (strpos($client_id, "googleusercontent") == false) {
  echo missingClientSecretsWarning();
  exit;
}
?>
<div class="box">
  <div class="request">
<?php
if (isset($authUrl)) {
  echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
} else {
  echo "<a class='logout' href='?logout'>Logout</a>";


  $search_console = new Google_Service_Webmasters($client);

  $sites = $search_console->sites->listSites();

  $filters = new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
  $filters->setStartDate("2015-07-01");
  $filters->setEndDate("2015-07-27");
  $query_data = $search_console->searchanalytics->query("http://www.guardatudo.com.br/", $filters);

  echo "<pre>";
  var_dump($sites);
  var_dump($query_data);
}



?>
  </div>

  <div class="data">
<?php 
if (isset($token_data)) {
  var_dump($token_data);
}
?>
  </div>
</div>