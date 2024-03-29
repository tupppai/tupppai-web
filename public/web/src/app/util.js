define(['backbone', 'marionette', 'lib/component/asyncList'], function (Backbone, Marionette) {
    "use strict";

    var util = {};
    util.collection = Backbone.Collection.extend({

    });
    util.model = Backbone.Model.extend({

    });
    util.view = Marionette.ItemView.extend({

    });
    util.list = Marionette.CollectionView.extend({
        onBeforeRender: function(view) {
            if(this.headerView) {
                this.$el.append(new this.headerView(this.headerData).render().el);
            }
        },
        onBeforeShow: function(view) {
            if(this.footerView) {
                this.$el.append(new this.footerView(this.footerData).render().el);
            }
        }
    });

    util.layout = Marionette.LayoutView.extend({

    });

    util.render = function(sections, callback) {
        var html = '';
        var regions = {};
        //todo multi level
        for(var i in sections) {
            html += '<section id="'+sections[i]+'"></section>';
            regions[sections[i]] = '#' + sections[i];
        }

        //layout init
        var layout = window.app.layout.extend({
            template: function() { return html; },
            regions: regions,
            onRender: callback
        });
        var layoutView = new layout;
        window.app.content.show(layoutView);

        return layoutView;
    };

    util.show = function(region, view) {
        if(view.options.loadCollection === false) {
            region.show(view);
        }
        else if(view.model) {
            view.model.fetch({
                success: function(data) {
                    region.show(view);
                }
            });
        }
        else if(view.collection) {
            view.collection.fetch({
                success: function(data) {
                    region.show(view);
                }
            });
        }
        else {
            region.show(view);
        }
    };


    util.showLayout = function(){
        // var sections = [ '_indexHeader', '_recommendItem','_popularItem','_productItem','_list', '_more' ];
        // var layoutView = window.app.render(sections);
        console.log(this)
        console.log(arguments)
    }

    return util;
});
