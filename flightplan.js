var plan = require('flightplan');
var fs = require('fs');
var config = JSON.parse(fs.readFileSync('flightplan.config.json', 'utf8'));

// configuration
plan.target('prod', [
  {
    host: config.prod.host,
    username: config.prod.username,
    privateKey: config.prod.privateKey,
    agent: process.env.SSH_AUTH_SOCK
  }
]);

var deploy = true;

// run commands on localhost
plan.local(function(local) {
  var result = local.exec('git push https://'+ config.prod.token +'@github.com/barschool/expo-form.git master');
  if ( result.stderr.search(/Everything up-to-date/m) === 0 ){
    deploy = false;
    local.log('Remote already up-to-date..');
  }
});

// run commands on remote host
plan.remote(function(remote) {
  deploy = true;
  if(deploy){
    remote.log('Pulling master..');
    remote.exec('cd /var/www/html/expo && git pull origin master');
  } else {
    remote.log('Nothing to deploy..');
  }
});