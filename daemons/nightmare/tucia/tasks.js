var Tucia = require('./nightmare-tucia');

var loginInfo = {
    'username': 'billqiang@qq.com',
    'password': '394246577'
};

var totalPlayers = 11;
var content = '挺不错的。';
var status=1;

exports.task_commentPlayers = function(){
    return function(nightmare){
        nightmare
        .use( Tucia.login( loginInfo.username, loginInfo.password )  )
        .use( Tucia.commentPlayers(content, totalPlayers) )
        .wait();
    }    
}

exports.task_likePlayers = function(){
    return function(nightmare){
        nightmare
        .use( Tucia.login( loginInfo.username, loginInfo.password )  )
        .use( Tucia.likePlayers( status, totalPlayers ) )
        .wait();
    }    
}


