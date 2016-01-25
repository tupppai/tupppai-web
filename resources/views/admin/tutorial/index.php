<ul class="breadcrumb">
    <li>
        <a href="#">教程管理</a>
    </li>
    <li>教程</li>
    <div class="btn-group pull-right">
        <a href="#add_tutorial" data-toggle="modal" class="add">创建教程</a>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="tutorial_id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<table id="tutorial_table" class="table table-bordered table-hover"></table>

<?php modal('/tutorial/add_tutorial'); ?>

<style>
#tutorial_table td.db_description{
    max-width: 200px;
    overflow: hidden;
    word-wrap: break-word;
    text-align: left;
}
.db_oper{
    width: 110px;
}
.db_cover img{
    width: 200px;
}
</style>

<script>
var table = null;

$(function() {
    table = new Datatable();
    table.init({
        src: $("#tutorial_table"),
        dataTable: {
            "columns": [
                { data: "id", name: "#" },
                // { data: "name", name: "分类名称" },
                { data: "uid", name: "用户ID"},
                { data: "title", name: "名称"},
                { data: "description", name: "描述"},
                { data: "cover", name: "封面"},
                { data: "link", name: "链接"},
                { data: "click_count", name: "浏览数"},
                { data: "comment_count", name: "评论数"},
                { data: "reply_count", name: "作品数"},//记录有多少作品上传了
                { data: "share_count", name:"分享"},
                { data: "oper", name: "操作"}
            ],
            "ajax": {
                "url": "/tutorial/list_tutorials"
            },
            'ordering':false
        },

        success: function(){},
    });

    $('#tutorial_table').on('click', '.online, .offline, .restore, .delete',function(){
        var btn = $(this);
        var postData = {
            'id': btn.attr( 'data-id' ),
            'type': 1,
            'status': btn.attr( 'data-status' )
        };
        if( btn.hasClass('delete') ){
            if(!confirm('确认删除教程?')){
                return false;
            }
        }

        $.post( '/tutorial/update_status', postData, function( res ){
            if( res.data.result == 'ok' ){
                table && table.submitFilter();
            }
        });
    });
});

</script>
<script src="/theme/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/theme/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.css"/>

<link href="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-multiselect/bootstrap-multiselect.min.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript"></script>
