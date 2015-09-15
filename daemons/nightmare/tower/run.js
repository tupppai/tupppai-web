var Nightmare   = require('nightmare');
var Tower       = require('./tower');

var loginInfo = {
    'username': '394246577@qq.com',
    'password': '394246577'
};

var nightmare = new Nightmare();

nightmare.use( Tower.login( loginInfo.username, loginInfo.password )  )
    .use( Tower.events() )
    .screenshot('/var/www/ps/public/tower.jpg')
    .run(function(err, nightmare) {
        console.log('done');
    });
