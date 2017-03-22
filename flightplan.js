var plan = require('flightplan');

// configuration
plan.target('prod', [
  {
    host: 'ebs-platform.com',
    username: 'peter',
    privateKey: '/Users/Peter/.ssh/eval_rsa',
    agent: process.env.SSH_AUTH_SOCK
  }
]);

var deploy = true;

// run commands on localhost
plan.local(function(local) {
  var result = local.exec('git push https://24b756656f283fc956e7ad9008d4bcba38a57ec0@github.com/barschool/expo-form.git master');
  if ( result.stderr.search(/Everything up-to-date/m) === 0 ){
    deploy = false;
    local.log('Remote already up-to-date..');
  }
});

// run commands on remote host
plan.remote(function(remote) {
  if(deploy){
    remote.log('Pulling master..');
    remote.exec('git pull https://24b756656f283fc956e7ad9008d4bcba38a57ec0@github.com/barschool/expo-form.git');
  } else {
    remote.log('Nothing to deploy..');
  }
});