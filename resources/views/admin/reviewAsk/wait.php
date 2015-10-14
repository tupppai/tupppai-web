<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>审核作品</li>
</ul>

<form class="form-inline">
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
    <div class="hidden">
        <input class="form-filter" type="hidden" name="type" value="1" />
        <input class="form-filter" type="hidden" name="status" value="<?php echo $status; ?>" />
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</form>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li>
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
      <li class="active">
        <a href="release">
          已发布</a>
      </li>
</div>

<ul id="review-data"></ul>

<?php modal('/review/review_item'); ?>

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Paginate();
    table.init({
        src: $('#review-data'),
        url: "/review/list_reviews",
        template: _.template($('#review-item-template').html()),
        success: function() {
        }
    });
});
</script>
