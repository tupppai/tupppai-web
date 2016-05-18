<ul class="breadcrumb">
  <li>
    <a href="#">交易模块</a>
  </li>
  <li>提现审核</li>
</ul>


<div class="form-inline">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
    </div>
</div>
<table class="table table-bordered table-hover" id="list_trans"></table>

<script type="text/javascript">
    $(function() {
    table = new Datatable();
    table.init({
        src: $("#list_trans"),
        dataTable: {
            "columns": [
                { data: "id", name: "流水ID" },
                { data: "trade_no", name: "交易ID" },
                { data: "uid", name: "账号ID" },
                { data: "nickname", name: "昵称" },
                { data: "amount", name:"交易金额"},
                { data: "subject", name:"标题"},
                { data: "body", name:"内容"},
                { data: "payment_type", name:"交易类型"},
                { data: "time_start", name:"请求时间"},
                { data: "trade_status", name:"状态"},
                { data: "oper", name:"操作"}
            ],
            "ajax": {
                "url": "/account/list_withdraws"
            }
        },
        success: function(){

        }
    });

    $('#list_trans').on('click', '.check_withdraw', function(){
        var status = $(this).attr('data-status');
        var tid = $(this).parents('tr').find('.db_id').text();
        var reason = '';
        if( status == 'approve' ){
            if( !confirm('确定？') ){
                return false;
            }
        }
        else if( status == 'refuse' ){
            reason = prompt('请填写拒绝理由：', '提现次数过多');
            if( !reason ){
                alert('拒绝理由不能为空。');
                return false;
            }
        }

        var postData = {
            'trade_id': tid,
            'status': status,
            'reason': reason
        };
        $.post('/account/update_withdraw', postData, function( resp ){
            if( resp.data.result == 'ok' ){
                toastr['success']('成功');
            }
            else if( resp.data.result == 'failed' ){
                toastr['error'](resp.data.msg);
            }
            table.submitFilter();
        });
    });
});
</script>
