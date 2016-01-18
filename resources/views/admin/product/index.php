<ul class="breadcrumb">
    <li>
        <a href="#">交易系统</a>
    </li>
    <li>商品管理</li>
     <div class="btn-group pull-right">
        <a href="#add_product" data-toggle="modal" class="add">创建商品</a>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <input name="name" class="form-filter form-control" placeholder="商品名称">
    </div>
    <div class="form-group">
        <input name="desc" class="form-filter form-control" placeholder="商品描述">
    </div>
    <div class="form-group">
        <input name="remark" class="form-filter form-control" placeholder="商品备注">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<table id="product_table" class="table table-bordered table-hover"></table>

<?php modal('/product/add_product'); ?>

<script>
var table = null;
$(function() {
    table = new Datatable();
    table.init({
        src: $("#product_table"),
        dataTable: {
            "columns": [
                { data: "id", name: "商品ID" },
                { data: "name", name: "商品名称" },
                { data: "desc", name: "商品描述"},
                { data: "remark", name: "商品备注"},
                { data: "price", name: "商品价格"},
                { data: "oper", name: "操作"}
            ],
            "ajax": {
                "url": "/product/list_products"
            }
        },
        success: function(){
        },
    });
    $('#product_table').on('click', '.edit', function(){
		var tr = $(this).parents('tr');

		var id = tr.find('.db_id').text();
		var name = tr.find(".db_name").text();
		var desc = tr.find(".db_desc").text();
		var remark = tr.find(".db_remark").text();
		var price = tr.find(".db_price").text();

		$('#add_product .modal-title').text('编辑商品');
		$("#add_product input[name='id']").val(id);
		$("#add_product input[name='name']").val(name);
		$("#add_product input[name='desc']").val(desc);
		$("#add_product input[name='remark']").val(remark);
		$("#add_product input[name='price']").val(price);
    });


	$("#product_table").on('click', '.delete', function(){
		var id = $(this).attr('data-id');

		$.post("/product/delete", { id: id }, function(data){
			data = data.data;
			if( data.result == 'ok' ){
				toastr['success']('删除成功');
				$("#add_product form")[0].reset();
				$("#add_product").modal("hide");
				$('#add_product .modal-title').text('创建商品');
				table.submitFilter();
				return true;
			}
			return false;
		});
	});
});

</script>
</div>
