<?php

function email_domain_exists($email, $record = 'MX'){
  list($user, $domain) = explode('@', $email);
  return checkdnsrr($domain, $record);
}

if(isset($_POST['email'])){
  if (email_domain_exists($_POST['email'])){
    echo "true";
  } else {
    echo "false";
  }
}

