define(['app/collections/Base', 'app/models/Channel'], function(Collection, channel) {
    return Collection.extend({
        model: channel,
        url: '/channels'
     });
}); 
