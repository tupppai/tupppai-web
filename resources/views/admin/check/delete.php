
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
      <li>
        <a href="/check/reject">
          审核拒绝</a>
      </li>
      <li class="active">
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
                { data: "author", name: "兼职用户昵称" },
                { data: "create_time", name:"任务分配时间"},
                { data: "ask", name:"求助内容"},
                { data: "refuse_reason", name: "拒绝原因"},
            ],
            "ajax": {
                "url": "list_works?type=refused",
            }
        },
        success: function(data){},
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
