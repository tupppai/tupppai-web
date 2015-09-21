<ul class="breadcrumb">
  <li>
    <a href="#">个人工作台</a>
  </li>
  <li>马甲管理</li>
</ul>


<div class="form-inline" id="search_box">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
    </div>
    <div class="form-group">
        <input name="nickname" class="form-filter form-control" placeholder="昵称">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
    <a href="#add_puppet" class="btn btn-default btn-sm float-right add">创建账号</a>
</div>


<table class="table table-bordered table-hover" id="list_puppets_ajax"></table>
<?php modal('/puppet/add_puppet'); ?>

<script>
var table = null;
$(function() {
    table = new Datatable();
    table.init({
        src: $("#list_puppets_ajax"),
        dataTable: {
            "columns": [
                { data: "oper", name: "操作"},
                { data: "create_time", name:"注册时间"},
                { data: "avatar", name:"头像"},
                { data: "nickname", name: "昵称"},
                { data: "sex", name:"性别"},
                { data: "uid", name: "账号ID" },
                { data: "phone", name: "注册信息"},
            ],
            "ajax": {
                "url": "/puppet/list_puppets"
            }
        },

        success: function(){}
    });

    $('#list_puppets_ajax').on( 'click', '.edit', function(){
        var tr = $(this).parents( 'tr' );

        var uid = tr.find( '.db_uid' ).text();
        var nickname = tr.find( '.db_nickname' ).text();
        var avatar = tr.find( '.db_avatar img').attr( 'src' );
        var gender = tr.find( '.db_role_display_name' ).text() == '男' ? '1' : '0';

        $("#add_puppet input[name='uid']").val( uid );
        $("#add_puppet input[name='nickname']").val( nickname );
        $("#add_puppet input[name='avatar']").val( avatar );
        $("#add_puppet img.user-portrait").attr( 'src', avatar );
        $("#add_puppet input[name='sex'][value='"+gender+"']").attr('checked', 'checked');
        $("#add_puppet input[name='sex'][value='"+gender+"']").parent().addClass('checked');
        $("#add_puppet").modal("show");
    });

    $('#search_box').on( 'click', 'a.add[href="#add_puppet"]', function(){
        $('#add_puppet form')[0].reset();
        $("#add_puppet input[name='uid']").val( '' );
        $("#add_puppet input[name='nickname']").val( '' );
        $("#add_puppet input[name='avatar']").val( '' );
        $("#add_puppet img.user-portrait").attr( 'src', '' );
        $("#add_puppet input[name='sex']").removeAttr('checked');
        $("#add_puppet input[name='sex']").parent().removeClass('checked');
        $("#add_puppet").modal("show");
    });
});
</script>

