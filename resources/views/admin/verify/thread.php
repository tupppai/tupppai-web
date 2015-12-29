<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/underscore/underscore-min.js"></script>

<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>多图审核</li>
  <div class="btn-group pull-right">
       <span>昨日发帖数：<?php echo $yesterday_count; ?></span>
       <span>总帖数：<?php echo $total_count; ?></span>
   </div>
</ul>
<div id="search_form">
<div class="form-inline">
    <div class="form-group">
        <label for="target_type">类型:</label>
        <select class="form-filter form-control" name="target_type" id="target_type">
          <option value="all">全部</option>
          <option value="ask">求助</option>
          <option value="reply">作品</option>
        </select>
    </div>
    <div class="form-group">
        <label for="thread_type">帖子类型:</label>
        <select class="form-filter form-control" name="thread_type" id="thread_type">
          <option value="all">全部</option>
          <option value="hot">热门推荐</option>
          <option value="blocked">屏蔽内容</option>
        </select>
    </div>
    <div class="form-group">
        <label for="user_type">用户标签:</label>
        <select class="form-filter form-control" name="user_type" id="user_type">
          <option value="all">全部</option>
          <option value="stars">明星用户</option>
          <option value="rec_stars">推荐明星用户</option>
          <option value="blacklist">黑名单用户</option>
          <option value="rec_blacklist">推荐黑名单用户</option>
        </select>
    </div>
    <div class="form-group">
        <label for="user_role">用户角色:</label>
        <select class="form-filter form-control" name="user_role" id="user_role">
          <option value="all">全部</option>
          <option value="newbie">新用户</option>
          <option value="general">一般用户</option>
          <option value="trustable">信任用户</option>
          <option value="blocked">屏蔽用户</option>
        </select>
    </div>
</div>
<div class="form-inline">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
    </div>
    <div class="form-group">
        <input name="thread_id" class="form-filter form-control" placeholder="帖子ID">
    </div>
    <div class="form-group">
        <input name="desc" class="form-filter form-control" placeholder="描述">
    </div>
    <div class="form-group">
        <input name="nickname" class="form-filter form-control" placeholder="昵称">
    </div>
    <div class="form-group">
        <input name="tag_name" class="form-filter form-control" placeholder="标签">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>
</div>

<div class="pagination" id="thread-data"></div>

<?php modal('/verify/thread_item'); ?>
<?php modal('/verify/reply_comment'); ?>
<?php modal('/verify/up_item'); ?>

