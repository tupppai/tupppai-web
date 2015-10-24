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

<table class="table table-bordered table-hover" id="list_users_ajax"></table>

<script>
var table = null;
var role = null;
$(function() {
    role = getQueryVariable('role');
    table = new Datatable();
    table.init({
        src: $("#list_users_ajax"),
        dataTable: {
            "columns": [
                { data: "uid", name: "账号ID" },
                { data: "nickname", name: "昵称"},
                { data: "reigster_time", name:"注册时间"},
                { data: "user_landing", name:"社交信息"},
                { data: "avatar", name:"头像"},
                { data: "introducer", name:"推荐人"},
                { data: "reason", name: "推荐理由"},
                { data: "recommend_time", name: "推荐时间"},
                { data: "oper", name: "操作"},
                { data: "result", name: "拒绝理由"}
            ],
            "ajax": {
                "url": "/recommendation/list_users?role="+role
            }
        },

        success: function(data){
        },
    });


    // $.post('/role/get_roles_by_user_id',{'user_id':user_id},function(result){
    //     var role_ids = result.data.roles;
    //     var per_box = $('input[name="role_id[]"]');

    //     for(var role_id in role_ids){
    //         per_box.filter('[value="'+role_ids[role_id]+'"]').attr({'checked':'checked'});
    //     }
    // });


});
</script>

