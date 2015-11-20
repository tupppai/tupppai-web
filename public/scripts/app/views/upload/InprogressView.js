define([
        'underscore',
        'app/views/Base',
        'tpl!app/templates/upload/InprogressView.html'
       ],
    function (_, View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });
