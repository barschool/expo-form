<?php
$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/expo';
include_once('../config.php');
$config = unserialize(CONFIG);
/* exit(var_dump($config['Schools'])); */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>New Expoform</title>
  <script src='<?php echo "$url/assets/js/vendor/jquery-1.11.1.min.js"; ?>'></script>
  <link rel="stylesheet" type="text/css" href='<?php echo "$url/assets/css/bootstrap.min.css"; ?>'>
  <script type="text/javascript" src='<?php echo "$url/assets/js/vendor/bootstrap.js"; ?>'></script>
  <style>
    .container.new-form{
      margin-top: 20px;
      max:width: 800px;
    }
    h2{
      margin-bottom: 20px;
      font-size: 30px;
    }
  </style>

  <script>
    function b64EncodeUnicode(str) {
      return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
        return String.fromCharCode('0x' + p1);
      }));
    };

    $(document).ready(function(){

      $('#destinations').on('click', function(e){
        console.log($('#destination').val());
        if( $('#destinations').is(":checked") ){
          $('#destinationContainer').hide();
        } else {
          $('#destinationContainer').show();
        }
      });
      
      $('#generateLink').on('click', function(e){
        var baseUrl = 'http://ebs-platform.com/expo/v2/';
        var path = [];
        var $form = $('form');
        var $container = $('#output');
        var missing = [];
        var showDestinations = $('#destinations').is(":checked") ? 1 : 0;

        var expoCity = $form.find('#expoCity').val();
        var expoName = $form.find('#expoName').val();
        var expoManager = $form.find('#expoManager').val();

        if(!expoName) missing.push('Expo Name');
        if(!expoCity) missing.push('Expo City');
        if(!expoManager) missing.push('Expo Manager');

        $container.show();

        if(missing.length){
          $container.find('code.plain').text( 'Required fields missing: ' + missing.join(', '));
        } else {
          path.push( expoCity );
          path.push( expoName );
          path.push( expoManager );
          path.push( $form.find('#market option:selected').val() );
          path.push( $form.find('#language option:selected').val() );
          path.push( $form.find('#startDate').val() );
          path.push( $form.find('#endDate').val() );
          path.push( $form.find('#endDate').val() );
          path.push( showDestinations );

          if(showDestinations == 0){
            path.push( $('#destination').val() );
          }

          $container.find('code.encoded').text( baseUrl + b64EncodeUnicode( path.join('/') ) );
          $container.find('code.plain').text( baseUrl + path.join('/') );
        }
        return false;
      });
    });
  </script>
</head>
<body>

<?php if(empty($_POST['submit'])): ?>

<div class="container new-form">
  <h2>Generate Expo form</h2>
  <form>
    <div class="form-group row">
      <label for="expoCity" class="col-sm-2 col-form-label">Expo City:</label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="expoCity" placeholder="New York">
      </div>
    </div>
    <div class="form-group row">
      <label for="expoName" class="col-sm-2 col-form-label">Expo Name:</label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="expoName" placeholder="National Career Fair Manhattan">
      </div>
    </div>
    <div class="form-group row">
      <label for="expoManager" class="col-sm-2 col-form-label">Expo Manager:</label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="expoManager" placeholder="Ray Berry">
      </div>
    </div>
    <div class="form-group row">
      <label for="market" class="col-sm-2 col-form-label">ISO Market:</label>
      <div class="col-sm-6">
        <select class="form-control" id="market">
          <?php foreach( $config['markets'] as $key => $market): ?>
            <option><?php echo $key ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group row">
      <label for="market" class="col-sm-2 col-form-label">Language:</label>
      <div class="col-sm-6">
        <select class="form-control" id="language">
          <?php foreach( $config['SupportedLanguages'] as $key => $language): ?>
            <?php if($key == 'en'): ?>
            <option value="<?php echo $key ?>" selected><?php echo $key ?></option>
            <?php else: ?>
              <option value="<?php echo $key ?>"><?php echo $key ?></option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group row">
      <label for="startDate" class="col-sm-2 col-form-label">Start Date:</label>
      <div class="col-sm-6">
        <input class="form-control" type="date" value="<?php echo date('Y-m-d') ?>" id="startDate">
      </div>
    </div>
    <div class="form-group row">
      <label for="endDate" class="col-sm-2 col-form-label">Start Date:</label>
      <div class="col-sm-6">
        <input class="form-control" type="date" value="<?php echo date('Y-m-d') ?>" id="endDate">
      </div>
    </div>

    <div class="form-group row">
      <label for="destinations" class="col-sm-2 col-form-label">Fix School?</label>
      <div class="col-sm-1">
        <div class="checkbox">
          <label><input checked id="destinations" type="checkbox" value="">No</label>
        </div>
      </div>
      <div hidden class="col-sm-5" id="destinationContainer">
        <select  class="form-control" id="destination">
          <?php foreach( $config['Schools'] as $key => $destination): ?>
            <option value="<?php echo $key ?>"><?php echo $destination ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-sm-4 col-sm-offset-2">
        <button id="generateLink" class="btn btn-primary">Generate link!</button>
      </div>
    </div>
  </form>
  <div id="output" class="col-sm-10" style="display: none">
    <h3>Link:</h3>
    <pre><code class="encoded"></code></pre>

    <p>Decoded link: (only for reference, this wont work!)</p>
    <pre><code class="plain"></code></pre>
  </div>
</div>
<?php else: ?>

<?php endif; ?>
  
</body>
</html>