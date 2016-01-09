<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>发布管理</li>
</ul>
<div class="btn-group pull-right">
    <ul class="dropdown-menu pull-right" role="menu">
    <li>
    <a href="#">Action</a>
    </li></ul>
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

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li>
        <a href="wait">
          待编辑</a>
      </li>
      <li class="active">
        <a href="pass">
          预览生效</a>
      </li>
      <li>
        <a href="fail">
          失败</a>
      </li>
      <li>
        <a href="release">
          已发布</a>
      </li>
    </ul>
</div>

<table class="table table-bordered table-hover" id="review_ajax"></table>
<button class="btn btn-danger delete" style="width: 25%">删除</button>

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Datatable();
    table.init({
        src: $("#review_ajax"),
        dataTable: {
            "columns": [
                { data: "id", name: "ID"},
                { data: "avatar", name: "马甲头像"},
                { data: "uid", name: "马甲ID" },
                { data: "nickname", name: "马甲昵称" },
                { data: "desc", name: "作品描述" },
                { data: "image_view", name: "作品" },
                { data: "execute_time", name: "发布时间" },
                { data: "checkbox", name: '<input type="checkbox" class="selectAll" />', orderable:false },
            ],
            "ajax": {
                "url": "/reviewReply/list_reviews?status=-1"
            }
        },
        success: function(data){
            $(".pass").click(function(){

            });
        }
    });
    $('.selectAll').on('click',function(){
        if(this.checked) {
            $("#review_ajax input[type='checkbox']").attr("checked", true);
            $("#review_ajax input[type='checkbox']").parent().addClass("checked");
        }
        else {
            $("#review_ajax input[type='checkbox']").attr("checked", false);
            $("#review_ajax input[type='checkbox']").parent().removeClass("checked");
        }
        //$("input.form-control[type='checkbox']:checked");
    });

    $('.delete').on('click', function(){
        var ids = getIds();
        $.post('/reviewReply/set_status', {'ids': ids,'status':'delete' }, function( data ){
            if( data.data.result == 'ok' ){
                toastr['success']('删除成功');
            }
        });
    });
});


function getIds(){
    var ids = [];
    $('#review_ajax tr').each(function(i,n){
        if($(this).find('input[type="checkbox"]:checked').length != 0){
            var id = $(this).find('.db_id').text();
            ids.push( id );
        }
    });
    return ids;
}
</script>
