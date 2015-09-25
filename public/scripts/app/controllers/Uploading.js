define(['app/views/UploadingView'],
    function (UploadingView) {
        "use strict";

        return function() {

            var view = new UploadingView().render();
            console.log(view);
            var html = view.el;

            $("#modalView").html(html);
            $('div[data-remodal-id=uploading-modal]').remodal().open();
        };

    });
