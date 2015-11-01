<ul class="breadcrumb">
  <li>
    <a href="#">系统模块</a>
  </li>
  <li>异常列表</li>
  <div class="btn-group pull-right">
        <a href="#add_app" data-toggle="modal"  data-target="#add_app" class="add">添加App</a>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="app_name" class="form-filter form-control" placeholder="应用名称">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search">搜索</button>
    </div>
</div>

<table id="exception_list" class="table table-bordered table-hover"></table>

<script>
var table = null;
$(function(){
	table = new Datatable();
    table.init({
        src: $("#exception_list"),
        dataTable: {
            "columns": [
            	{ data: "id", name:"ID"},
                { data: "messages", name: "内容" },
                { data: "create_time", name: "创建时间" },
                { data: "oper", name: "操作" }
            ],
            "ajax": {
                "url": "/exception/list_exceptions"
            },
            'ordering':false
        },
        success:function(){}
    });
});
</script>
