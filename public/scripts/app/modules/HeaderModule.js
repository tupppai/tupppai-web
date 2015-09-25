define(['marionette', 'app/models/User', 'tpl!app/templates/HeaderView.html'],
    function (Marionette, User, template) {
        "use strict";

        var headerView = Marionette.ItemView.extend({
            model: User,
            tagName: 'div',
            className: '',
            template : template,
            initialize: function () {
                this.listenTo(this.model, "change", this.render);
            },
            onRender: function() {
                if(this.model.get('uid') != 0) {
                    $("#headerView .login-view").addClass('hide');
                    $("#headerView .profile-view").removeClass('hide');
                }
                else {
                    $("#headerView .profile-view").addClass('hide');
                    $("#headerView .login-view").removeClass('hide');
                }
                //全局事件
                $("a.menu-bar-item").click(function(){ 
                    $("a.menu-bar-item").removeClass('active');
                    $(this).addClass('active');
                });
                $("a.menu-bar-item[href='"+location.hash+"']").addClass('active');
                $("span.title-bar-setting").click(function(){ 
                    $("#setting_panel").toggleClass('hide');
                });
            }
        });

        return headerView;
    });
