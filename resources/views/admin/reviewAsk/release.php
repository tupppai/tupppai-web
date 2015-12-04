
<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>审核作品</li>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
    </div>
    <div class="form-group">
        <input name="username" class="form-filter form-control" placeholder="名称">
    </div>
    <div class="form-group">
        <input name="nickname" class="form-filter form-control" placeholder="昵称">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li>
        <a href="wait">
          待编辑</a>
      </li>
      <li>
        <a href="pass">
          待生效</a>
      </li>
      <li>
        <a href="fail">
          已失效</a>
      </li>
      <li class="active">
        <a href="release">
          已发布</a>
      </li>
</div>

<table class="table table-bordered table-hover" id="review_ajax"></table>

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Datatable();
    table.init({
        dom: "<t><'row'<'col-md-5 col-sm-12'li><'col-md-7 col-sm-12'p>>",
        src: $("#review_ajax"),
        dataTable: {
            "columns": [
                { data: "id", name: "ID" },
                //{ data: "oper", name: "操作"},
                { data: "execute_time", name: "时间" },
                { data: "nickname", name: "昵称" },
                { data: "categories", name: "帖子频道" },
                //{ data: "create_time", name:"创建时间"},
                //{ data: "release_time", name:"发布时间"},
                { data: "image_view", name:"求助内容"},
                //{ data: "reply_image", name:"回复内容"}
            ],
            "ajax": {
                "url": "/reviewAsk/list_reviews?status=1&type=1",
            }
        },
        success: function(data){
            $(".edit").click(function(){

            });

            $(".deny").click(function(){
                var obj = {};
                obj.review_id = $(this).attr('data');
                obj.status  = 2;
                obj.data    = "";

                $.post("/reviewAsk/set_status", obj, function(data){
                    toastr['success']("操作成功");
                    table.submitFilter();
                });
            });

            $(".submit-score").click(function(){
                var obj = {};
                obj.review_id = $(this).attr('data');
                obj.status  = 1;
                obj.data    = $("#review_ajax input[name='score']:checked").val();

                $.post("/reviewAsk/set_status", obj, function(data){
                    toastr['success']("操作成功");
                    table.submitFilter();
                });
            });

            $(".category").click(function(){
                var obj = {};
                obj.ask_id      = $(this).attr('ask_id');
                obj.category_id = $(this).attr('category_id');
                obj.status      = $(this).hasClass("btn-primary")?0: 1;

                $.post("/reviewAsk/set_category_status", obj, function(data){
                    toastr['success']("操作成功");
                    table.submitFilter();
                });
            });
        }
    });

    $('#review_ajax').on('draw.dt',function(){
        $('td.db_categories').each(function(i, n){
            var th_cats = $('<input>').attr({
                'name': 'th_cats',
                'class': 'search-query',
                'type': 'text'
            });

            var btn = $('<button>').attr({
                'class': 'save_category',
            }).text('列入频道');
                $(n).append( th_cats ).append( btn );
        });
        $('#review_ajax').trigger('addTokenInput');
    });

    $('#review_ajax').on( 'addTokenInput', function(){

        $('input[name="th_cats"]').tokenInput("/category/search_category",{
            propertyToSearch: 'display_name',
            jsonContainer: 'data',
            theme: "facebook",
            hintText: '输入频道名，以添加频道',
            noResultsText: '无相应结果',
            searchingText: '查找中',
            tokenLimit: 5,
            // preventDuplicates: true,
            //tokenValue: 'data-id',
            // resultsFormatter: function(item){
            //     var genderColor = item.sex == 1 ? 'deepskyblue' : 'hotpink';
            //     return "<li>" +
            //     "<img src='" + item.avatar + "' title='" + item.username + " " + item.nickname + "' height='25px' width='25px' />"+
            //     "<div style='display: inline-block; padding-left: 10px;'>"+
            //         "<div class='username' style='color:"+genderColor+"'>" + item.username + "</div>"+
            //         "<div class='nickname'>" + item.nickname + "</div>"+
            //     "</div>"+
            //     "</li>" },
            tokenFormatter: function(item) {
                return "<li class='token-input-token-facebook cat_ids' data-id='"+item.id+"'>"+
                item.display_name +"</li>";
            },
        });

    });

    $('#review_ajax').on('click', '.save_category', function(){
        var cat_ids = Array();
        var tr = $(this).parents('tr');
        var target_id = tr.find('.db_id').text();

        $('input[name="th_cats"]').siblings('ul').find('li.cat_ids').each(function(){
            cat_ids.push($(this).attr('data-id'));
        });
        cat_ids = cat_ids.join(',');

        var postData = {
            'target_id': target_id,
            'target_type': 1, //ask
            'category': cat_ids,
            'status': 'normal'
        };

        $.post('/verify/set_thread_category', postData, function( data ){
            data = data.data;
            if( data.result == 'ok' ){
                table.submitFilter();
            }
        });
    });
});
</script>

<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/css/token-input.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/css/token-input-facebook.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/css/token-input-mac.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/js/jquery.tokeninput.js" type="text/javascript"></script>
<style>
    ul.token-input-list,
    ul.token-input-list-facebook,
    ul.token-input-list-mac,
    div.token-input-dropdown,
    div.token-input-dropdown-facebook,
    div.token-input-dropdown-mac,
    ul.token-input-list li input,
    ul.token-input-list-facebook li input,
    ul.token-input-list-mac li input{
        width: 200px;
        display: inline-block;
        vertical-align: middle;
    }

    .thread_category.normal{ color: dodgerblue; }
    .thread_category.verifing{ color: darkkhaki; }
    .thread_category.verified{ color: lightgreen; }
    .thread_category.deleted{ color: magenta;  text-decoration: line-through; }
</style>
