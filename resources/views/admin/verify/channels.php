<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>内容分类</li>
</ul>

<div id="search_form">
    <div class="form-inline">
<!--         <div class="form-group">
            <input name="uid" class="form-filter form-control" placeholder="账号ID">
        </div>
        <div class="form-group">
            <input name="nickname" class="form-filter form-control" placeholder="昵称">
        </div>
 -->
        <div class="form-group">
            <select name="category_ids" class="form-filter form-control">
                <option value="">所有频道</option>
                <?php
                    $catId = isset( $_REQUEST['category_ids'] ) ? $_REQUEST['category_ids'] : NULL;
                    foreach( $channels as $channel ):
                ?>
                    <option value="<?php echo $channel['id']; if( $catId == $channel['id']): echo '" selected="selected'; endif;?>"><?php echo $channel['display_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="form-filter form-control" id="search" >搜索</button>
        </div>
    </div>
</div>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li class="all">
        <a href="?">待审核</a>
      </li>
      <li class="view_threads">
        <a href="/verify/channels?type=valid">全部</a>
      </li>
    </ul>
</div>
<div class="tabbable-line">
    <label for="selectAll">
        <input type="checkbox" name="selectAll" id="selectAll" checked="checked"/>全选
    </label>
</div>


<div id="thread-data"></div>


<button class="btn btn-danger delete" style="width: 20%">拒绝</button>
<button class="btn btn-info online" style="width: 20%">通过</button>

<?php modal('/verify/category_item'); ?>

<style>
    .photo-container-admin{
        margin-top: 10px;
        margin-bottom: 10px;
        border: 1px solid transparent;
    }
    .photo-container-admin:hover{
        border: 1px solid steelblue;
    }
    .set-value label{
        padding: 0;
    }
    .photo-main{
        border-top:1px solid lightgray;
        border-bottom:1px solid lightgray;
    }
</style>
<script>
var table = null;
var category_ids;
var type;
var pc_host = '<?php echo $pc_host; ?>';

jQuery(document).ready(function() {
    category_ids = getQueryVariable('category_ids', '');
    type = getQueryVariable('type', 'checked');
    console.log(type);
    if( type == 'valid' ){
        $('li.view_threads').addClass('active');
    }
    else{
        $('li.all').addClass('active');
    }

    table = new Paginate();
    table.init({
        src: $('#thread-data'),
        url: '/verify/list_channel_threads?category_ids='+category_ids+'&category_type=channels&status='+type,
        template: _.template($('#thread-item-template').html()),
        success: function() {
        },
        display:15
    });

    $('#selectAll').on('click', function(){
        var all = $(this).prop('checked');
        var checkboxes = $('.photo-container-admin input[name="confirm_online"]');
        if( all ){
            checkboxes.prop('checked', 'checked');
        }
        else{
            checkboxes.removeProp('checked');
        }
    });

    $('#search').on('click', function(){
        var form = $('#search_form');
        var postData = {
            'uid'         : form.find('[name="uid"]').val()      ,
            'nickname'    : form.find('[name="nickname"]').val() ,
            'category_ids'  : form.find('[name="category_ids"]').val(),
            'category_type' : 'channels',
            'status': type
        };
        $.post('/verify/list_channel_threads', postData, function( data ){
            table.submitFilter();
        });
    });

     $('.online, .delete').on('click', function(){
        var target_types = [];
        var target_ids = [];
        var categories = [];
        var statuses = [];
        $('input[name="confirm_online"]:checked').each(function( i, n ){
            var p = $(this).parents('.photo-container-admin');
            target_types.push( p.attr('data-target-type') );
            target_ids.push( p.attr('data-target-id') );
            categories.push( p.attr('data-category-id') );
        });

        if( $(this).hasClass('delete') ){
            status = 'delete';
        }
        else if( $(this).hasClass('online') ){
            status = 'online';
        }
        var postData = {
            'target_id[]': target_ids,
            'target_type[]': target_types,
            'category_id[]': categories,
            'status[]': status
        };

        $.post('/verify/set_thread_category_status', postData, function( data ){
            if( data.data.result == 'ok' ){
              table.submitFilter();
            }
        });
    });

    $( '#thread-data').on('click','.remove_from_category', function(){
        var box = $(this).parents('.photo-container-admin');
        var postData = {
            'target_id[]': box.attr('data-target-id'),
            'target_type[]': box.attr('data-target-type'),
            'category_id[]': box.attr('data-category-id'),
            'status[]': 'delete'
        };

        $.post('/verify/set_thread_category_status', postData, function( data ){
            if( data.data.result == 'ok' ){
              table.submitFilter();
            }
        });
    });

});
</script>
