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
        <input name="tutorial_display_name" class="form-filter form-control" placeholder="频道名称">
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
.db_pc_banner_pic img, .db_banner_pic img{
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
                { data: "display_name", name: "名称"},
                { data: "description", name: "描述"},
                { data: "link", name: "链接"},
                { data: "banner_pic", name: "APP封面"},
                { data: "pc_banner_pic", name: "PC封面"},
                { data: "oper", name: "操作"}
            ],
            "ajax": {
                "url": "/tutorial/list_tutorials"
            },
            'ordering':false
        },

        success: function(){},
    });

    $('#tutorial_table').on('click', '.online, .offline, .restore',function(){
        var btn = $(this);
        var postData = {
            'id': btn.attr( 'data-id' ),
            'status': btn.attr( 'data-status' )
        };

        $.post( '/tutorial/update_status', postData, function( res ){
            if( res.data.result == 'ok' ){
                table && table.submitFilter();
            }
        });
    });
});

</script>

