<ul class="breadcrumb">
    <li>
        <a href="#">帖子模块</a>
    </li>
    <li>分类</li>
    <div class="btn-group pull-right">
        <a href="#add_tag" data-toggle="modal" class="add">创建栏目</a>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="tag_id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <input name="tag_name" class="form-filter form-control" placeholder="名称">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<table id="tag_table" class="table table-bordered table-hover"></table>

<?php modal('/tag/add_tag'); ?>
<?php modal('/tag/delete_tag'); ?>

<script>
var table = null;
$(function() {
    table = new Datatable();
    table.init({
        src: $("#tag_table"),
        dataTable: {
            "columns": [
                { data: "id", name: "#" },
                { data: "name", name: "名称" },
                { data: "release_time", name: "生效时间"},
                { data: "user_count", name: "使用用户数"},
                { data: "thread_count", name: "内容数" },
                { data: "status", name: "状态" },
                { data: "oper", name: "操作"},
                { data: "reason", name: "描述" , "id":"11"}
            ],
            "ajax": {
                "url": "/tag/list_tags"
            }
        },
        success: function(){}
    });

});

</script>
