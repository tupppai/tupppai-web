 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelFoldView.html'
       ],
    function (View, template ) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'channel-fold',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
            },
            onRender: function() {
                // var foldContain = $(".channel-fold").find(".fold-contain");
                // var foldContainLength      = foldContain.length;
                // var foldContainArr = [];

                // for (var i = 0; i < foldContainLength; i++) {
                //     for (var j = 0; j < foldContainLength; j++) {
                //         foldContainArr[i] = foldContain.eq(i).find(".channel-works-contain").eq(j);
                //     };
                // };
                    // console.log(foldContainArr);
                    // console.log(typeof(foldContainArr[1]));

            },
        });
    });
