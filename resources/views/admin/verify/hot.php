<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/underscore/underscore-min.js"></script>

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

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li class="all" data-type="unreviewed">
        <a href="/verify/hot?type=unreviewed">审核库</a>
      </li>
      <li class="hot" data-type="app">
        <a href="/verify/hot?type=app">APP热门</a>
      </li>
      <li class="pc_hot" data-type="pc">
        <a href="/verify/hot?type=pc">PC热门</a>
      </li>
      <li class="" style="margin-top:15px;" >
        <label for="selectAll">
            <input type="checkbox" name="selectAll" id="selectAll" checked="checked"/>全选
        </label>
      </li>
    </ul>
</div>


<div id="thread-data"></div>

<?php modal('/verify/check_item'); ?>
<?php //modal('/verify/reply_comment'); ?>

<button class="btn btn-danger delete" style="width: 20%">隐藏</button>
<button class="btn btn-warning invalid" style="width: 20%">失效</button>
<button class="btn btn-success update" style="width: 20%">确定</button>
<button class="btn btn-info online" style="width: 20%">生效</button>
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
    #thread-data span.popular_type.set-value{
        display: none;
    }
    #thread-data.unreviewed span.popular_type.set-value{
        display:inline-block;
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
var stats = {
    'unreviewed': [ 'hidden', 'ready' ],
    'app': [ 'hidden', 'online' ],
    'pc': [ 'hidden', 'online' ]
};

jQuery(document).ready(function() {
    type = getQueryVariable('type');
    if(!type) type = 'unreviewed';
    $('ul.nav-tabs li[data-type="'+type+'"]').addClass('active');
    $('#thread-data').addClass( type );
    if( type == 'unreviewed' ){
        $('.online').hide();
        $('.invalid').hide();
        $('.delete').text('取消推荐');
        $('#thread-data').addClass( type );
    }
    else{
        $('.update').hide();
    }

    table = new Paginate();
    table.init({
        src: $('#thread-data'),
        url: '/verify/list_threads?type='+type,
        template: _.template($('#thread-item-template').html()),
        success: function() {
        },
        display:15
    });

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

    $('.update, .online').on('click', function(){
        var postData = packData();

        if( postData['pc']['target_id'].length > 0 ){
            $.post('/verify/set_thread_as_pouplar#pc', postData['pc'], function( data ){
                if( data.data.result == 'ok' ){
                    if( type == 'pc'){
                        location.reload();
                    }
                    else{
                        toastr['success']('设置PC热门成功');
                    }
                }
            });
        }

        if( postData['app']['target_id'].length > 0 ){
            $.post('/verify/set_thread_as_pouplar#app', postData['app'], function( data ){
                if( data.data.result == 'ok' ){
                    if( type == 'app' ){
                        location.reload();
                    }
                    else{
                        toastr['success']('设置APP热门成功');
                    }
                }
            });
        }

        return true;
    });

    $('.delete, .invalid').on('click', function(){
        var target_types = [];
        var target_ids = [];
        $('input[name="confirm_online"]:checked').each(function( i, n ){
            var p = $(this).parents('.photo-container-admin');
            target_types.push( p.attr('data-target-type') );
            target_ids.push( p.attr('data-target-id') );
        });
        if( $(this).hasClass('delete') ){ // unreviewed
            status = 'delete';
        }
        else if( $(this).hasClass('invalid') ){ // app & pc
            status = 'invalid';
        }
        var postData = {
            'target_ids': target_ids,
            'target_types': target_types,
            'category': type,
            'status': status
        };

        $.post('/verify/delete_popular', postData, function( data ){
            if( data.data.result == 'ok' ){
              location.reload();
            }
        });
    });

});

function packData(){
    var pc_ids  = [];
    var pc_types  = [];
    var pc_status = [];

    var app_ids = [];
    var app_types = [];
    var app_status = [];

    $('.photo-container-admin .chg_stat').each(function(i,n){
        var cont = $(this).parents('.photo-container-admin');
        if( !cont.find( 'input[name="confirm_online"]' ).prop('checked') ){
            return;
        }
        var status = 0;

        if( type != 'app' && $(this).hasClass('pc_popular') ){
            ckd = Number($(this).prop('checked'));
            status = stats[type][ckd];

            pc_ids.push( cont.attr('data-target-id') );
            pc_types.push( cont.attr('data-target-type') );
            pc_status.push( status );
        }
        if( type != 'pc' && $(this).hasClass('app_popular') ){
            ckd = Number($(this).prop('checked'));
            status = stats[type][ckd];

            app_ids.push( cont.attr('data-target-id') );
            app_types.push( cont.attr('data-target-type') );
            app_status.push( status );
        }
    });

    return {
        'pc':{
            'target_id': pc_ids,
            'target_type': pc_types,
            'status': pc_status,
            'type': 'pc'
        },
        'app': {
            'target_id': app_ids,
            'target_type': app_types,
            'status': app_status,
            'type': 'app'
        }
    };
}
</script>
