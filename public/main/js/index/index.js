/**
 * 首页
 *
 */
$(function() {
    
    //Model of Ask Item
    var AskItem = Backbone.Model.extend({
        item_type: 'new',  
        initialize: function() {
        }
    }); 
    
    //Collection of Ask Items
    var AskItemList = Backbone.Collection.extend({
        current_type: 'new',
        current_page: 0,
        model: AskItem,
            
        fetch: function(type, page) {
            var indexType = typeof(type) != 'undefined' ? type : 'new';
            var indexPage = typeof(page) != 'undefined' ? page : 1;
            
            $.ajax({
                url: '/ask/getAsksByType',
                method: 'post',
                data: {'type' : indexType, 'page' : indexPage},
                success: function(data) {
                    var asks = data.data;
                    _.each(asks, function(askItem) {
                        askPage.addOne(askItem);
                    });
                    askItemList.current_page += 1;
                    askItemList.current_type = indexType;
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
            
            //求P的时候隐藏无关栏目 
            if (this.model.type == 1) {
                $('.hot-item-header').css('display', 'none');    
                $('.photo-item-replies').css('display', 'none');
            }
            return this;
        }
    });
    
    //View of Ask Page
    var AskPageView = Backbone.View.extend({
        el: $('.photo-container'),        
        
        initialize: function() {
            this.listenTo(askItemList, 'add', this.addOne);
            this.listenTo(askItemList, 'reset', this.addAll);
        },
        addOne: function(askItem) {
            var view = new AskItemView({model: askItem});
            $('.photo-container').append(view.render().el);
        },
        addAll: function() {
            askItemList.each(this.addOne, this);
        },
        //根据type调整page
        modifyPage: function(type) {
            if (type == 'new') {
                $('.hot-first-item').css('display', 'none');    
                
                if (!$('#menu-bar-ask').hasClass('active')) {
                    $('#menu-bar-ask').addClass('active');    
                }
                if ($('#menu-bar-hot').hasClass('active')) {
                    $('#menu-bar-hot').removeClass('active');    
                }
            } else if (type == 'hot') {
                $('.hot-first-item').css('display', '');    
                
                if (!$('#menu-bar-hot').hasClass('active')) {
                    $('#menu-bar-hot').addClass('active');    
                }
                if ($('#menu-bar-ask').hasClass('active')) {
                    $('#menu-bar-ask').removeClass('active');    
                }

            }
        }
    }); 
    
    var askPage = new AskPageView;   
    
    //Router of index Page
    var IndexPageRouter = Backbone.Router.extend({
        routes: {
            '': 'index_router'             
        },
        //router rules  of index page
        index_router: function() {
            var url = window.location.search;
            var page = 1;
            var type = 'new';

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
            askPage.modifyPage(type);
        }
    });
    
    var indexPageRouter = new IndexPageRouter();
    Backbone.history.start();

    //页面滚动监听 进行翻页操作
    $(window).scroll(function() {
        //页面可视区域高度
        var windowHeight = $(window).height();
        //总高度
        var pageHeight = $(document.body).height();
        //滚动条top
        var scrollTop = $(window).scrollTop();
    
        var scrollDetector = (pageHeight - windowHeight - scrollTop) / windowHeight;
        if (scrollDetector < 0.15) {
            askItemList.fetch(askItemList.current_type, askItemList.current_page+1);  
        }
    });
});

