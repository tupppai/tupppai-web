define(['app/views/Base', 'tpl!app/templates/SettingView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click #select-boy' : 'selectBoy',
                'click #select-girl' : 'selectGirl',
                'click .commit-btn' : 'submit'
            },
            selectBoy: function(e) {
            	var el = e;
            	$(el.currentTarget).addClass('boy-pressed').parent().parent().find('#select-girl').removeClass('girl-pressed');
            	
            },
            selectGirl: function(e) {
            	var el = e;
            	$(el.currentTarget).addClass('girl-pressed').parent().parent().find('#select-boy').removeClass('boy-pressed');

            },
            submit: function() {
                //todo: 这里存放id
                var avatar   = $(".head-picture img").attr('src');
                var nickname = $(".nickname-input input").val();
                var sex = ($(".setting-sex input[type='radio']:checked").attr('id') == 'select-boy')? 1: 0;

                if (nickname == '') {
                    alert('昵称不能为空哦');
                    return false;
                }

                $.post('/user/save', {
                    avatar: avatar,
                    nickname: nickname,
                    sex: sex
                }, function(data) {
                    //todo: toast
                });
            }
        });
    });
