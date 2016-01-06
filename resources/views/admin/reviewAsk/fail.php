
<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>审核作品</li>
</ul>

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
          待编辑</a>
      </li>
      <li>
        <a href="pass">
          待生效</a>
      </li>
      <li class="active">
        <a href="fail">
          已失效</a>
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
                { data: "uid", name: "ID" },
                { data: "nickname", name: "昵称" },
                { data: "create_time", name: "时间" },
                { data: "desc", name:"求助内容"},
                { data: "image_view", name:"求助图片"},
                // { data: "reply_image", name:"回复内容"},
                // { data: "evaluation", name: "拒绝理由"}
            ],
            "ajax": {
                "url": "/reviewAsk/list_reviews?status=-3&type=1"
            }
        },
        success: function(data){
            $(".edit").click(function(){

            });
        },
    });
});
</script>
