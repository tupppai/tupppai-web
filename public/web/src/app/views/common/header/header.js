define(['uploadify', 'tpl!app/views/common/header/header.html'],
    function (uploadify, template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .login-tupppai": 'fnLoginPopup',
                "click .resgister-tupppai" : 'fnResgister',
                "click .ask-button" : 'fnBinding',
                "click .header-nav li" : 'fnPress',
                "click #exit-out" : "fnExitOut",
                "click .login-btn" : 'fnLoginPopup',
                "click .login-btn" : 'fnLoginPopup',
                "click .logo" : "fnUpdate"
            },
            onShow: function() {
                var self = this;
                Common.upload("#upload_picture-reply", function(data){
                    $("#updateImage").attr('src',data.data.url);
                }, null, {
                     url: '/upload'
                });
              $("a#Binding-popup").fancybox({
                    'padding': 0
                });
              $('#set_work').click(function() {
                    var src = $('#updateImage').attr('src');
                    var desc = $('desc_content').text();
                    var upload_id = $('#set_work').attr('data-upload-id');
                    $.post('/v2/replies/save',{
                        upload_id: upload_id,
                        desc: desc,
                        tasgs: tasgs,
                    },function(){

                    })

              })
              $('.login-button').click(function(){
                    var username = $('.fancybox-inner .phone-number input').val();
                    var password = $('.fancybox-inner .password input').val();
                    var url = '/user/login'
                    $.post( url ,{
                        username: username ,
                        password: password
                    } ,function(data){
                        if(data.uid) {
                           history.back();
                           window.location.reload();
                        }

                    })
                })

              $('#gain-code').click(function(){
                    var util = {
                        wait: 60,
                        hsTime: function (that) {
                            console.log(that);
                            var self = $(this);
                            var wait = $(that).val();
                            wait = wait.slice(0,-1);
                            self.addClass('sent');

                            if (wait == 0) {
                                $('#gain-code').removeAttr("disabled").removeClass('sent').val('重新发送');
                                self.wait = 60;
                            } else {
                                var self = this;
                                $(that).attr("disabled", true).addClass('sent').val( + self.wait + 'S');
                                self.wait--;
                                setTimeout(function () {
                                    self.hsTime(that);
                                }, 1000)
                            }
                        }
                    }
                    var phone = $('#register_phone').val();
                    var phone_lenght = phone.length;
                    var url = '/user/code'

                    if( phone_lenght != 11 ) {
                        alert( "你的手机号码位数不对" );
                    }else {
                        $.get(url,{
                            phone: phone
                        },function(){
                            util.hsTime('#gain-code');
                        })
                    }
              })

              $('#user-register-btn').click(function(){
                    var sex = 1;
                    var code = $('#register_code').val();
                    var avatar = $('#register-avatar').attr('src');
                    var nickname = $('#register_nickname').val();
                    var phone    =  $('#register_phone').val();
                    var password = $('#register_password').val();

                    var url = "/user/register";
                    var postData = {
                        'nickname': nickname,
                        'sex' : sex,
                        'mobile': phone,
                        'password': password,
                        'avatar' : avatar,
                        'code' : code,
                        'type' : 'mobile'
                    };
                    $.post(url, postData, function( returnData ){
                        if(returnData.ret != 0)
                            history.back();
                            location.reload();
                    });
              })
            },
            upload: function(e) {
                debugger;
                var upload_id = $("#upload_picture-reply").attr("upload-id");
                var ask_id    = $('#reply-uploading-popup').attr("ask-id");
                var desc      = $("#reply-uploading-popup .reply-content").val();
                var category_id = $('#reply-uploading-popup').attr("data-id");
                var task = $('body').attr('data-task');
                var id = $('body').attr('data-id');

                if( !desc) {
                    error('提示','内容不能为空');
                    return false;
                }
                if( !upload_id ) {
                    error('上传作品','请上传作品');
                    return false;
                }
                if( task == 'task') {
                    var url = '/task/upload/'+id;
                } else {
                    var url = '/replies/save';
                }
                    $.post(url, {
                        ask_id: ask_id,
                        upload_id: upload_id,
                        category_id: category_id,
                        desc: desc,
                    }, function(data) {
                        $.fancybox.close();
                            // if(category_id) {
                            //     location.href = '/#channel/'+ category_id ;
                            //     location.reload();
                            // } else {
                            //     location.href = '/#channel/reply';
                            //     location.reload();
                            // }

                            $('.title-bar').removeClass("hide");
                            $('.header-back').removeClass("height-reduce");
                            $(".reply-index").addClass("active").siblings().removeClass("active");
                             toast('上传成功',function(){
                            // location.reload();
                        });
                    });
                    var src = "http://7u2spr.com1.z0.glb.clouddn.com/20151205-154952566297205441e.png";
                $(".upload-middle").removeClass("opacity");
                $(".show-picture").attr("src", src);
                $("#upload_picture-reply").attr("upload-id", '');
                $("#reply-uploading-popup").attr("ask-id", '');
                $(".upload-accomplish").parent().parent().find(".reply-content").val('');
            },
            fnUpdate: function() {
                $("a[href=#update-popup]").attr('id','update-popup');

                $("a#update-popup").fancybox({
                        'padding': 0,
                    });
                $("a#update-popup").click();
                $("a#update-popup").removeAttr('id');

            },
            fnExitOut:function() {
                $.get('/user/logout',{},function(){
                        window.location.reload();
                })
            },
            fnPress:function(e) {
                $('.header-nav li').removeClass('home-press');
                $(e.currentTarget).addClass('home-press');
            },
            fnLoginPopup:function() {
                $("a[href=#login-popup]").attr('id','login-popup');

                $("a#login-popup").fancybox({
                        'padding': 0,
                    });
                $("a#login-popup").click();
                $("a#login-popup").removeAttr('id');
            },
            fnResgister: function() {
              $("a[href=#resgister-popup]").attr('id','resgister-popup');
              $("a#resgister-popup").fancybox({
                    'padding': 0
                });
                $("a#resgister-popup").click();
                $("a#resgister-popup").removeAttr('id');
            },
            fnBinding: function() {
                $("a#Binding-popup").click();
            }

        });
    });
