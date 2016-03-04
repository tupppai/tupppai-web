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
        <input name="activity_id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <input name="activity_display_name" class="form-filter form-control" placeholder="活动名称">
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
    $('ul.breadcrumb').on('click', '.online, .offline',function(){
        var btn = $(this);
        var postData = {
            'id': btn.attr( 'data-id' ),
            'status': btn.attr( 'data-status' )
        };

        $.post( '/category/update_status', postData, function( res ){
            if( res.data.result == 'ok' ){
                table && table.submitFilter();
            }
        });
    });
});

</script>
