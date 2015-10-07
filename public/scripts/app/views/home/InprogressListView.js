define([ 
        'app/views/home/ListView', 
        'app/collections/Inprogresses', 
        'tpl!app/templates/home/InprogressItemView.html',
       ],
    function (View, Inprogresses, InprogressItemTemplate ) {
        "use strict";

        var inprogresses = new Inprogresses;


        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: inprogresses,
            template: InprogressItemTemplate,
         
        });
    });
