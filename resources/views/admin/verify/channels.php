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
            <select name="category_id" class="form-filter form-control">
                <option value="">所有频道</option>
                <?php
                    $catId = isset( $_REQUEST['category_id'] ) ? $_REQUEST['category_id'] : NULL;
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
        <a href="/verify/channels?status=valid">全部</a>
      </li>
    </ul>
</div>
<div class="tabbable-line">
    <label for="selectAll">
        <input type="checkbox" name="selectAll" id="selectAll"/>全选
    </label>
    <span class="thread_category normal">█</span>正在生效(用户自己添加)
    <span class="thread_category verifing">█</span>待审核
    <span class="thread_category verified">█</span>已审核
    <span class="thread_category deleted">█</span>已拒绝
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
    .remove_from_category{
        cursor: pointer;
    }

    .thread_category.normal{ color: dodgerblue; }
    .thread_category.verifing{ color: darkkhaki; }
    .thread_category.verified{ color: lightgreen; }
    .thread_category.deleted{ color: magenta;  text-decoration: line-through; }
</style>
<script>
var table = null;
var category_id;
var status;
var pc_host = '<?php echo $pc_host; ?>';

jQuery(document).ready(function() {
    category_id = getQueryVariable('category_id', '');
    status = getQueryVariable('status', 'checked');
    if( status == 'valid' ){
        $('li.view_threads').addClass('active');
        $('#thread-data~button.btn').hide();
        $('label[for="selectAll"]').hide();
    }
    else{
        $('li.all').addClass('active');
    }

    table = new Paginate();
    table.init({
        src: $('#thread-data'),
        url: '/verify/list_category_threads?category_id='+category_id+'&category_type=channels&status='+status,
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
            'category_id'  : form.find('[name="category_id"]').val(),
            'category_type' : 'channels',
            'status': status
        };
        $.post('/verify/list_category_threads', postData, function( data ){
            table.submitFilter();
        });
    });

     $('.online, .delete').on('click', function(){
        var target_types = [];
        var target_ids = [];
        var categories = [];
        var statuses = [];
        var thread_status;
        $('input[name="confirm_online"]:checked').each(function( i, n ){
            var p = $(this).parents('.photo-container-admin');
            target_types.push( p.attr('data-target-type') );
            target_ids.push( p.attr('data-target-id') );
            categories.push( p.attr('data-category-id') );
        });

        if( $(this).hasClass('delete') ){
            thread_status = 'delete';
        }
        else if( $(this).hasClass('online') ){
            thread_status = 'done';
        }
        var postData = {
            'target_id[]': target_ids,
            'target_type[]': target_types,
            'category_id[]': categories,
            'status': thread_status
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
            'status': 'delete'
        };

        $.post('/verify/set_thread_category_status', postData, function( data ){
            if( data.data.result == 'ok' ){
              table.submitFilter();
            }
        });
    });

});
</script>
