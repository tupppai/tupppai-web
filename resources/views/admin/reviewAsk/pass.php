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
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</form>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li>
        <a href="wait">
          待编辑</a>
      </li>
      <li class="active">
        <a href="pass">
          待生效</a>
      </li>
      <li>
        <a href="fail">
          已失效</a>
      </li>
      <li>
        <a href="release">
          已发布</a>
      </li>
</div>
<label for="selectAll">
    <input type="checkbox" name="selectAll" id="selectAll" checked="checked"/>全选
</label>

<div id="review-data"></div>
<button class="btn btn-danger delete" style="width: 25%">删除</button>
<button class="btn btn-info restore" style="width: 25%">恢复</button>

<?php modal('/review/review_item'); ?>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-multiselect/bootstrap-multiselect.min.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript"></script>

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Paginate();
    table.init({
        src: $('#review-data'),
        url: "/reviewAsk/list_reviews?status=-1&type=1",
        template: _.template($('#review-item-template').html()),
        success: function() {
            $("select[name='puppet_uid']").attr('disabled', true);
            $('select[name="th_cats[]"]').multiselect({
                nonSelectedText: '无分类',
                // enableFiltering: true
            });
        }
    });


    $('.delete').on('click', function(){
        var ids = [];
        $('.admin-card-container input[name="confirm_online"]:checked').each(function(i,n){
            ids.push( $(this).parents('.admin-card-container').attr('data-id') );
        });
        $.post('/review/set_status', {'review_ids': ids, 'status': 0}, function( data ){
            if( data.data.result == 'ok' ){
              location.reload();
            }
        });
    });

    $('.restore').on('click', function(){
        var ids = [];
        $('.admin-card-container input[name="confirm_online"]:checked').each(function(i,n){
            ids.push( $(this).parents('.admin-card-container').attr('data-id') );
        });
        var postData = {
          'review_ids': ids,
          'status': -5
        };
        $.post('/review/set_status', postData, function( data ){
            if( data.data.result == 'ok' ){
              location.reload();
            }
        });
    });

    $('#selectAll').on('click', function(){
        var all = $(this).prop('checked');
        var checkboxes = $('.admin-card-container input[name="confirm_online"]');
        if( all ){
            checkboxes.prop('checked', 'checked');
        }
        else{
            checkboxes.removeProp('checked');
        }
    });
});
</script>
