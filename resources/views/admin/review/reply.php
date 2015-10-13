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
    <div class="hidden">
        <input class="form-filter" type="hidden" name="type" value="1" />
        <input class="form-filter" type="hidden" name="status" value="<?php echo $status; ?>" />
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<?php
$data = array(
    -5=>'待审核',
    -1=>'预发布',
    -3=>'审核拒绝',
     1=>'已发布'
);
echo '<div class="tabbable-line"><ul class="nav nav-tabs">';
foreach($data as $key=>$val){
    if($key == $status)
        echo '<li class="active"><a href="?status='.$key.'">'.$val.'</a></li>';
    else 
        echo '<li><a href="?status='.$key.'">'.$val.'</a></li>';
}
echo '</div></ul>';
?>

<table class="table table-bordered table-hover" id="review_ajax"></table>

<?php modal('/review/review_item'); ?>

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Datatable();
    table.init({
        src: $("#review_ajax"),
        dataTable: {
            "columns": [
                { data: "checkbox", name: "全选<input type='checkbox'/>", orderable: false },
                { data: "avatar", name: "用户头像"},
                { data: "nickname", name: "用户昵称" },
                { data: "uid", name: "用户ID" },
                { data: "image_view", name: "原图" },
                { data: "desc", name: "描述" },
                { data: "puppet_uid", name: "马甲账号" },
                { data: "upload_id", name: "上传作品" },
                { data: "puppet_desc", name: "描述" },
                { data: "release_time", name: "发布时间" },
            ],
            "ajax": {
                "url": "/review/list_reviews"
            }
        },
        success: function(data){

        }
    });
});
</script>
