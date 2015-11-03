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

<ul id="review-data"></ul>

<?php modal('/review/review_item'); ?>
<button class="online">生效</button>
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


    $('.online').on('click', function(){
        var ids = [];
        $('.admin-card-container').each(function(i,n){
            ids.push( $(this).attr('data-id') );
        });
        $.post('/review/set_status', {'review_ids': ids, 'status': -1}, function( data ){
            console.log( data );
        });
    });
});
</script>
