<ul class="breadcrumb">
    <li>
        <a href="#">内容管理</a>
    </li>
    <li>标签管理（用户列表）</li>
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

<script>
var table = null;
$(function() {
    table = new Datatable();
    table.init({
        src: $("#tag_table"),
        dataTable: {
            "columns": [
                { data: "id", name: "#" },
                { data: "uid", name: "uid" },
                { data: "tag_id", name: "tag_id" },
                { data: "status", name: "状态" },
                { data: "phone", name: "电话" },
                { data: "username", name: "用户名" },
                { data: "nickname", name: "昵称" },
                { data: "avatar", name: "头像" },
                { data: "image_url", name: "图片" }
            ],
            "ajax": {
                "url": "/tag/list_threads"
            }
        },
        success: function(){}
    });

});

</script>
