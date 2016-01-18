<ul class="breadcrumb">
    <li>
        <a href="#">个人工作台</a>
    </li>
    <li>频道</li>
    <div class="btn-group pull-right">
        <a href="#add_category" data-toggle="modal" class="add">创建频道</a>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="category_id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <input name="category_display_name" class="form-filter form-control" placeholder="频道名称">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<table id="category_table" class="table table-bordered table-hover"></table>

<?php modal('/category/add_category'); ?>
<?php modal('/category/delete_category'); ?>
<style>
#category_table td.db_description{
    max-width: 200px;
    overflow: hidden;
    word-wrap: break-word;
    text-align: left;
}
.db_oper{
    width: 110px;
}
.db_pc_pic img, .db_app_pic img{
    width: 200px;
}
.db_icon img, .db_post_btn img{
    width: 50px;
}
</style>
<script>
var table = null;
$(function() {
    table = new Datatable();
    table.init({
        src: $("#category_table"),
        dataTable: {
            "columns": [
                { data: "id", name: "#" },
                // { data: "name", name: "分类名称" },
                { data: "display_name", name: "名称"},
                // { data: "parent_name", name: "父分类名称"},
                { data: "pc_pic", name: "PC图片"},
                { data: "app_pic", name: "APP图片"},
                { data: "icon", name: "频道图标"},
                { data: "post_btn", name: "创作按钮"},
                { data: "description", name: "文案"},
                // { data: "create_time", name: "创建时间"},
                // { data: "update_time", name: "更新时间" },
                { data: "oper", name: "操作"}
            ],
            "ajax": {
                "url": "/category/list_categories"
            },
            'ordering':false
        },

        success: function(){},
    });

    $('#category_table').on('click', '.online, .offline, .restore',function(){
        var btn = $(this);
        var postData = {
            'id': btn.attr( 'data-id' ),
            'status': btn.attr( 'data-status' )
        };

        $.post( '/category/update_status', postData, function( res ){
            if( res.data.result == 'ok' ){
                table && table.submitFilter();
            }
        });
    });


    $( "#category_table tbody" ).sortable({
      placeholder: "category-item-highlight",
      update: function(){
        var sorts = [];
        var items = $('#category_table tr td.db_id');
        items.each(function(i){
            sorts.push(items[i].innerText);
        });

        $.post('/category/sort_categories',{'sorts':sorts},function(result){
            if( result.ret==1){
                toastr['success']('排序更改成功');
                table.submitFilter();  //刷新表格
            }
        })
      }
    });
    $( "#category_table tr" ).disableSelection();
});

</script>
<script src="/theme/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/theme/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.css"/>

<style>
    .category-item-highlight{
        height: 120px;
        background-color: khaki;
    }
</style>
