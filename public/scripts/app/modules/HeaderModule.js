define([
        'marionette',
        'fancybox', 
        'app/models/User', 
        'tpl!app/templates/HeaderView.html',
     ],
    function (Marionette, fancybox, User, template) {
        "use strict";

        var headerView = Marionette.ItemView.extend({
            model: User,
            tagName: 'div',
            className: '',
            template : template,

            initialize: function () {
                this.listenTo(this.model, "change", this.render);
                this.listenTo(this.model, "change", this.loginArea);

            },
            loginArea: function() {
                if(this.model.get('uid') != 0) {
                    $("#headerView .login-view").addClass('hide');
                    $("#headerView .profile-view").removeClass('hide');
                }
                else {
                    $("#headerView .profile-view").addClass('hide');
                    $("#headerView .login-view").removeClass('hide');
                }
            },
            onRender: function() {
                $('#keyword').blur(function(){
                    $(".search-content").css("display","none");
                })
                $('#keyword').keypress(function(e) {  
                    // 回车键事件  
                    if(e.which == 13) {
                        $("a.menu-bar-search").click();  
                    }
                });

                $('#keyword').keyup(function() {
                    var keyword = $('#keyword').val();
                    if (keyword != undefined && keyword != '') {
                        // 异步获取相关用户
                        $.ajax({
                            type: 'GET',
                            url : 'search/users?size=3&keyword=' + keyword,
                            success: function(data) {
                                var users_tpl = $('#tpl_search_users').html();
                                var user_template = Handlebars.compile(users_tpl);

                                var result = {
                                    data: data.data
                                };
                                var users_html = user_template(result);

                                $('#search_users').html(users_html);
                            }
                        });

                        // 异步获取相关内容
                        $.ajax({
                            type: 'GET',
                            url : 'search/threads?size=3&keyword=' + keyword,
                            success: function(data) {
                                var threads_tpl = $('#tpl_search_threads').html();
                                var threads_template = Handlebars.compile(threads_tpl);

                                var result = {
                                    data: data.data
                                };
                                var threads_html = threads_template(result);

                                $('#search_threads').html(threads_html);
                            }
                        });

                        $('.search-content').show();
                    } else {
                        $('.search-content').hide();
                    }
                });

                $('.look-more-icon').click(function() {
                    $('.search-content').hide();
                    $("a.menu-bar-search").click();  
                }); 

                $('a.menu-bar-search').unbind('click').click(function(){
                    var keyword = $('#keyword').val();
                    $('.menu-bar-item ').removeClass('active');
                    if(keyword != undefined && keyword != '') {
                        location.href = '#search/all/'+keyword;
                    }
                    else {
                        location.href = '#search/all';
                    }
                });

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
