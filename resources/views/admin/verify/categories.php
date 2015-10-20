
<script type="text/javascript" src="/main/vendor/node_modules/underscore/underscore-min.js"></script>

<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>多图审核</li>
</ul>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li class="all active">
        <a href="/verify/categories">审核库</a>
      </li>
      <li class="hot">
        <a href="/verify/categories?type=pending">热门</a>
      </li>
      <li class="pc_hot">
        <a href="/verify/categories?type=sent">PC热门</a>
      </li>
    </ul>
</div>

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
<label for="selectAll">
    <input type="checkbox" name="selectAll" id="selectAll" checked="checked"/>全选
</label>
<div id="thread-data"></div>

<?php modal('/verify/check_item'); ?>
<?php modal('/verify/reply_comment'); ?>

<button class="btn btn-danger delete" style="width: 20%">拒绝</button>
<button class="btn btn-success update" style="width: 20%">确定</button>
<style>
    .photo-container-admin{
        border: 1px solid transparent;
    }
    .photo-container-admin:hover{
        border: 1px solid steelblue;
    }
</style>
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
        var t = $(this).siblings('span.btn_text');
        var c = $(this);
        var txt = t.text();
        var cancel_text = '取消';
        if( c.prop('checked') == true ){
            txt = cancel_text + txt;
        }
        else{
            txt = txt.substr( 2 );
        }
        t.text( txt );
    });

    $('#thread-data').on('click', '.categorize', function(){

    });





    $('.online').on('click', function(){
        if( !updateInfo() ){
            return false;
        }
        var ids = [];
        var hasFault = false;
        $('.admin-card-container').removeClass('wrong');

        $('.admin-card-container input[name="confirm_online"]:checked').each(function(i,n){
            var cont = $(this).parents('.admin-card-container');
            ids.push( cont.attr('data-id') );
            var release_time= Date.parse( cont.find('input[name="release_time"]').val() )/1000;
            if( release_time < Math.ceil( (new Date()).getTime() / 1000 ) ){
                cont.addClass('wrong');
                hasFault = true;
            }
        });
        if( hasFault ){
            toastr['warning']('发布时间不能是过去的时间');
            return;
        }

        var postData = {
            'review_ids': ids,
            'status': -1
        };

        $.post('/review/set_status', postData, function( data ){
            if( data.data.result == 'ok' ){
                location.reload();
            }
        });
        return true;
    });

    $('.update').on('click', function(){
        updateInfo()
    });

    $('.delete').on('click', function(){
        var ids = [];
        $('.photo-container-admin input[name="confirm_online"]:checked').each(function(i,n){
            ids.push( $(this).parents('.photo-container-admin').attr('data-id') );
        });
        $.post('/review/set_status', {'review_ids': ids, 'status': 0}, function( data ){
            if( data.data.result == 'ok' ){
              location.reload();
            }
        });
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


});
</script>
