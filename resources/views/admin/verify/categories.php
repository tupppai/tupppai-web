
<script type="text/javascript" src="/main/vendor/node_modules/underscore/underscore-min.js"></script>

<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>多图审核</li>
</ul>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li class="all" data-type="unreviewed">
        <a href="/verify/categories?type=unreviewed">审核库</a>
      </li>
      <li class="hot" data-type="app">
        <a href="/verify/categories?type=app">热门</a>
      </li>
      <li class="pc_hot" data-type="pc">
        <a href="/verify/categories?type=pc">PC热门</a>
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
<?php //modal('/verify/reply_comment'); ?>

<button class="btn btn-danger delete" style="width: 20%">删除</button>
<button class="btn btn-success update" style="width: 20%">确定</button>
<style>
    .photo-container-admin{
        border: 1px solid transparent;
    }
    .photo-container-admin:hover{
        border: 1px solid steelblue;
    }
    .set-value label{
        padding: 0;
    }
    .chg_stat{
        display:none;
    }
    .photo-main{
        border-top:1px solid lightgray;
        border-bottom:1px solid lightgray;
    }
</style>
<script>
var table = null;
var type;
jQuery(document).ready(function() {
    type = getQueryVariable('type');
    $('ul.nav-tabs li[data-type="'+type+'"]').addClass('active');
    $('#thread-data').addClass( type );

    table = new Paginate();
    table.init({
        src: $('#thread-data'),
        url: '/verify/list_threads?type='+type,
        template: _.template($('#thread-item-template').html()),
        success: function() {
        }
    });
});

$(document).ready(function(){
    $('#thread-data').on('click', '.chg_stat', function(e){
        if( type != 'unreviewed' ){
            e.preventDefault();
            return;
        }
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


    $('.update').on('click', function(){
        var pc_ids  = [];
        var pc_types  = [];
        var pc_status = [];

        var app_ids = [];
        var app_types = [];
        var app_status = [];

        //confirm_online:checked
        $('.photo-container-admin .chg_stat').each(function(i,n){
            var cont = $(this).parents('.photo-container-admin');
            if( !cont.find( 'input[name="confirm_online"]' ).prop('checked') ){
                return;
            }
            var status = 0;

            if( $(this).hasClass('pc_popular') ){
                status = Number($(this).prop('checked'));
                if( type == 'unreviewed' && status ){
                    status = -1;
                }
                pc_ids.push( cont.attr('data-target-id') );
                pc_types.push( cont.attr('data-target-type') );
                pc_status.push( status );
            }
            if( $(this).hasClass('app_popular') ){
                status = Number($(this).prop('checked'));
                if( type == 'unreviewed' && status ){
                    status = -1;
                }
                app_ids.push( cont.attr('data-target-id') );
                app_types.push( cont.attr('data-target-type') );
                app_status.push( status );
            }
        });

        if( pc_ids.length > 0 ){
            var postData = {
                'target_id': pc_ids,
                'target_type': pc_types,
                'status': pc_status,
                'type': 'pc'
            };

            $.post('/verify/set_thread_as_pouplar', postData, function( data ){
                if( data.data.result == 'ok' ){
                    toastr['success']('设置PC热门成功');
                }
            });
        }

        if( app_ids.length > 0 ){
            var postData = {
                'target_id': app_ids,
                'target_type': app_types,
                'status': app_status,
                'type': 'app'
            };

            $.post('/verify/set_thread_as_pouplar', postData, function( data ){
                if( data.data.result == 'ok' ){
                    toastr['success']('设置APP热门成功');
                }
            });
        }


        return true;
    });

    $('.delete').on('click', function(){
        var target_types = [];
        var target_ids = [];
        $('input[name="confirm_online"]:checked').each(function( i, n ){
            var p = $(this).parents('.photo-container-admin');
            target_types.push( p.attr('data-target-type') );
            target_ids.push( p.attr('data-target-id') );
        });
        var postData = {
            'target_ids': target_ids,
            'target_types': target_types
            // 'category': type
        };

        $.post('/verify/delete_popular', postData, function( data ){
            if( data.data.result == 'ok' ){
              location.reload();
            }
        });
    });

});
</script>
