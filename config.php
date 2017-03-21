<?php

$config['markets'] = array(
  'en' => 'Europe/London',
  'se' => 'Europe/Stockholm',
  'no' => 'Europe/Stockholm',
  'dk' => 'Europe/Stockholm',
  'fi' => 'Europe/Stockholm',
  'ie' => 'Europe/Dublin',
  'de' => 'Europe/Stockholm',
  'nl' => 'Europe/Stockholm',
  'es' => 'Europe/Madrid',
  'fr' => 'Europe/Paris',
  'it' => 'Europe/Rome',
  'au' => 'Europe/Sidney',
  'in' => 'Asia/Karachi',
  'us' => 'America/New_York'
);

$config['SupportedLanguages'] = array(
  'sv' => 'sv_SE',
  'no' => 'nb_NO',
  'da' => 'da_DK',
  'fi' => 'fi_FI',
  'en' => 'en_US',
  'de' => 'de_DE',
  'es' => 'es_ES',
  'it' => 'it_IT',
  'fr' => 'fr_FR'
);

$config['Schools'] = array(
  'Cape Town',
  'Rome',
  'Madrid',
  'Mallorca',
  'Miami',
  'Las Vegas',
  'St. Martin',
  'Berlin',
  'Paris',
  'Milan',
  'New York',
  'Manchester',
  'Amsterdam',
  'Helsinki',
  'Copenhagen',
  'Kos',
  'Dublin',
  'London',
  'Sydney',
  'Phuket',
  'Barcelona',
  'Oslo',
  'Stockholm',
);

$config['AgeGroups'] = array(
  'Under 18',
  '18-21',
  '22-24',
  '25-27',
  '28+',
);

define ("CONFIG", serialize($config));