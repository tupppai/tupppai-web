define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/topic/',
        defaults: {
            topic_id: ' ',
            node_id: ' ',
            uid: ' ',
            ruid: ' ',
            title: ' ',
            avatar: ' ', 
            keywords: ' ',
            nickname: ' ', 
            content: ' ',
            addtime: ' ',
            updatetime: ' ',
            lastreply: ' ',
            views: ' ',
            comments: ' ',
            favorites: ' ',
            closecomment: null,
            is_top: ' ',
            is_hidden: ' ',
            ord: ' '
        },
        construct: function() {

        }
    });

}); 
