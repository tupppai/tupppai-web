var Nightmare = require('nightmare');
var Tucia = require('./nightmare-tucia');

var loginInfo = {
    'username': 'billqiang@qq.com',
    'password': '394246577'
};

var totalPlayers = 11;
var content = '挺不错的。';

new Nightmare()
    .use( Tucia.login( loginInfo.username, loginInfo.password )  )
    .use( Tucia.commentPlayers(content, totalPlayers) )
    //.use( Tucia.comment( uid, content ) )
    .run();

