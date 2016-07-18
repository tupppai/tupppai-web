<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>审核作品</li>
</ul>

<?php include "search_user.php" ?>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li class="active">
        <a href="/check/wait">
          待审核 </a>
      </li>
      <li>
        <a href="/check/pass">
         审核通过 </a>
      </li>
      <li>
        <a href="/check/reject">
          审核拒绝</a>
      </li>
      <li>
        <a href="/check/delete">
          已删除</a>
      </li>
</div>
<?php modal("/check/preview"); ?>
<?php modal("/help/reward"); ?>
<table class="table table-bordered table-hover" id="check_ajax"></table>
<?php modal("/check/evaluation"); ?>

<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-flexselect/css/flexselect.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-flexselect/js/jquery.flexselect.js" type="text/javascript"></script>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-flexselect/js/liquidmetal.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $theme_dir; ?>assets/global/plugins/select2/select2.css"/>

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Datatable();
    table.init({
        dom: "<t><'row'<'col-md-5 col-sm-12'li><'col-md-7 col-sm-12'p>>",
        src: $("#check_ajax"),
        dataTable: {
            "ordering": false,
            "columns": [
                { data: "id", name: "ID" },
                { data: "oper", name: "操作"},
                { data: "author", name: "姓名" },
                { data: "reply_upload_time", name:"发布时间"},
                { data: "ask", name:"求助"},
                { data: "reply", name:"作品内容"}
            ],
            "ajax": {
                "url": "list_works?type=done"
            }
        },
        success: function(data){
            $('select.flexselect').flexselect({
                allowMismatch:true,
                allowMismatchBlank:false,
                preSelection:false
            }).siblings('input.flexselect').attr('placeholder','拒绝理由');

            $(".deny").click(function(){
                var reply_id = $(this).attr("data");
                $("#modal_evaluation").attr("data", reply_id);
            });

            // $(".quick-deny").click(function(){
            //     var obj = {};
            //     obj.reply_id = $(this).attr('data');
            //     obj.status   = 2;
            //     obj.data     = $(this).text();

            //     $.post("set_status", obj, function(data){
            //         toastr['success']("操作成功");
            //         table.submitFilter();
            //     });
            // });
            $('.reject_btn').on('click', function(){
                var obj = {};
                obj.aid = $(this).attr('data-aid');
                obj.score = 0;
                obj.reason  = $(this).parents('td').find('input.flexselect').val().replace(/(\d{1,}\.)/,'');

                $.post("/check/verify_task", obj, function(data){
                    if( data.code == 0 ){
                        toastr['success']("操作成功");
                        table.submitFilter();
                    }
                    else{
                        toastr['error']('操作失败');
                    }
                });

            });

            $(".score").click(function(){
                var obj = {};
                obj.aid = $(this).attr('data-aid');
                obj.score = $(this).attr('data-score');
                obj.amount = $(this).attr('data-score');

                $.post("/check/verify_task", obj, function(data){
                    if( data.code == 0 ){
                        toastr['success']("操作成功");
                        table.submitFilter();
                    }
                    else{
                        toastr['error']('操作失败');
                    }
                });
            });
        }
    });

    $('#check_ajax').on('click', '.preview_link', function(e){
        e.preventDefault();
        var src = $(this).children('img').attr('src');
        var prv_modal = $('#preview_modal');
        prv_modal.find('#preview_image').attr('src', src);
        prv_modal.find('#preview_image').css('width', '500px');
        prv_modal.find('#preview_image').css('height', 'auto');
        prv_modal.modal("show");

        return false;
    });


    $('#check_ajax').on('show.bs.collapse','.collapse.pass',function(){
        var aid = $(this).attr('data-aid');
        var select = $('#reward_uid_'+aid);

        if( !select.find('option').length ){
            $.post('/puppet/get_puppets',{'type':'puppets'}, function( data ){
                data = data.data;
                $.each( data, function( i, n ){
                    var option = $('<option>').val( n.uid ).text( n.nickname+'(uid:'+n.uid+')' );
                    select.append( option );
                });

                select.select2();
            });
        }
    });

    $('#check_ajax').on('click','.reward_work', function( e ){
        var crnt_reward = $(this).parents('.collapse');
        var aid = crnt_reward.attr('data-aid');
        var grade = crnt_reward.find('.reward_amount').val();
        var reward_uid = crnt_reward.find('select[name="reward_uid"]').val();

        var postData = {
            'aid': aid,
            'score': grade,
            'amount': grade,
            'from_uid': reward_uid
        };

        $.post('/check/verify_task', postData, function( data ){
            if( data.code == 0 ){
                toastr['success']('审核成功');
                table.submitFilter();
            }
        });
    });


});
</script>
<style>
.db_stat{
    min-width: 110px;
}
.db_create_time{
    min-width: 80px;
}
.db_oper{
    min-width: 200px;
}
</style>
