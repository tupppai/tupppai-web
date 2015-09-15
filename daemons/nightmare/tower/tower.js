var BASE_HOST = 'https://tower.im';
var urls = {
    'login'  : BASE_HOST + '/users/sign_in',
    'home' : BASE_HOST + '/teams/d0d3e9925d7043379b09522b6b92c97f/events/',
    'events' : BASE_HOST + '/teams/d0d3e9925d7043379b09522b6b92c97f/events/'
};
var consts = {
    'viewport':{
        'width': 1600,
        'height': 800
    }    
}

var elements = {
    'login': {
        'usernameBox': 'input[name="email"]',
        'passwordBox': 'input[name="password"]',
        'submitBtn'  : '#btn-signin'
    }  
};

//account
exports.login = function( username, password ){
    return function( nightmare ){
        nightmare
        .viewport( consts.viewport.width, consts.viewport.height )
        .goto( urls.login )
        .type( elements.login.usernameBox, username )
        .type( elements.login.passwordBox, password )
        .click( elements.login.submitBtn )
        .wait();
    }    
}; 

exports.home = function () {
    return function( nightmare ) {
        nightmare.goto(urls.home).wait();
    }
}

exports.events = function () {
    return function( nightmare ) {
        for(var i in 10) {
            nightmare
                .goto(urls.events)
                .exists( '.event-head', function( h ){console.log('head'+h);  })
                .wait(10*i);
        }
        nightmare
            .goto(urls.events)
            .exists( '.event-head', function( h ){console.log('head'+h);  })
            .wait(10*i);
    }
}
