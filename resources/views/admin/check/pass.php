<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>审核作品</li>
</ul>
<div class="btn-group pull-right">
    <ul class="dropdown-menu pull-right" role="menu">
    <li>
    <a href="#">Action</a>
    </li></ul>
</div>

<?php include "search_user.php" ?>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li>
        <a href="/check/wait">
          待审核 </a>
      </li>
      <li class="active">
        <a href="/check/pass">
         审核通过 </a>
      </li>
      <li>
        <a href="/check/reject">
          审核拒绝</a>
      </li>
<!--
      <li>
        <a href="/check/release">
          已发布</a>
      </li>
-->
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
                { data: "grade", name: "分数"},
                { data: "auditor", name: "审核人"},
                { data: "author", name: "兼职用户昵称" },
                { data: "update_time", name:"审核时间"},
                { data: "reply", name:"作品内容"},
                { data: "reply_upload_time", name:"作品上传时间"},
                //{ data: "desc", name:"描述"}
                //{ data: "delete", name:"删除作品"}
            ],
            "ajax": {
                "url": "list_works?type=checked"
            },
        },
        success: function(data){ }
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
