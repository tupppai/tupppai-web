<ul class="breadcrumb">
  <li>
    <a href="#">系统模块</a>
  </li>
  <li>推荐Sms</li>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="phone" class="form-filter form-control" placeholder="手机号码">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search">搜索</button>
    </div>
</div>

<table id="sms_list" class="table table-bordered table-hover"></table>

<script>
var table = null;
$(function(){
	table = new Datatable();
    table.init({
        src: $("#sms_list"),
        dataTable: {
            "columns": [
            	{ data: "id", name:"ID"},
                { data: "to", name: "手机号码" },
                { data: "content", name: "内容" },
                { data: "sent_time", name: "发送时间" },
                { data: "reg_time", name: "注册时间" },
                { data: "is_reg", name: "是否注册" },
                { data: "is_used", name: "是否使用" },
            ],
            "ajax": {
                "url": "/sms/list_smses"
            },
            'ordering':false
        },
        success:function(){
        }
    });
});
</script>
