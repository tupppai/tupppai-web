<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>内容分类</li>
</ul>

<div id="search_form">
    <div class="form-inline">
<!--         <div class="form-group">
            <input name="uid" class="form-filter form-control" placeholder="账号ID">
        </div>
        <div class="form-group">
            <input name="nickname" class="form-filter form-control" placeholder="昵称">
        </div>
 -->
        <div class="form-group">
            <input type="hidden" name="category_id" class="form-group" value="<?=$_REQUEST['category_id'];?>" />
            <input type="hidden" name="category_type_id" class="form-filter form-control" value="<?=\App\Models\Category::CATEGORY_TYPE_REPLIES;?>" />
        </div>
<!--        <div class="form-group">-->
<!--            <select name="arguments['category_type']" id="" class="form-filter form-control">-->
<!--                <option value="null">全部</option>-->
<!--                <option value="--><?//=\App\Models\Category::CATEGORY_TYPE_REPLIES ?><!--" selected="selected">作品</option>-->
<!--                <option value="--><?//=\App\Models\Category::CATEGORY_TYPE_ASKS ?><!--" selected="selected">求助</option>-->
<!--            </select>-->
<!--        </div>-->
        <div class="form-group">
            <input type="text" name="uid" class="form-filter form-control" id="uid" placeholder="uid">
        </div>
        <div class="form-group">
            <input type="text" name="nickname" class="form-filter form-control" id="nickname" placeholder="昵称">
        </div>
        <div class="form-group">
            <input type="text" name="id" class="form-filter form-control" id="uid" placeholder="ID 作品OR求助">
        </div>
        <div class="form-group">
            <input type="text" name="desc" class="form-filter form-control" id="desc" placeholder="描述">
        </div>
        <div class="form-group">
            <input type="text" name="start_time" class="form-filter form-control" id="start_at" placeholder="开始时间">
            <input type="text" name="end_time" class="form-filter form-control" id="end_at" placeholder="结束时间">
        </div>
        <div class="form-group">
            <button type="submit" class="form-filter form-control" id="search" >搜索</button>
        </div>
    </div>
</div>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li class="all">
        <a href="?">待审核</a>
      </li>
      <li class="view_threads">
        <a href="/verify/channels?status=valid">全部</a>
      </li>
    </ul>
</div>
<div class="tabbable-line">
    <label for="selectAll">
        <input type="checkbox" name="selectAll" id="selectAll"/>全选
    </label>
    <span class="thread_category normal">█</span>正在生效(用户自己添加)
    <span class="thread_category verifing">█</span>待审核
    <span class="thread_category verified">█</span>已审核
    <span class="thread_category deleted">█</span>已拒绝
</div>


<div id="thread-data"></div>

<link href="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.js" type="text/javascript"></script>
<button class="btn btn-danger delete" style="width: 20%">拒绝</button>
<button class="btn btn-info online" style="width: 20%">通过</button>

<?php modal('/verify/category_item'); ?>

<style>
    .photo-container-admin{
        margin-top: 10px;
        margin-bottom: 10px;
        border: 1px solid transparent;
    }
    .photo-container-admin:hover{
        border: 1px solid steelblue;
    }
    .set-value label{
        padding: 0;
    }
    .photo-main{
        border-top:1px solid lightgray;
        border-bottom:1px solid lightgray;
    }
    .remove_from_category{
        cursor: pointer;
    }

    .thread_category.normal{ color: dodgerblue; }
    .thread_category.verifing{ color: darkkhaki; }
    .thread_category.verified{ color: lightgreen; }
    .thread_category.deleted{ color: magenta;  text-decoration: line-through; }
</style>
<script>
var table = null;
var category_id;
var status;
var pc_host = '<?php echo $pc_host; ?>';

jQuery(document).ready(function() {
    category_id = getQueryVariable('category_id', '');
    status = getQueryVariable('status', 'checked');
    if( status == 'valid' ){
        $('li.view_threads').addClass('active');
        $('#thread-data~button.btn').hide();
        $('label[for="selectAll"]').hide();
    }
    else{
        $('li.all').addClass('active');
    }

    table = new TempPaginate();
    table.init({
        src: $('#thread-data'),
        url: '/verify/list_category_threads?category_id='+category_id+'&category_type=channels&status='+status,
        template: _.template($('#thread-item-template').html()),
        success: function() {
        },
        display:15
    });

    $('#selectAll').on('click', function(){
        var all = $(this).prop('checked');
        var checkboxes = $('.photo-container-admin input[name="confirm_online"]');
        if( all ){
            checkboxes.prop('checked', 'checked');
        }
        else{
            checkboxes.removeProp('checked');
        }
    });



     $('.online, .delete').on('click', function(){
        var target_types = [];
        var target_ids = [];
        var categories = [];
        var statuses = [];
        var thread_status;
        $('input[name="confirm_online"]:checked').each(function( i, n ){
            var p = $(this).parents('.photo-container-admin');
            target_types.push( p.attr('data-target-type') );
            target_ids.push( p.attr('data-target-id') );
            categories.push( p.attr('data-category-id') );
        });

        if( $(this).hasClass('delete') ){
            thread_status = 'delete';
        }
        else if( $(this).hasClass('online') ){
            thread_status = 'done';
        }
        var postData = {
            'target_id[]': target_ids,
            'target_type[]': target_types,
            'category_id[]': categories,
            'status': thread_status
        };

        $.post('/verify/set_thread_category_status', postData, function( data ){
            if( data.data.result == 'ok' ){
              table.submitFilter();
            }
        });
    });



    $("#start_at").datetimepicker({
        //navigationAsDateFormat: true,
        dateFormat: 'yy-mm-dd',
        autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-left"
    });
    $("#end_at").datetimepicker({
        dateFormat: 'yy-mm-dd'
    });


});

/***
 Wrapper/Helper Class for datagrid based on jQuery Datatable Plugin
 ***/
var TempPaginate = function() {
    var options = {
        src: null,
        url: null,
        template: null,

        count       : 50,
        start       : 1,
        display     : 10,
        border                  : false,
        text_color              : '#79B5E3',
        background_color        : 'none',
        text_hover_color        : '#2573AF',
        background_hover_color  : 'none',
        images      : false,
        mouse       : 'press'
    };

    return {
        init: function(opts) {
            for(var i in options) {
                if(opts[i]) options[i] = opts[i];
            }
            options.onChange = this.submitFilter;
            options.success  = opts.success;
            var Paginate = this;
            options.src.append('<div id="paginate-content"></div>');
            options.src.append('<div id="paginate-pagebar"></div>');
            $('button.form-filter[type="submit"]').click(function() {
                Paginate.submitFilter();
            });
            this.submitFilter(1);
        },
        submitFilter: function(index){
            if(!index) index = options.start;
            var params= {};
            var forms = $(".form-filter");
            _.each(forms, function(row){
                if(row.name && row.value)
                    params[row.name] = row.value;
            });
            params['page'] = index;

            $.get(options.url, params, function(data){
                $("#paginate-content").empty();
                data = JSON.parse(data);

                options.count = data.recordsTotal/options.display;
                if(options.count > parseInt(options.count)) {
                    options.count = parseInt(options.count) + 1;
                }
                //todo: error reporting
                results = data.data;
                for(var i in results){
                    $("#paginate-content").append(options.template(results[i]));
                }
                if(results.length == 0){
                    $("#paginate-content").append('<div style="margin-top: 20px; text-align:center">空记录</div>');
                }
                options.success && options.success(results);

                options.start = index;
                if(options.count > 1) {
                    $("#paginate-pagebar").paginate(options);
                }
            });
        }
    };
};


</script>
