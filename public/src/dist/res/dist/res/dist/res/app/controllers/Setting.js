define(['underscore', 
        'app/models/User', 
        'app/views/SettingView',
        'app/views/BaseMaterialView',
        'app/views/UserSafetyView',
       ],
    function (_, User, SettingView, BaseMaterialView, UserSafetyView) {
        "use strict";

        return function(type) {
            setTimeout(function(){
                $("title").html("图派-设置");
                $('.header-back').removeClass("height-reduce");
            },100);

            var user = new User({type: type});
            user.url = 'user/status?settings';
            user.fetch();

            var view = new SettingView({ model: user });
            window.app.content.show(view);

             var baseMaterialRegion = new Backbone.Marionette.Region({el:"#settingContent"});
             var base_view = new BaseMaterialView({
                 model: user
             });

             var safetyMaterialRegion = new Backbone.Marionette.Region({el:"#settingContent"});
             var safety_view = new UserSafetyView({
                 model: user
             });

            switch(type) {
            case 'base':
                baseMaterialRegion.show(base_view);
                break;
            case 'safety':
                safetyMaterialRegion.show(safety_view);
                break;
            default:
                baseMaterialRegion.show(base_view);
                break;
            }
        };
    });
