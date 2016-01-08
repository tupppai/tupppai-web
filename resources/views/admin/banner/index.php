<ul class="breadcrumb">
  <li>
    <a href="#">系统模块</a>
  </li>
  <li>推荐Banner</li>
  <div class="btn-group pull-right">
        <a href="#add_banner" data-toggle="modal"  data-target="#add_banner" class="add">添加Banner</a>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="app_name" class="form-filter form-control" placeholder="应用名称">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search">搜索</button>
    </div>
</div>

<table id="banner_list" class="table table-bordered table-hover"></table>

<?php modal('/banner/add_banner'); ?>

<script>
var table = null;
var editdata = null;
$(function(){
	table = new Datatable();
    table.init({
        src: $("#banner_list"),
        dataTable: {
            "columns": [
            	{ data: "id", name:"ID"},
                { data: "desc", name: "描述" },
                { data: "small_pic", name: "客户端" },
                { data: "large_pic", name: "pc" },
                { data: "url", name: "客户端跳转" },
                { data: "pc_url", name: "pc跳转" },
                { data: "create_time", name: "添加时间" },
                { data: "oper", name: "操作" }
            ],
            "ajax": {
                "url": "/banner/list_banners"
            },
            'ordering':false
        },
        success:function(){
            $(".edit").click(function(e) {
                var tr = $(this).parent().parent();

                //判断url内行
                callbackUrl = tr.find('.db_url').text();
                editdata = [
                    {id: tr.find('.db_id').text(), name: tr.find('.db_desc').text(), callbackUrl: callbackUrl},
                ];
                if(callbackUrl.indexOf('tupppai://') >= 0){
                    $('#client').hide();
                    $('#search-query').attr('name', 'url');
                    $('#client').attr('name', '');
                    loadtokeninput(editdata);
                    $('#clientTypeCategorie').parent().attr('class','checked');
                    $('#clientTypeUrl').parent().attr('class','');
//                    $('#clientTypeUrl').attr('checked','');
                    // $('.token-input-list-facebook').show();
                }else{
                    $('#client').show();
                    $('#search-query').attr('name', '');
                    $('#client').attr('name', 'url');
                    loadtokeninput([]);
                    $('#clientTypeCategorie').parent().attr('class','');
                    $('#clientTypeUrl').parent().attr('class','checked');
                    $('.token-input-list-facebook').hide();
                }
                createInit();
                //add data
                $("#add_banner input[name='id']").val(tr.find('.db_id').text());
                $("#add_banner input[name='desc']").val(tr.find('.db_desc').text());
                $("#add_banner input[name='small_pic']").val(tr.find('.db_small_pic img').attr('src'));
                $("#add_banner #small_preview").attr('src', tr.find('.db_small_pic img').attr('src'));
                $("#add_banner input[name='large_pic']").val(tr.find('.db_large_pic img').attr('src'));
                $("#add_banner #large_preview").attr('src', tr.find('.db_large_pic img').attr('src'));
                $("#add_banner input[name='url']").val(tr.find('.db_url').text());
                $("#add_banner input[name='pc_url']").val(tr.find('.db_pc_url').text());

      			$('#add_banner').modal('show');
            }); 
        }
    });

    $('#banner_list').on('click', '.delete', function(){
    	var banner_id = $(this).parents('tr').find('.db_id').text();
    	$.post('/banner/del_banner', {'banner_id':banner_id},function(result){
    		if(result.ret==1){
    			toastr['success']('删除成功');
                table.submitFilter();  //刷新表格
    		}
    	});
    });

    $( "#banner_list tbody" ).sortable({
      placeholder: "app-item-highlight",
      update: function(){
      	var sorts = [];
      	var items = $('#banner_list tr td.db_id');
      	items.each(function(i){
      		sorts.push(items[i].innerText);
      	});

      	$.post('/banner/sort_banners',{'sorts':sorts.join(',')},function(result){
      		if( result.ret==1){
      			toastr['success']('排序更改成功');
      			$('#add_banner').modal('hide');
                table.submitFilter();  //刷新表格
      		}
      	})
      }
    });

    $( "#banner_list tr" ).disableSelection();
});
</script>
<script src="/theme/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/theme/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.css"/>

<style>
.applogo{
	height: 50px;
	width: 50px;
	border-radius: 12px !important;
	border: 1px solid lightgray;
}
.app-item-highlight{
	height: 67px;
	background-color: khaki;
}
</style>
