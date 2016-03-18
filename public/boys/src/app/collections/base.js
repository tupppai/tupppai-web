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
            };
            window.addEventListener('load', function() {
              FastClick.attach(document.body);
            }, false);
        },
        plock: false,
        lock: function() { 
            if(this.plock != this._listenerId) {
                this.plock = this._listenerId;
                return false;
            }
            else {
                return true;
            }
        },
        unlock: function(data) {
            this.plock = false;
        },
        post: function(callback) {
            var self = this;
            $.post(self.url, self.data, function(data) {
                self.trigger('change');
                callback && callback(data);
            });
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

            };
            return this.sync('read', this, options);
        },
		loading: function(callback) {
            var self = this;
            this.fetch({
            	success: function(data) {
                    //add search callback
                    self.unlock(data);
                    self.trigger('change');
                    callback && callback(data);
                }
            });
        },
		paging: function(page, callback) {
			this.fetch({
				page: page,
				callback: function() {
                    self.unlock(data);
                    self.trigger('change');
                    callback && callback(data);
                }
			});
		}
     });
}); 
