define([ 
		'app/models/boy',
		'app/models/user',
		'app/views/selectmale/SelectMaleGodsView',
		'app/views/selectmale/selectAvatarView',
		'app/views/selectmale/selectButtonView' 
		], 
	function (boy, User, SelectMaleGodsView, selectAvatarView, selectButtonView) {
    "use strict";
    return function() {
        var view = new SelectMaleGodsView({
        });
        window.app.content.show(view);

       // 头像数组
    	var boyMessage = new boy;
    	boyMessage.url = '/wxactgod/avatars'
        var movieDetailViewRegion = new Backbone.Marionette.Region({el:"#selectionAvatar"});
        var movieDetailView = new selectAvatarView({
        	model: boyMessage
        });
        movieDetailViewRegion.show(movieDetailView);

       // 我要制作按钮
        var user = new User;
        user.url = '/wxactgod/index'
        var movieDetailViewRegion = new Backbone.Marionette.Region({el:"#selectButton"});
        var movieDetailView = new selectButtonView({
            model: user
        });
        movieDetailViewRegion.show(movieDetailView);
    };
});
