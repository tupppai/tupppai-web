define([ 
        'app/views/home/HomeView', 
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
             render: function() {
                $('#load_inprogress').addClass('designate-nav').siblings().removeClass('designate-nav');
            },
        
        });
    });
