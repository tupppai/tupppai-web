<ul class="breadcrumb">
  <li>
    <a href="#">用户模块</a>
  </li>
  <li>创建账号</li>
</ul>

<?php
    echo file_get_contents('../resources/views/admin/waistcoat/search_user.php');
?>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <?php
      foreach($roles as $role){
          $active = ($role_name == $role->name)?'active':'';
          echo "<li class='$active' data='".$role->id."'>".
            '<a href="'.$role->name.'">'.$role->display_name.'</a>'.
          '</li>';
      }
      ?>
       <a href="/help/index" data-toggle="modal" class="btn btn-default btn-sm float-right">发布帖子</a>
      <a href="#add_user" data-toggle="modal" class="btn btn-default btn-sm float-right">创建账号</a>
    </ul>
</div>

<table class="table table-bordered table-hover" id="waistcoat_ajax"></table>


<?php modal('/user/add_user'); ?>
<?php modal('/user/remark_user'); ?>

<script>
var table = null;
jQuery(document).ready(function() {
    var role_id = $(".nav li.active").attr("data");

    table = new Datatable();
    table.init({
        src: $("#waistcoat_ajax"),
        dataTable: {
            "columns": [
                { data: "uid", name: "账号ID" },
                { data: "username", name: "用户名"},
                { data: "create_time", name:"创建时间"},
                { data: "nickname", name: "昵称"},
                { data: "avatar", name: "头像", orderable:false },
                { data: "sex", name:"性别"},
                { data: "asks_count", name:"求助数"},
                { data: "replies_count", name:"作品数"},
                { data: "fans_count", name:"粉丝数"},
                { data: "uped_count", name:"被赞数"},
                { data: "inform_count", name:"被举报数"},
                { data: "data", name: "操作", orderable:false},
                { data: "user_landing", name:"三方账号"}
            ],
            "ajax": {
                "url": "/waistcoat/list_users?role_id=" + role_id
            }
        },

        success: function(data){
            $(".remark").click(function(){
                var nickname = $(this).attr('nickname');
                $("#remark_user input[name='nickname']").val(nickname);

                var uid = $(this).attr('uid');
                $("#remark_uid").val(uid);

                var password = $(this).attr('password');
                $("#remark_user input[name='password']").val(password);

                var remark = $(this).attr('remark');
                $("#remark").val(remark);
            });
        },
    });
});
</script>

