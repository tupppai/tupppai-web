
<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>审核作品</li>
</ul>

<?php include "search_user.php"; ?>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li>
        <a href="/check/wait">
          待审核 </a>
      </li>
      <li>
        <a href="/check/pass">
         审核通过 </a>
      </li>
      <li class="active">
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

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Datatable();
    table.init({
        src: $("#check_ajax"),
        dataTable: {
            "columns": [
                { data: "reply_id", name: "作品ID" },
                { data: "auditor", name: "审核人"},
                //{ data: "oper", name: "操作"},
                { data: "author", name: "姓名" },
                { data: "reply_upload_time", name:"发布时间"},
                { data: "reply", name:"作品内容"},
                { data: "grade_reason", name: "拒绝原因"},
            ],
            "ajax": {
                "url": "list_works?type=rejected",
            }
        },
        success: function(data){ },
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
