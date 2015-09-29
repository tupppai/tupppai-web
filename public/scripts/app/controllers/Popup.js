define(['app/views/PopupView', 'app/models/Ask', 'app/models/Reply'],
    function (PopupView, Ask, Reply) {
        "use strict";

        var action = {};
        action.show = function(id){
        }

        action.detail = function(id){
        }

        action.comment = function(id){
        }

        return function(type, id) {
            action[type](id);
            return false;

            var view = new PopupView();
            window.app.modal.show(view);

            $('div[data-remodal-id=picture-popup-modal]').remodal().open();
        };

    });
