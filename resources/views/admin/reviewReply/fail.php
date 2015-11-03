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
        <input name="role_created_beg" class="form-filter form-control" placeholder="开始时间">
        <input name="role_created_end" class="form-filter form-control" placeholder="结束时间">
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
          预览生效</a>
      </li>
      <li class="active">
        <a href="fail">
          失败</a>
      </li>
      <li>
        <a href="release">
          已发布</a>
      </li>
    </ul>
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
                { data: "id", name: "ID"},
                { data: "avatar", name: "马甲头像"},
                { data: "uid", name: "马甲ID" },
                { data: "nickname", name: "马甲昵称" },
                { data: "desc", name: "作品描述" },
                { data: "image_view", name: "作品" },
                { data: "execute_time", name: "发布时间" },
            ],
            "ajax": {
                "url": "/reviewReply/list_reviews?status=-3"
            }
        },
        success: function(data){
            $(".edit").click(function(){

            });
        },
    });
});
</script>
