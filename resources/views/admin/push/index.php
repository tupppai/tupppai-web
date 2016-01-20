<ul class="breadcrumb">
  <li>
    <a href="#">系统模块</a>
  </li>
  <li>Tower 操作纪录</li>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="action" class="form-filter form-control" placeholder="操作类型">
        <input name="project" class="form-filter form-control" placeholder="项目名称">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search">搜索</button>
    </div>
</div>

<table id="push_list" class="table table-bordered table-hover"></table>

<script>
var table = null;
$(function(){
	table = new Datatable();
    table.init({
        src: $("#push_list"),
        dataTable: {
            "columns": [
            	{ data: "id", name:"ID"},
                { data: "action", name: "操作类型" },
                { data: "project", name: "项目名称" },
                { data: "title", name: "todo内容" },
                { data: "create_by", name: "指派者" },
                { data: "update_by", name: "操作者" },
                { data: "update_time", name: "时间" },
            ],
            "ajax": {
                "url": "/push/list_pushes"
            },
            'ordering':false
        },
        success:function(){}
    });
});
</script>
