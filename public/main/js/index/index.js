$(function() {
    //Model of Ask Item
    var AskItem = Backbone.Model.extend({
        initialize: function() {
        }
    }); 
    
    //Collection of Ask Items
    var AskItemList = Backbone.Collection.extend({
        model: AskItem,
            
        fetch: function(type, page) {
            var indexType = typeof(type) != 'undefined' ? type : 'ask';
            var indexPage = typeof(page) != 'undefined' ? page : 1;
            $.ajax({
                url: '/ask/getAsksByType',
                method: 'post',
                data: {'type' : indexType, 'page' : indexPage},
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
        
        events: {
            'click .actionbar-like-icon': 'action_like'  
        },
        action_like: function() {
            var item_id = this.model.id;
            //TODO likeAction here
            psAjax('/ask/upAsk', 'get', {id: item_id});            
        },
        initialize: function() {
            this.listenTo(this.model, 'change', this.render);
        },

        render: function() {
            $(this.el).html(this.askItemTemplate(this.model));
            return this;
        }
    });
    
    //Router of index Page
    var IndexPageRouter = Backbone.Router.extend({
        routes: {
            '': 'index_router'             
        },
        //router rules  of index page
        index_router: function() {
            var url = window.location.search;
            var page = 1;
            var type = 'hot';

            if (url.indexOf('?') != -1) {
                var str = url.substr(1);    
                var strs = str.split('&');

                for (var i = 0; i < strs.length; i++) {
                    if (strs[i].split('=')[0] == 'page') {
                        page = strs[i].split('=')[1];       
                    }    
                    if (strs[i].split('=')[0] == 'type') {
                        type = strs[i].split('=')[1];    
                    }
                }
            }
            askItemList.fetch(type, page);
        }
    });
    
    var indexPageRouter = new IndexPageRouter();
    Backbone.history.start();

    //View of Ask Page
    var AskPageView = Backbone.View.extend({
        el: $('.photo-container'),        
        
        initialize: function() {
            this.listenTo(askItemList, 'add', this.addOne);
            this.listenTo(askItemList, 'reset', this.addAll);
                
            askItemList.fetch();    
            
            //初始化分页组件
            this.initialize_pagination(); 

        },
        initialize_pagination: function() {
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

