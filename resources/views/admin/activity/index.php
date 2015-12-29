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

<link href="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.js" type="text/javascript"></script>

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
                { data: "end_time", name: "活动截止日期"},
                { data: "description", name: "文案"},
                { data: "pc_pic", name: "PC栏目图"},
                { data: "app_pic", name: "APP栏目图"},
                { data: "banner_pic", name: "App活动banner"},
                { data: "pc_banner_pic", name: "PC活动banner"},
                { data: "post_btn", name: "提交按钮"},
                { data: "icon", name: "icon"},
                { data: "ask_view", name: "求助内容"},
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
