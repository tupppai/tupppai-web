$(function() {
    //Model of Ask Item
    var AskItem = Backbone.Model.extend({
        initialize: function() {
        }
    }); 
    
    //Collection of Ask Items
    var AskItemList = Backbone.Collection.extend({
        model: AskItem,
            
        fetch: function() {
            $.ajax({
                url: '/ask/getAsksByType',
                method: 'post',
                data: {'type' : 'ask', 'page' : 1},
                success: function(asks) {
                    _.each(asks, function(askItem) {
                        askPage.addOne(askItem);
                    });
                }
            });
        }
    }); 

    var askItemList = new AskItemList;
    
    //View of Ask Item
    var AskItemView = Backbone.View.extend({ 
        askItemTemplate: _.template($('#ask-item-template').html()),

        initialize: function() {
            this.listenTo(this.model, 'change', this.render);
        },

        render: function() {
            $(this.el).html(this.askItemTemplate(this.model));
            return this;
        }
    });
    
    //View of Ask Page
    var AskPageView = Backbone.View.extend({
        el: $('.photo-container'),        
        
        initialize: function() {
            this.listenTo(askItemList, 'add', this.addOne);
            this.listenTo(askItemList, 'reset', this.addAll);
            
            askItemList.fetch();    
        },

        addOne: function(askItem) {
            var view = new AskItemView({model: askItem});
            $('.photo-container').append(view.render().el);
        },

        addAll: function() {
            askItemList.each(this.addOne, this);
        }
    });

    var askPage = new AskPageView; 
});

