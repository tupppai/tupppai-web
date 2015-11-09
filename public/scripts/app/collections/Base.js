define(['backbone', 'underscore'], function(Backbone, _) {
    return Backbone.Collection.extend({
        data: {
            page: 0,
            size: 10
        },
		initialize: function() {
            this.data = {
                page: 0,
                size: 15
            }
        },
        post: function(callback) {
            var self = this;
            $.post(self.url, self.data, function(data) {
                var data = self.parse(data);

                self.trigger('change');
                callback && callback(data);
            });
        },
        parse: function(resp, xhr) {  
            if(this.url == 'user/status') {
            }
            else if(resp.ret == 0 && resp.code == 1 ){
                $(".login-popup").click();
            }
            else if(resp.ret == 0 ) {
                error('操作失败', resp.info);
            }
            return resp.data;
        },
        plock: false,
        lock: function() { 
            if(!this.plock) {
                this.plock = true;
                return false;
            }
            else {
                return true;
            }
        },
        unlock: function() {
            this.plock = false;
        },
        fetch: function(options) {
			var self = this;
            if(self.lock()) return true;

            options = options ? _.clone(options) : {};

			// add search filter
			if(self.page)
            	self.data.page = self.page;
			else 
				self.data.page ++;
			options.data = self.data;
            if (options.parse === void 0) options.parse = true;
            var success = options.success;
            options.success = function (collection, resp, options) {
                var method = options.update ? 'update' : 'reset';
                collection[method](resp, options);
                if (success) success(collection, resp, options);

				//add search callback
                self.unlock();
                self.trigger('change');
                options.callback && options.callback(resp.data);
            };
            return this.sync('read', this, options);
        },
		loading: function(callback) {
            this.fetch({
            	callback: callback
            });
        },
		paging: function(page, callback) {
			this.fetch({
				page: page,
				callback: callback
			});
		}
     });
}); 