<script>
var pc_host = '<?php echo $pc_host; ?>';
var table = null;
jQuery(document).ready(function() {
    table = new Paginate();
    table.init({
        display: 15, //size
        src: $('#thread-data'),
        url: '/verify/list_threads',
        template: _.template($('#thread-item-template').html()),
        success: function() {
            // initialize sol
            $('select[name="user-roles"]').multiselect({
                nonSelectedText: '无角色'
            });
            $('select[name="th_cats"]').multiselect({
                nonSelectedText: '无分类',
                // enableFiltering: true
            });
        }
    });

    $('#thread-data').on('change', 'select[name="user-roles"]', function(){
        var role_id = $(this).val();
        var par = $(this).parents('div.photo-container-admin');
        var uid = par.find('.user-id').attr('data-uid');
        $.post('/user/assign_role', {'user_id': uid, 'role_id[]': role_id}, function( data ){
            data=data.data;
            if( data.result == 'ok' ){
                table.submitFilter();
            }
        })
    });

    $('#thread-data').on('click', '.chg_user_stat', function(){
        var par = $(this).parents('div.photo-container-admin');
        var uid = par.find('.user-id').attr('data-uid');
        var status = Number($(this).attr('data-status')) > 0 ? -1 : 1;
        $.post('/user/block_user', { 'uid': uid, 'status': status }, function( data ){
            data=data.data;
            if( data.result == 'ok' ){
                table.submitFilter();
            }
        });
    });

    $('#thread-data').on( 'click', '.shield-cantent', function(){
        var par = $(this).parents('div.photo-container-admin');
        var target_type = par.attr('data-target-type');
        var target_id = par.attr('data-target-id');
        var status = ( Number(par.attr('data-status')) == 1  )? -6 : 1;

        var data = {
            'target_type': target_type,
            'target_id': target_id,
            'status': status
        };
        $.post('/verify/set_thread_status', data, function( data ){
            data=data.data;
            if( data.result == 'ok' ){
                table.submitFilter();
            }
        });
    });


    $('#thread-data').on('click','.master', function(){
        var par = $(this).parents('div.photo-container-admin');
        var uid = par.find('.user-id').attr('data-uid');
        var status = $(this).attr('data-isgod');
        var t;
        if( status == 'true' ){
            status = 0;
            t = '取消';
        }
        else{
            status = 1;
            t = '设置';
        }

        //if(confirm("确认"+t+"大神?")) {
            $.post('/personal/set_master',{ 'uid': uid, 'status': status }, function( data ){
                data = data.data;
                if( data.result == 'ok' ){
                    table.submitFilter();
                }
            });
        //}
    });

    $('#thread-data').on('click', '.comment_thread, .up_thread', function(){
        var form = $($(this).attr('href')).find('form');
        var par = $(this).parents('.photo-container-admin');

        var target_type = par.attr( 'data-target-type' );
        var target_id = par.attr('data-target-id');

        form.find( 'input[name="target_type"]' ).val( target_type );
        form.find( 'input[name="target_id"]' ).val( target_id );
    });

    $('#thread-data').on('click', '.popularize', function(){
        var par = $(this).parents('div.photo-container-admin');
        var target_type = par.attr('data-target-type');
        var target_id = par.attr('data-target-id');
        var status = 'checked';

        var data = {
            'target_id': target_id,
            'target_type': target_type,
            'type': 'popular',
            'status' : status
        };

        $.post( '/verify/set_thread_as_pouplar', data, function( data ){
            data=data.data;
            if( data.result == 'ok' ){
                toastr['success']('设置成功');
                table.submitFilter();
            }
        } );
    });

    $('#thread-data').on( 'click', '.recommend', function(){
        var p        = $(this).parents('.photo-container-admin');
        var uid      = p.find('.user-id').attr('data-uid');
        var role     = p.find('.recommend_role').val();
        var reason   = p.find('input[name="reason"]').val();
        var postData = {
            'uid': uid,
            'reason': reason,
            'role_id': role
        };
        $.post('/recommendation/user', postData, function( data ){
            data = data.data;
            if( data.result == 'ok' ){
                toastr['success']('推荐成功');
                location.reload();
            }
        });
    } );

    $('#search').on('click', function(){
        var form = $('#search_form').find('.form-inline');
        var postData = {
            'target_type' : form.find('[name="target_type"]').val(),
            'thread_type' : form.find('[name="thread_type"]').val(),
            'user_type'   : form.find('[name="user_type"]').val()  ,
            'user_role'   : form.find('[name="user_role"]').val()  ,
            'uid'         : form.find('[name="uid"]').val()        ,
            'thread_id'   : form.find('[name="thread_id"]').val()  ,
            'desc'        : form.find('[name="desc"]').val()       ,
            'nickname'    : form.find('[name="nickname"]').val()   ,
            'tag_name'    : form.find('[name="tag_name"]').val()   ,
        };
        $.post('/verify/list_threads', postData, function( data ){
            table.submitFilter();
        });
    });


    $("#thread-data").on('click',".category", function(){
        var obj = {};
        obj.target_type = 1; //ask
        obj.target_id = $(this).attr('ask_id');
        obj.category  = $(this).attr('category_id');
        obj.category_from  = $(this).attr('category_id');
        obj.status    = $(this).attr("data-isActivity")=='true'?0: 1;

        $.post("/verify/set_thread_category", obj, function(data){
            toastr['success']("操作成功");
            table.submitFilter();
        });
    });

    $("#thread-data").on('click',".tags", function(){
        var obj = {};
        obj.tag_id      = $(this).attr('data-id');
        obj.target_type = $(this).attr('data-target-type');
        obj.target_id   = $(this).attr('data-target-id');

        $.post("/verify/set_thread_tag", obj, function(data){
            toastr['success']("操作成功");
            table.submitFilter();
        });
    });

    $('#thread-data').on('click', '.save_category', function(){
        var cat_ids = Array();
        var photoMain = $(this).parents('.photo-container-admin');
        var target_id = photoMain.attr('data-target-id');
        var target_type = photoMain.attr('data-target-type');
        var thcat = photoMain.find('select[name="th_cats"]~div');

        photoMain.find('select[name="th_cats"] option:selected').each(function( i, n ){
            var val = $(n).val();
            thcat.find('input[type="checkbox"][value="'+ val +'"]').prop('disabled', 'disabled');
        });
        cat_ids = cat_ids.join(',');

        var postData = {
            'target_id': target_id,
            'target_type': target_type,
            'category': cat_ids,
            'status': 'checked'
        };

        $.post('/verify/set_thread_category', postData, function( data ){
            data = data.data;
            if( data.result == 'ok' ){
                toastr['success']('设置分类成功');
                photoMain.find('li.cat_ids span:contains(×)').remove();
                //todo: refetch categories
                //table.submitFilter();
            }
        });
    });
});
</script>

<link href="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-multiselect/bootstrap-multiselect.min.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript"></script>
