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
            },
            selectBoy: function(e) {
            	var el = e;
            	$(el.currentTarget).addClass('boy-pressed').parent().parent().find('#select-girl').removeClass('girl-pressed');
            	
            },
            selectGirl: function(e) {
            	var el = e;
            	$(el.currentTarget).addClass('girl-pressed').parent().parent().find('#select-boy').removeClass('boy-pressed');

            }

        });
    });
