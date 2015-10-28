define([
        'marionette',
        'fancybox',  
        'app/models/User', 
        'tpl!app/templates/HeaderView.html',
        'app/views/UploadingAskView'
     ],
    function (Marionette, fancybox, User, template, UploadingAskView) {
        "use strict";

        var headerView = Marionette.ItemView.extend({
            model: User,
            tagName: 'div',
            className: '',
            template : template,
            initialize: function () {
                this.listenTo(this.model, "change", this.render);
                var view = new UploadingAskView();
                window.app.modal.show(view);
            },
        
            construct: function () {
                    $("#headerView .login-view").removeClass('hide');
            },
            onRender: function() {

                if(this.model.get('uid') != 0) {
                    $("#headerView .login-view").fadeOut(1);
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
                $(".title-bar-logo").click(function(){
                    $("a.menu-bar-item").removeClass('active');
                    $("a.menu-bar-item[href='#asks']").addClass('active');
                });
                
                $(".return-home-page").click(function(){
                    $("a.menu-bar-item").removeClass('active');
                    $("a.menu-bar-item[href='#asks']").addClass('active');
                });
                $("a.menu-bar-item[href='/"+location.hash+"']").addClass('active');
              
            },
        });

        return headerView;
    });
