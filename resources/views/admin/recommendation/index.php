<ul class="breadcrumb">
  <li>
    <a href="#">用户模块</a>
  </li>
  <li>用户列表</li>
</ul>


<div class="form-inline">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
    </div>
    <div class="form-group">
        <input name="username" class="form-filter form-control" placeholder="角色名称">
    </div>
    <div class="form-group">
        <input name="nickname" class="form-filter form-control" placeholder="展示名称">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>
<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li class="recommend_type" data-type="unreviewed">
        <a href="#">审核库</a>
      </li>
      <li class="recommend_type" data-type="pending">
        <a href="#">待生效</a>
      </li>
      <li class="recommend_type" data-type="normal">
        <a href="#">已生效</a>
      </li>
    </ul>
</div>

<table class="table table-bordered table-hover" id="list_users_ajax"></table>
<span class="oper_section unreviewed">
    <button class="btn btn-danger chg_stat delete" style="width: 20%">删除</button>
    <button class="btn btn-info chg_stat pass" style="width: 20%">通过</button>
</span>
<span class="oper_section pending">
    <button class="btn btn-danger chg_stat reject" style="width: 20%">拒绝</button>
    <button class="btn btn-info chg_stat online" style="width: 20%">生效</button>
</span>
<span class="oper_section normal">
    <button class="btn btn-danger chg_stat delete" style="width: 20%">取消</button>
</span>
<style>
    .oper_section{
        display: none;
    }
</style>
<script>
var table = null;
var role = null;
var status = null;

$(function() {
    type = getQueryVariable('type');
    role = getQueryVariable('role');
    if(!type) type = 'unreviewed';
    $('ul.nav-tabs li[data-type="'+type+'"]').addClass('active');
    $('.oper_section.'+type).show();
    $('#thread-data').addClass( type );
    $('.recommend_type').on('click', function(e){
        e.preventDefault();
        var t = $(this).attr('data-type');
        var url =  '/recommendation/index?type='+t+'&role='+role;
        location.href=url;
    });

    table = new Datatable();
    table.init({
        src: $("#list_users_ajax"),
        dataTable: {
            "columns": [
                { data: "checkbox", sortable: false, name: "<label for='selectAll'><input type='checkbox' name='selectAll' id='selectAll'/>全选</label>" },
                { data: "id", name: "ID" },
                { data: "uid", name: "账号ID" },
                { data: "nickname", name: "昵称"},
                { data: "register_time", name:"注册时间"},
                { data: "user_landing", name:"社交信息"},
                { data: "avatar", name:"头像"},
                { data: "introducer_name", name:"推荐人"},
                { data: "reason", name: "推荐理由"},
                { data: "recommend_time", name: "推荐时间"},
                // { data: "result", name: "拒绝理由"}
            ],
            "ajax": {
                "url": "/recommendation/list_users?type="+type+"&role="+role
            }
        },

        success: function(data){
        },
    });

    $('.chg_stat').on('click', function(){

        if( $(this).hasClass('pass') ){
            status = 'pass';
        }
        else if($(this).hasClass('online')){
            status = 'online';
        }
        else if( $(this).hasClass('reject') ){
            status = 'reject';
        }
        else if( $(this).hasClass('delete') ){
            status = 'delete';
        }
        else{
            status = '';
        }

        var postData = packPostData( status );
        if( !postData.ids.length ){
            return toastr['error']('请选择要进行操作的用户');
        }
        $.post('/recommendation/chg_stat', postData, function( data ){
            data = data.data;
            if( data.result == 'ok' ){
                location.reload();
            }
        });
    });

    $('#list_users_ajax').on('click', '#selectAll', function(){
        var all = $(this).parent().hasClass('checked');
        var checkboxes = $('td.db_checkbox input[type="checkbox"]');

        if( all ) {
            _.each( checkboxes, function( cbox ){
                $(cbox).attr("checked", true);
                $(cbox).parent().addClass("checked");
            });
        }
        else {
            _.each( checkboxes, function( cbox ){
                $(cbox).attr("checked", false);
                $(cbox).parent().removeClass("checked");
            });
        }
    });


    // $.post('/role/get_roles_by_user_id',{'user_id':user_id},function(result){
    //     var role_ids = result.data.roles;
    //     var per_box = $('input[name="role_id[]"]');

    //     for(var role_id in role_ids){
    //         per_box.filter('[value="'+role_ids[role_id]+'"]').attr({'checked':'checked'});
    //     }
    // });


});

function packPostData( stat ){
    var ids = [];
    $('input[name="check_user"]:checked').each(function(){
        var p = $(this).parents('tr');
        var id = p.find('.db_id').text();
        ids.push( id );
    });
    return {
        'ids': ids,
        'status': stat
    };
}
</script>

