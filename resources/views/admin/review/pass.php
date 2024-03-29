
<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>发布管理</li>
</ul>
<div class="btn-group pull-right">
    <ul class="dropdown-menu pull-right" role="menu">
    <li>
    <a href="#">Action</a>
    </li></ul>
</div>

<div class="form-inline">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
    </div>
    <div class="form-group">
        <input name="username" class="form-filter form-control" placeholder="名称">
    </div>
    <div class="form-group">
        <input name="nickname" class="form-filter form-control" placeholder="昵称">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>
<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li>
        <a href="wait">
          待审核 </a>
      </li>
      <li class="active">
        <a href="pass">
         审核通过 </a>
      </li>
      <li>
        <a href="reject">
          审核拒绝</a>
      </li>
      <li>
        <a href="release">
          已发布</a>
      </li>
</div>

<table class="table table-bordered table-hover" id="review_ajax"></table>

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Datatable();
    table.init({
        src: $("#review_ajax"),
        dataTable: {
            "columns": [
                { data: "id", name: "ID" },
                { data: "time", name:"时间"},
                { data: "parttime_name", name: "昵称" },
                //{ data: "create_time", name:"创建时间"},
                //{ data: "release_time", name:"发布时间"},
                //{ data: "ask_image", name:"求助内容"},
                //{ data: "reply_image", name:"回复内容"},
                { data: "image_view", name:"求助/回复内容"},
                { data: "oper", name:"操作"}
                //{ data: "score", name: "积分"}
            ],
            "ajax": {
                "url": "/review/list_reviews?status=1"
            }
        },
        success: function(data){
            $(".pass").click(function(){

            });

            $(".del").click(function(){
                var target_id   = $(this).attr("data");
                if(confirm("确认删除作品?")){
                    $.post("/review/set_status", {
                        review_id: target_id,
                        status: 0
                    }, function(){
                        toastr['success']("删除成功");
                        table.submitFilter();
                    });
                }
            });

        }
    });
});
</script>
