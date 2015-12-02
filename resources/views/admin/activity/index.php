<ul class="breadcrumb">
    <li>
        <a href="#">内容管理</a>
    </li>
    <li>活动管理</li>
    <div class="btn-group pull-right">
        <a href="#add_activity" data-toggle="modal" class="add">新增活动</a>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="activity_id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <input name="activity_display_name" class="form-filter form-control" placeholder="活动名称">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<table id="activity_table" class="table table-bordered table-hover"></table>

<?php modal('/activity/add_activity'); ?>

<script>
var table = null;
$(function() {
    table = new Datatable();
    table.init({
        src: $("#activity_table"),
        dataTable: {
            "columns": [
                { data: "id", name: "#" },
                // { data: "name", name: "分类名称" },
                { data: "display_name", name: "活动名称"},
                { data: "pc_pic", name: "PC图片"},
                { data: "app_pic", name: "APP图片"},
                { data: "create_time", name: "创建时间"},
                { data: "update_time", name: "更新时间" },
                { data: "oper", name: "操作"}
            ],
            "ajax": {
                "url": "/activity/list_activities"
            }
        },

        success: function(){},
    });

    $('#activity_table').on('click', '.online, .offline, .restore, .delete',function(){
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
