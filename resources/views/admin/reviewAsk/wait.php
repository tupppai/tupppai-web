<link href="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.js" type="text/javascript"></script>
<script type="text/javascript" src="/theme/assets/global/plugins/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="/theme/assets/global/plugins/select2/select2.css"/>
<style>
.db_upload_view { position: relative; width: 120px; }
.uploadify { left: 60px; top: 10px; }
.user-portrait { left: 10px; position: absolute; }
</style>


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
      <li class="active">
        <a href="wait">
          待编辑</a>
      </li>
      <li>
        <a href="pass">
          待生效</a>
      </li>
      <li>
        <a href="reject">
          已失效</a>
      </li>
      <li>
        <a href="release">
          已发布</a>
      </li>
</div>

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
            var select = $("select[name='puppet_uid']");

            _.each(select, function(row) {
                var length = $(row).find("option").length;
                var index  = parseInt(Math.random()*length);
                var value  = $(row).find("option:eq("+index+")").attr("value");
                select.val(value==""?1:value);
                $(row).select2();
            });

            $('input[name="release_time"]').datetimepicker({
                lang: 'ch',
                format: 'Y-m-d H:m', 
                value: new Date().Format("yyyy-MM-dd hh:mm:ss")
            });
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
