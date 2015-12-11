<ul class="breadcrumb">
  <li>
    <a href="#">用户模块</a>
  </li>
  <li>创建账号</li>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="账号ID">
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
      <li class="active">
        <a href="/invitation/help">
        求助 </a>
      </li>
      <li>
        <a href="/invitation/delhelp">
        已删除求助</a>
      </li>
    </ul>
</div>
<table class="table table-bordered table-hover" id="waistcoat_ajax"></table>


<script>
var table = null;
jQuery(document).ready(function() {
    table = new Datatable();
    table.init({
        src: $("#waistcoat_ajax"),
        dataTable: {
            "columns": [
                { data: "id", name: "帖子ID" },
                { data: "create_time", name:"求助发布时间"},
                { data: "oper", name: "删除/查看原图", orderable: false},
                { data: "avatar", name:"发贴头像", orderable: false},
                { data: "username", name:"发贴姓名"},
                { data: "nickname", name:"发帖昵称"},
                // { data: "content", name:"帖子详细信息"},
                { data: "sex", name:"性别"},
                { data: "click_count", name: "浏览数"},
                { data: "comment_count", name: "评论数", orderable: false},
                { data: "share_count", name:"分享"},
                { data: "download_times", name: "下载数", orderable: false},
                { data: "reply_count", name: "作品数", orderable: false},//记录有多少作品上传了
                { data: "inform_count", name: "举报数", orderable: false},
                //{ data: "weixin_share_count", name:"朋友圈内"},
                // { data: "userurl", name: "分享好友" },
                //{ data: "up_count", name: "点赞"},
            ],
            "ajax": {
                "url": "/help/list_helps"
            }
        },

        success: function(data){
            $(".delete").click(function(){
            });

            $(".del").click(function(){
                var target_id   = $(this).attr("data");
                var type        = $(this).attr("type");
                if(confirm("确认删除作品?")){
                    $.post("/help/set_status", {
                        id: target_id,
                        type: type
                    }, function(){
                        toastr['success']("删除成功");
                        table.submitFilter();
                    });
                }
            });
        }
  });
});
</script>
