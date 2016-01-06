define(['app/collections/Base', 'app/models/Banner'], function(Collection, Banner) {
    return Collection.extend({
        model: Banner,
        url: '/banners',
        initialize: function() {
            this.data = {
                type: 'normal',
                page: 0,
                size: 10
            }
        }
     });
}); 
