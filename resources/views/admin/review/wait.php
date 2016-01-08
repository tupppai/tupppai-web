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
      <li class="active">
        <a href="wait">
          待审核 </a>
      </li>
      <li>
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

<ul id="review-data"></ul>
<div id="navigation"><a href="/review/list_reviews?page=1"></a></div>

<?php modal('/review/review_item'); ?>

<script>
jQuery(document).ready(function() {

    flow = new Endless();
    flow.init({
        src: $('#review-data'),
        url: "/review/list_reviews?status=-5",
        template: _.template($('#review-item-template').html()),
        success: function() {
        }
    });
});
</script>
