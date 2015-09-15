 var BASE_HOST = 'http://www.tucia.net';
 var PLAYERS_PER_PAGE = 10;
 var urls = {
     'login'  : BASE_HOST,
     'player' : BASE_HOST + '/player/artists',
     'player_page': '/page',
     'player_home' : BASE_HOST + '/home'
 };  
 var consts = {
     'viewport':{
         'width': 1600,
         'height': 1200
     }    
 }       
         
 var elements = { 
     'login': {
         'usernameBox': 'input[name="login_username"]',
         'passwordBox': 'input[name="login_password"]',
         'submitBtn'  : 'input[value="登录"]',
         'popup': '#popwarnbox'
     },  
     'user':{
         'userHint': '.top_user_hint'
     },
     'message':{
         'contentBox': '#new_comment',
         'comments': '.commentbox'
     },
     'player':{
         'followBtn': '.followUserBtn',
         'unfollowBtn': '.unfollowUserBtn'
     }
 };
 
 

var cmntCount = 0;
var likeCount = 0;
 var playerIds = [];
 var jj = function( a ){return '/var/www/ps/public/'+a+'.jpg'};
         
         
 //account
 exports.login = function( username, password ){
    return function( nightmare ){
         nightmare
         .viewport( consts.viewport.width, consts.viewport.height )
         .goto( urls.login )
         .wait()
         .type( elements.login.usernameBox, username )
         .type( elements.login.passwordBox, password )
         .click( elements.login.submitBtn )
         .wait()
         .exists( elements.user.userHint, function( has ){ has && console.log('login successful') })
         .visible( elements.login.popup, function( has ){ has && console.log('login failed') });
     }   
 };      


 //comment single user
 var comment = exports.comment = function( nightmare, uid, content ){
     var HOME_BASE = urls.player_home + '/' + uid ;
     var player_urls = {
         'message': HOME_BASE + '/message',
         'follwing': HOME_BASE + '/following',
         'pending': HOME_BASE + '/pending',
         'shots': HOME_BASE + '/shots'
     };
 
     var firstComment = elements.message.comments + ':first-child';
 
 
     nightmare
         .viewport( consts.viewport.width, consts.viewport.height )
         .goto( player_urls.message )
         .wait()
         .type( elements.message.contentBox, content )
         .click( elements.message.submitBtn  )
         .wait()
         .evaluate(function ( element ) {
             var commentBox =  $( element ).find( 'div.span11 p:nth-child(2)' );
             commentBox.find('*:not(br)').remove();
             text = commentBox.text();
             return text;
         }, function ( content ) {
             console.log( uid+', content='+content );
         }, firstComment )
         .wait();
 };

exports.commentPlayers = function( content, total ){
    return function( nightmare ){
        cmntPlayers( nightmare, content, total );
    }
}

var cmntPlayers = function( nightmare, content, total ){
    var page = Math.floor( cmntCount / PLAYERS_PER_PAGE ) + 1;
        nightmare
        .viewport( consts.viewport.width, consts.viewport.height ) 
        .goto( urls.player + urls.player_page + page )
        .wait()
        .evaluate( function( elements ){
            var players = [];
            var ele = [ elements.player.followBtn, elements.player.unfollowBtn ].join(',');
            $( ele ).each( function(){
                players.push( this.getAttribute('rel') );
            });
            return players;
        }, function( player_ids ){
            playerIds = playerIds.concat( player_ids );
            for( var i in player_ids ){
                var player_id = Number(player_ids[i]);
                if( isNaN( player_id )  ){
                    continue;
                }
                comment( nightmare, player_id, content );
                //must wait
                nightmare.wait();
                cmntCount++;
                if( cmntCount >= total ){
                    return;
                }
            }
            nightmare.wait();
            if( player_ids.length == PLAYERS_PER_PAGE && cmntCount < total ){
                cmntPlayers( nightmare, content, total );
            }
        }, elements)
        .wait();
}










//like
var likePlayers = function( nightmare, status, total ){
    var opEle = [];
    switch( status ){
        case 0: //dislike
            opEle.push( elements.player.followBtn );
            break;
        case 1: //like
            opEle.push( elements.player.unfollowBtn );
            break;
        case 2: //toggle
            opEle.push( elements.player.unfollowBtn );
            opEle.push( elements.player.followBtn );
            break;
    }
    var page = Math.floor( likeCount / PLAYERS_PER_PAGE ) + 1;
    console.log( status );
    console.log( opEle );
    console.log( page );
    nightmare
    .viewport( consts.viewport.width, consts.viewport.height ) 
    .goto( urls.player + urls.player_page + page )
    .evaluate( function( ele ){
        var flwBtnIds = [];
        var elements = document.querySelectorAll( ele );
        for( var i in elements ){
            var id = elements[i].id;
            if( id ){
                flwBtnIds.push( id );
            }
        }
        //$( ele ).each(function(){
        //    flwBtnIds.push( this.id );
        //});
        return flwBtnIds;
    }, function( player_ids ){
        console.log( player_ids );
            playerIds = playerIds.concat( player_ids );
            for( var i in player_ids ){
                var player_id = '#' + player_ids[i];
                var oposite = player_id + '[disabled="disabled"]';
                //var uid = player_id.match(/_(\d*)$/)[1];
                var uid= 425150;
                if( !uid ){
                    console.log('no uid?' + player_id);    
                    return;
                }
                var HOME_BASE = urls.player_home + '/' + uid ;
                console.log( HOME_BASE );
                nightmare
                .goto( HOME_BASE )
                .click( player_id )
                .wait();
                if( player_id.substr(1,2) == 'un' ){
                   nightmare.click().wait();
                }
                //nightmare
                //.exists( oposite, function( h, oposite ){
                //    if( h ){
                //        console.log( 'liked/unliked successful. '+oposite );    
                //    }
                //    else{
                //        console.log( 'like/unlike ununun successful. ' )    
                //    }
                //} );
                //nightmare.back();
                //likeCount++;
                //if( likeCount >= total ){
                //    return;
                //}
            }
            //nightmare.wait();
            if( player_ids.length == PLAYERS_PER_PAGE && likeCount < total ){
                likePlayers( nightmare, status, total );
            }
    }, opEle.join(',') )
    .wait();
}

exports.likePlayers = function( status, total ){
    return function( nightmare ){
        likePlayers( nightmare, status, total );
    }
}

