<ul class="breadcrumb">
  <li>
    <a href="#">系统模块</a>
  </li>
  <li>静态页面</li>
  <div class="btn-group pull-right">
    <a href="/html/add" >添加静态页面</a>
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

<table id="html_list" class="table table-bordered table-hover"></table>

<script>
var table = null;
$(function(){
	table = new Datatable();
    table.init({
        src: $("#html_list"),
        dataTable: {
            "columns": [
            	{ data: "id", name:"ID"},
                { data: "title", name: "描述" },
                { data: "url", name: "客户端" },
                { data: "path", name: "路径" },
                { data: "status", name: "状态" },
                { data: "create_time", name: "添加时间" },
                { data: "oper", name: "操作" }
            ],
            "ajax": {
                "url": "/html/list_htmls"
            },
            'ordering':false
        },
        success:function(){
        }
    });
});
</script>
