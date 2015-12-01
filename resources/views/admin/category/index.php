<ul class="breadcrumb">
    <li>
        <a href="#">个人工作台</a>
    </li>
    <li>分类</li>
    <div class="btn-group pull-right">
        <a href="#add_category" data-toggle="modal" class="add">创建分类</a>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="category_id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <input name="category_name" class="form-filter form-control" placeholder="栏目名称">
    </div>
    <div class="form-group">
        <input name="category_display_name" class="form-filter form-control" placeholder="展示名称">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<table id="category_table" class="table table-bordered table-hover"></table>

<?php modal('/category/add_category'); ?>
<?php modal('/category/delete_category'); ?>

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
                { data: "parent_name", name: "父分类名称"},
                { data: "pc_pic", name: "PC图片"},
                { data: "app_pic", name: "APP图片"},
                // { data: "create_time", name: "创建时间"},
                // { data: "update_time", name: "更新时间" },
                { data: "oper", name: "操作"}
            ],
            "ajax": {
                "url": "/category/list_categories"
            }
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
});

</script>
