
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
<table class="table table-bordered table-hover" id="check_ajax"></table>
<?php modal("/check/evaluation"); ?>

<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-flexselect/css/flexselect.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-flexselect/js/jquery.flexselect.js" type="text/javascript"></script>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-flexselect/js/liquidmetal.js" type="text/javascript"></script>

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Datatable();
    table.init({
        dom: "<t><'row'<'col-md-5 col-sm-12'li><'col-md-7 col-sm-12'p>>",
        src: $("#check_ajax"),
        dataTable: {
            "columns": [
                { data: "id", name: "ID" },
                { data: "oper", name: "操作"},
                { data: "author", name: "姓名" },
                { data: "reply_upload_time", name:"发布时间"},
                { data: "ask", name:"求助"},
                { data: "reply", name:"作品内容"}
                //{ data: "delete", name:"删除作品"}
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

            $(".quick-deny").click(function(){
                var obj = {};
                obj.reply_id = $(this).attr('data');
                obj.status   = 2;
                obj.data     = $(this).text();

                $.post("set_status", obj, function(data){
                    toastr['success']("操作成功");
                    table.submitFilter();
                });
            });
            $('.reject_btn').on('click', function(){
                var obj = {};
                var row = $(this).parents('tr');
                obj.reply_id = row.find('td.db_id').text();
                obj.status   = 2;
                obj.data     = $(this).parents('td').find('input.flexselect').val().replace(/(\d{1,}\.)/,'');

                $.post("set_status", obj, function(data){
                    toastr['success']("操作成功");
                    table.submitFilter();
                });

            });

            $(".score").click(function(){
                var obj = {};
                obj.aid = $(this).attr('data-aid');
                obj.score = $(this).attr('data-score');

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

});
</script>
<style>
.db_stat{
    min-width: 110px;
}
.db_create_time{
    min-width: 80px;
}
td.db_oper div {
    text-align: left;
}
</style>
