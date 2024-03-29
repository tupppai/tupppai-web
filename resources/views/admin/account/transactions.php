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

    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
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
                { data: "userinfo", name: "账号ID" },
                { data: "trans_amount", name:"交易金额"},
                { data: "trans_balance", name:"余额"},
                { data: "type", name:"交易类型"},
                { data: "created_at", name:"交易时间"},
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
