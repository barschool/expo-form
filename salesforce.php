<?php

  function RFC2822_email($email){
    $email_lower = strtolower($email);
    $pattern = "/(?:[a-z0-9!#$%&'*+\/\=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/\=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";
    preg_match($pattern, $email_lower, $matched_email);

    if (!$matched_email){
      return false;
    } else {
      return $matched_email[0];
    }
  }

  function salesforceAuthenticate(){
    require_once('./salesforce.config.php');

    $url = SF_INSTANCE_URL . "/services/oauth2/token";
    $ch = curl_init($url);

    $content = 	"grant_type=password" .
                "&client_id=" 		.	SF_CLIENT_ID .
                "&client_secret=" . SF_CLIENT_SECRET .
                "&username=" 			. SF_USER .
                "&password=" 			. SF_PASSWORD;

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, URLOPT_FRESH_CONNECT, 1);

    $data = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($data, true);
    return $data['access_token'];
  }

  function createLead(){

    $SfLanguages = array(
      'DE' => 'de',
      'DK' => 'da',
      'ES' => 'es',
      'FI' => 'fi',
      'FR' => 'fr',
      'IT' => 'it',
      'NL' => 'nl_NL',
      'NO' => 'no',
      'SE' => 'sv'
    );

    $site_market    = strtoupper($_POST['market']);
    $language       = isset($SfLanguages[ $site_market ]) ? $SfLanguages[ $site_market ] : 'en_US';
    $debug          = false;
    $email          = RFC2822_email($_POST['email']);

    $LeadData = array(
      'Email'               => $email,
      'FirstName'           => $_POST['firstname'],
      'LastName'            => $_POST['lastname'],
      'Phone'               => $_POST['itlPhoneFull'],
      'IP_Adress__c'        => $_SERVER['REMOTE_ADDR'],
      'Market_ISO_code__c'  => $site_market,
      'Language__c'         => $language,
      'Page_URL__c'         => $_SERVER['HTTP_REFERER'],
      'Timezone__c'         => $_POST['timezonediff'],
    );

    if ($_POST['expoCity'] === 'School Referral') {
      $LeadData['LeadSource'] = 'School Referral';
      $LeadData['Referring_School__c'] = $_POST['expoManager'];
      $LeadData['Page_title__c'] = $_POST['expoManager'];

    } else {
      $LeadData['LeadSource'] = 'Expos';
      $LeadData['UTM_Source__c'] = $_POST['utm_source'];
      $LeadData['Expo_City__c'] = ucwords($_POST['expoCity']);
      $LeadData['Expo_Manager__c'] = (!empty($_POST['expoManager'])) ? ucwords($_POST['expoManager']) : '';
      $LeadData['Expo_Name__c'] = ucwords($_POST['expoName']);
      $LeadData['Expo_date__c'] = $_POST['date'];
      if( !empty($_POST['schools']) ){
        foreach($_POST['schools'] as $key => $school){
          $LeadData['School_choice_' . ($key + 1) . '__c'] = $school;
        };
      };
      if( !empty($_POST['age']) ){
          $LeadData['Age_group__c'] = $_POST['age'][0];
      };
    };

    // debug
    //echo '<pre>'; print_r($LeadData); echo '</pre>';

    $LeadData = json_encode($LeadData);

    $access_token = salesforceAuthenticate();
    $url = SF_INSTANCE_URL . '/services/data/v20.0/sobjects/Lead/';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array(
      "Authorization: OAuth $access_token",
      "Content-type: application/json"
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $LeadData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    curl_setopt($ch, URLOPT_FRESH_CONNECT, 1);
    $json_response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($json_response, $header_size);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ( $status != 201 ) {
      $error['error'] = true;
      $error['response'] = $json_response;
      //return $error;
      //die("Error: call to URL $url failed with status $status, response $json_response");
      $logData = sprintf("\"%s\"|$LeadData|$body\n", date("Y-m-d H:i:s"));
      error_log($logData, 3, dirname(__file__) . '/log/expo.log');
      return false;
    } else {
      $response = json_decode($json_response, true);
      return $response["id"];
    };
  }
