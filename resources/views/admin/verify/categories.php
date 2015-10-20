
<script type="text/javascript" src="/main/vendor/node_modules/underscore/underscore-min.js"></script>

<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>多图审核</li>
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

<div id="thread-data"></div>

<?php modal('/verify/check_item'); ?>
<?php modal('/verify/reply_comment'); ?>


<script>
var table = null;
jQuery(document).ready(function() {

    table = new Paginate();
    table.init({
        src: $('#thread-data'),
        url: '/verify/list_threads?type=unreviewed',
        template: _.template($('#thread-item-template').html()),
        success: function() {
        }
    });
});

$(document).ready(function(){
    $('#thread-data').on('click', '.chg_stat', function(){
        var par = $(this).parents('div.photo-container-admin');
        var target_type = par.attr('data-target-type');
        var target_id = par.attr('data-target-id');
        //var status = Number($(this).attr('data-status')) > 0 ? -1 : 1;
        var status = 1;//通过的状态
        var data = {
            'target_id': target_id,
            'target_type': target_type,
            'status' : status
        };

        $.post( '/verify/set_thread_status', data, function( data ){
            data=data.data;
            if( data.result == 'ok' ){
                table.submitFilter();
            }
        } );

    });

    $('#thread-data').on('click', '.categorize', function(){

    });
});
</script>
