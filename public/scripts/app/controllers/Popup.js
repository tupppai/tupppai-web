define(['app/views/PopupView'],
    function (PopupView) {
        "use strict";

        return function() {

            var view = new PopupView().render();
            console.log(view);
            var html = view.el;

            $("#modalView").html(html);
            $('div[data-remodal-id=picture-popup-modal]').remodal().open();
        };

    });
