<?php
  // Submit handler
  if (isset($_POST["submit"])) {
    include_once('./salesforce.php');
    $result = createLead();
  };

  $vars = explode('/',$_GET['q']);

  $expo_name  = $vars[0];
  $title      = $vars[1];
  $market     = $vars[2];
  $language   = $vars[3];

  $markets = array(
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
    'us' => 'America/New_York '
  );

  if (!isset($_POST['submit'])){
    if (!$markets[$market]){
      exit( sprintf("Unsupported market, supported values: %s.", implode(', ', array_keys($markets))) );
    } else {
      date_default_timezone_set( $markets[$market] );
      $from_date = DateTime::createFromFormat('j-M-Y', $vars[4]);
      $to_date = DateTime::createFromFormat('j-M-Y', $vars[5]);
      $today = new DateTime();

      if ( !$from_date || !$to_date ){
        exit('Invalid date(s) supplied, expected format: 13-Nov-2016.');
      } elseif ( !($today >= $from_date && $today <= $to_date) ) {
        exit('This event is not currently active');
      };
    };
  };

  // I18N support
  $SupportedLanguages = array(
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

  $language = array_key_exists($language, $SupportedLanguages) ? $SupportedLanguages[$language] : 'en_US';
  $language = "$language.UTF-8";
  putenv("LANG=" . $language);
  setlocale(LC_ALL, $language);
  $domain = "messages";
  bindtextdomain($domain, "Locale");
  bind_textdomain_codeset($domain, 'UTF-8');
  textdomain($domain);
  // Have to use absolute url because of the rewriting in .htaccess
  $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/expo';

  // Helper to clean template
  function _e($text){
    echo _($text);
  };

  $Schools = array(
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

  $AgeGroups = array(
    'Under 18',
    '18-21',
    '22-24',
    '25-27',
    '28+',
  );
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $title; ?></title>

    <?php if ( isset($_POST['submit']) ): ?>
    <meta http-equiv="refresh" content="3;URL='<?php echo $_SERVER['HTTP_REFERER'] ?>'" />
    <?php else: ?>
      <script src='<?php echo "$url/assets/js/vendor/jquery-1.11.1.min.js"; ?>'></script>
      <script src='<?php echo "$url/assets/js/vendor/jquery.validate.min.js"; ?>'></script>
      <script src='<?php echo "$url/assets/js/vendor/additional-methods.min.js"; ?>'>></script>
      <link rel="stylesheet" type="text/css" href='<?php echo "$url/assets/css/intlTelInput.css"; ?>'>
      <link rel="stylesheet" type="text/css" href='<?php echo "$url/assets/css/bootstrap.min.css"; ?>'>
      <link rel="stylesheet" type="text/css" href='<?php echo "$url/assets/css/style.css"; ?>'>
      <script type="text/javascript" src='<?php echo "$url/assets/js/site.js"; ?>'></script>
      <script type="text/javascript" src='<?php echo "$url/assets/js/vendor/intlTelInput.js"; ?>'></script>
      <script type="text/javascript" src='<?php echo "$url/assets/js/vendor/utils.js"; ?>'></script>
      <script type="text/javascript" src='<?php echo "$url/assets/js/vendor/bootstrap.js"; ?>'></script>
    <?php endif ?>

    <link rel="stylesheet" type="text/css" href='<?php echo "$url/assets/css/style.css"; ?>'>

    <!-- Dynamic styles -->
    <style>
      html {
        background: url(<?php echo "$url/assets/img/IMG_3648.jpg" ?>) no-repeat center center fixed;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
      }
      .iti-flag {
        background-image: url('<?php echo "$url/assets/img/flags.png"; ?>') !important;
      }
      @media only screen and (-webkit-min-device-pixel-ratio: 2),
      only screen and (min--moz-device-pixel-ratio: 2),
      only screen and (-o-min-device-pixel-ratio: 2 / 1),
      only screen and (min-device-pixel-ratio: 2),
      only screen and (min-resolution: 192dpi),
      only screen and (min-resolution: 2dppx) {
        .iti-flag {
          background-image: url('<?php echo "$url/assets/img/flags@2x.png"; ?>') !important;
        }
      }
    </style>
  </head>

  <?php $small = ($expo_name == 'School Referral');  ?>
  <body>
    <div id="page-wrap" <?php echo ($expo_name == 'School Referral') ? 'class="small"' : ''; ?>>
      <img class="logo" src=<?php echo "$url/assets/img/ebs_logo.jpg" ?>>
        <?php if ( !isset($_POST['submit']) ): ?>
        <form accept-charset="UTF-8" action='<?php echo $_SERVER['PHP_SELF']; ?>' enctype="multipart/form-data" id="form" method="POST">
          <div class="container">
            <div class="row">
              <div class="form-field <?php echo ($small) ? 'col-xs-12' : 'col-xs-6 col-xs-offset-3'; ?>">
                <input type="text" id="firstname" name="firstname" placeholder="<?php _e('First Name'); ?>"
                  data-msg="<?php _e('Please enter your first name'); ?>" data-toggle="popover"/>
              </div>
              <div class="form-field <?php echo ($small) ? 'col-xs-12' : 'col-xs-6 col-xs-offset-3'; ?>">
                <input type="text" class="hs-input" id="lastname" name="lastname" placeholder="<?php _e('Last Name'); ?>"
                  data-msg="<?php _e('Please enter your last name'); ?>" data-toggle="popover"/>
              </div>
              <div class="form-field <?php echo ($small) ? 'col-xs-12' : 'col-xs-6 col-xs-offset-3'; ?>">
                <input type="email" class="hs-input" type="text" id="email" name="email" placeholder="<?php _e('Email'); ?>"
                  data-msg-required="<?php _e('Please enter your email'); ?>"
                  data-msg-format="<?php _e('Please enter a valid email'); ?>"
                  data-msg-domain="<?php _e("Email domain don't exist"); ?>" data-toggle="popover"/>
              </div>
              <div class="form-field <?php echo ($small) ? 'col-xs-12' : 'col-xs-6 col-xs-offset-3'; ?>">
                <input type="hidden" id="itlPhoneFull" class="itlPhoneFull brochure" name="itlPhoneFull" style="display: none"
                  data-msg-required="<?php _e('Please enter your phonenumber'); ?>"
                  data-msg-valid="<?php _e('Please enter a valid phonenumber'); ?>" data-toggle="popover"/>
                <?php /* <p name="itl-phone" class="jsonly"><?php _e('Search the list by typing your country name, or enter your country code to select country'); ?></p> */ ?>
                <input type="tel" name="itl-phone" id="itl-phone" class="itl-phone jsonly">
              </div>
          </div>
          <?php if ( $expo_name != 'School Referral'): ?>
          <div class="container multiselect">
            <h4><?php _e('Your 3 favorite destinations...'); ?></h4>
            <div class="row row-centered">
              <div class="form-group">
                  <div class="items-collection">

                    <?php foreach ($Schools as $school): ?>
                    <div class="items col-xs-2 col-centered">
                        <div class="info-block block-info clearfix">
                            <div data-toggle="buttons" class="btn-group multiselect">
                                <label class="btn btn-default destination">
                                    <div class="itemcontent">
                                        <input type="checkbox" name="schools[]" autocomplete="off" value="<?php echo $school; ?>">
                                        <h5><?php echo $school; ?></h5>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                  </div>
              </div>
            </div>
          </div>
          <div class="container multiselect">
            <h4><?php _e('Your Age...'); ?></h4>
            <div class="row row-centered">
              <div class="form-group">
                  <div class="items-collection">

                    <?php foreach ($AgeGroups as $ageGroup): ?>
                    <div class="items col-xs-2 col-centered">
                        <div class="info-block block-info clearfix">
                            <div data-toggle="buttons" class="btn-group multiselect">
                                <label class="btn btn-default age">
                                    <div class="itemcontent">
                                        <input type="checkbox" name="age[]" autocomplete="off" value="<?php echo $ageGroup; ?>">
                                        <h5><?php echo $ageGroup; ?></h5>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                  </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <input type="hidden" name="option_market" value="<?php echo $market; ?>">
          <input type="hidden" name="title" value="<?php echo $title; ?>">
          <input type="hidden" name="utm_source" value="Expo">
          <input type="hidden" name="utm_medium" value="<?php echo $expo_name; ?>">
          <input type="hidden" name="timezonediff" value="<?php echo $tz; ?>">
          <input type="hidden" name="geoip" id="geoip" value="">
          <input type="hidden" name="date" value="<?php echo date_format($from_date, 'Y-m-d'); ?>">
          <input type="submit" name="submit" <?php echo ($small) ? 'class="small"' : ''; ?>" value="<?php _e('Submit'); ?>">
        </form>
        <?php else: ?>
          <h2><?php _e('Thank you!'); ?></h2>
          <h4><?php _e('We have sent more info to your email.'); ?></h4>
        <?php endif; ?>

    </div>
  </body>

</html>
