define(['app/views/PopupView', 'app/models/Ask', 'app/models/Reply'],
    function (PopupView, Ask, Reply) {
        "use strict";

        var action = {};
        action.show = function(id){
            
        }

        action.detail = function(id){
            var ask = new Ask;
            ask.url = '/asks/'+id

            var view = new PopupView({model: ask});

            window.app.modal.show(view);

            ask.fetch({
                success: function(data) {
                    view.popupModal = $('div[data-remodal-id=picture-popup-modal]').remodal();
                    view.popupModal.open();
                    $(document).on('click', '.download', view.downloadClick);
                }
            });
        }

        action.comment = function(id){
        }

        return function(type, id) {
            action[type](id);
        };

    });
