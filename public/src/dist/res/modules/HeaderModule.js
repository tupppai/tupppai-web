define([
        'marionette',
        'fancybox', 
        'app/collections/Inprogresses', 
        'app/models/User', 
        'app/collections/Users',
        'app/collections/Threads',
        'tpl!app/templates/HeaderView.html',
        'app/views/upload/InprogressItemView',
        'app/views/search/UserSearchView',
        'app/views/search/ContentSearchView',
     ],
    function (Marionette, fancybox, Inprogresses, User, Users, Threads, template, InprogressItemView,UserSearchView,ContentSearchView) {
        "use strict";

        var headerView = Marionette.ItemView.extend({
      
            model: User,
            tagName: 'div',
            className: 'logo-header-center',
            template : template,
    
            initialize: function () {
                this.listenTo(this.model, "change", this.render);
                this.listenTo(this.model, "change", this.loginArea);
            },
            loginArea: function() {
                if(this.model.get('uid') != 0) {
                    var href = '/#trend';
                    $("#headerView .login-view").addClass('hide');
                    $("#headerView .profile-view").removeClass('hide');
                    $(".menu-bar-trend").attr("href", href );
                }
                else {
                    var href = '#login-popup';
                    $("#headerView .profile-view").addClass('hide');
                    $("#headerView .login-view").removeClass('hide');
                    $(".menu-bar-trend").attr("href", href );
                }
            },
            onRender: function() {
                $(".remind-message").click(function(){
                     $(".remind-red-dot-icon").addClass('hide');
                })
                $(".scrollTop-icon").click(function(){
                    $("html, body").animate({
                        scrollTop: "0" 
                    }, 200);
                });

                $('.title-bar').removeClass("hide");

                $('#more-user').click(function(){
                    $('.menu-bar-user').click();
                });
                $('#more-thread,#search_threads').click(function(){
                    $('.menu-bar-thread').click();
                });
                $('.search-icon').click(function(){
                    var width = $('#keyword').width();
                    if( width == 0 ) {
                        $('#keyword').animate({
                            width: '180px'
                        },300).focus();
                    }
                });
                $(".inprogress-popup").click(function(){
                    var inprogresses = new Inprogresses;
                    var inprogressItemView = new Backbone.Marionette.Region({el:"#InprogressItemView"});
                    var view = new InprogressItemView({
                        collection: inprogresses
                    });
                    inprogressItemView.show(view);
                    $(".ask-uploading-popup-hide").removeClass("blo");
                })
                $('#keyword').focus(function(){
                    var value = $('#keyword').val();
                    if(value){
                        $(".search-content").css("opacity",1);
                    }
                });
                $('#keyword').blur(function(){
                    $(".search-content").css({
                        opacity : 0
                    },300);
                    var value = $('#keyword').val();
                    if(!(value)) {
                        $('#keyword').animate({
                            width: '0'
                        },300)
                    }
                });
                $('#keyword').keypress(function(e) {  
                    // 回车键事件  
                    if(e.which == 13) {
                        $("a.menu-bar-search").click();  
                    }
                });

                $('#keyword').keyup(function() {
                    var keyword = $('#keyword').val();

                    
                    if (keyword != undefined && keyword != '') {
                        var thread = new Threads;
                        thread.data.size = 3;
                        thread.url = '/search/threads';
                        thread.data.keyword = keyword;

                        var contentRegion = new Backbone.Marionette.Region({el:"#search_threads"});
                        var content_view = new ContentSearchView({
                            collection: thread
                        });
                        contentRegion.show(content_view);

                        var users = new Users;
                        users.url = '/search/users';
                        users.data.size= 3;
                        users.data.keyword = keyword;

                        var userRegion = new Backbone.Marionette.Region({el:"#search_users"});
                        var user_view = new UserSearchView({
                            collection: users
                        });
                        userRegion.show(user_view);

                        $('.search-content').show();
                    } else {
                        $('.search-content').hide();
                    }
                });

                $('.look-more-icon').click(function() {
                    $('.search-content').hide();
                }); 
                $('.look-content').unbind('click').click(function(){
                    var keyword = $('#keyword').val();
                    location.href = '#search/all/'+keyword;
                });
                $('#more-user').unbind('click').click(function(){
                    var keyword = $('#keyword').val();
                    $('.menu-bar-item ').removeClass('active');
                    location.href = '#search/user/'+keyword;
                  
                });
                $('#more-thread,#search_threads').unbind('click').click(function(){
                    var keyword = $('#keyword').val();
                    $('.menu-bar-item ').removeClass('active');
                    location.href = '#search/thread/'+keyword;
                });

                // $('a.menu-bar-search').unbind('click').click(function(){
                //     $('.menu-bar-item ').removeClass('active');
                //     var keyword = $('#keyword').val();
                //     if(keyword != undefined && keyword != '') {
                //         location.href = '#search/all/'+keyword;
                //     }
                //     else {
                //         location.href = '#search/all';
                //     }
                // });

                $("a.menu-bar-item").click(function(){ 
                    $("a.menu-bar-item").removeClass('active');
                    $(this).addClass('active');
                });

                $(".title-bar-logo").click(function(){
                    $("a.menu-bar-item").removeClass('active');
                    $("a.menu-bar-item[href='#askFlows']").addClass('active');
                });
                
                $(".return-home-page").click(function(){
                    $("a.menu-bar-item").removeClass('active');
                    $("a.menu-bar-item[href='#askflows']").addClass('active');
                });
                
                $("a.menu-bar-item[href='/"+location.hash+"']").addClass('active'); 
            }
        });

        return headerView;
    });
