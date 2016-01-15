<ul class="breadcrumb">
  <li>
    <a href="#">交易模块</a>
  </li>
  <li>用户交易流水</li>
</ul>


<div class="form-inline">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
    </div>
</div>
<table class="table table-bordered table-hover" id="list_account_transactions"></table>

<script type="text/javascript">
    $(function() {
    table = new Datatable();
    table.init({
        src: $("#list_account_transactions"),
        dataTable: {
            "columns": [
                { data: "id", name: "流水ID" },
                { data: "uid", name: "账号ID" },
                { data: "amount", name:"交易余额"},
                { data: "balance", name:"余额"},
                { data: "type", name:"交易类型"},
                { data: "amount", name: "设备"},
                { data: "memo", name: "备注"},
                { data: "status", name:"交易状态"}
            ],
            "ajax": {
                "url": "/account/list_transactions"
            }
        },
        success: function(){

        }
    });
});
</script>
