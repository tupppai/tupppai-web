<ul class="breadcrumb">
    <li>
        <a href="#">公众号活动管理</a>
    </li>
    <li>男神活动</li>
    <div class="btn-group pull-right">
        <?php echo $oper ?>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        处理状态<select name="ask_status" class="form-control form-filter">
            <option value="">全部</option>
            <option value="pending">未处理</option>
            <option value="processing">进行中</option>
            <option value="rejected">已拒绝</option>
            <option value="done">已完成</option>
        </select>
    </div>
    <div class="form-group">
        领取状态<select name="reply_status" class="form-control form-filter">
            <option value="">全部</option>
            <option value="received">已领取</option>
            <option value="unreceived">未领取</option>
        </select>
    </div>
    <div class="form-group">
        <select class="form-control form-filter" name="assign_uid" id="operator">
        </select>
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<table id="activity_table" class="table table-bordered table-hover"></table>

<?php modal('/WXActGod/assign'); ?>
<?php modal('/WXActGod/reject'); ?>
<?php modal('/WXActGod/upload'); ?>

<script>
var table = null;
$(function() {
    table = new Datatable();
    table.init({
        src: $("#activity_table"),
        dataTable: {
            "columns": [
                { data: "id", name: "#" },
                { data: "uid", name: "用户ID" },
                { data: "create_time", name: "上传时间"},
                { data: "request_image", name: "求P原图"},
                { data: "request", name: "求P需求"},
                { data: "received_status", name: "领取状态"},
                { data: "reply_image", name: "作品"},
                { data: "request_status", name: "处理状态"},
                { data: "oper", name: "操作"}
            ],
            "ajax": {
                "url": "/WXActGod/list_requests"
            }
        },

        success: function(){
        },
    });
    $('#activity_table').on('click', '.db_oper a[data-toggle="modal"]', function(){
        var tr = $(this).parents('tr');
        var target_id = tr.find('td.db_id').text();
        var modal_name = $(this).attr('href');
        var modal = $(modal_name);
        modal.find('form input[name="target_id"]').val( target_id );
    });
    $('ul.breadcrumb').on('click', '.online, .offline, .undelete',function(){
        var btn = $(this);
        var postData = {
            'status': btn.attr( 'data-status' )
        };

        $.post( '/WXActGod/update_status', postData, function( res ){
            if( res.data.result == 'ok' ){
                table && table.submitFilter();
            }
        });
    });

    $.post('/WXActGod/get_designers#asdasd=ads',{'type':'puppets'}, function( data ){
        data = data.data;
        var select = $('#operator');
        select.empty();
        select.append($('<option>').val('').text('全部'));
        $.each( data, function( i, n ){
            var option = $('<option>').val( n.uid ).text( n.nickname+'(uid:'+n.uid+')' );
            select.append( option );
            select.select2();
        });
    });
});

</script>
