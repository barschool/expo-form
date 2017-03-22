<?php
$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/expo';
include_once('../config.php');
$config = unserialize(CONFIG);
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
      $('#generateLink').on('click', function(e){
        var baseUrl = 'http://ebs-platform.com/expo/v2/';
        var path = [];
        var $form = $('form');

        path.push( $form.find('#expoCity').val() );
        path.push( $form.find('#expoName').val() );
        path.push( $form.find('#expoManager').val() );
        path.push( $form.find('#market option:selected').val() );
        path.push( $form.find('#language option:selected').val() );
        path.push( $form.find('#startDate').val() );
        path.push( $form.find('#endDate').val() );

        var $container = $('#output');
        $container.show();
        $container.find('code.encoded').text( baseUrl + b64EncodeUnicode( path.join('/') ) );
        $container.find('code.plain').text( baseUrl + path.join('/') );
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