<ul class="breadcrumb">
  <li>
    评论库管理
  </li>
</ul>
<div>
<div class="form-inline">
    <div class="form-group">
        <input name="content" class="form-filter form-control" placeholder="内容">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search">搜索</button>
    </div>
    <a href="#add_comment" class="btn btn-default btn-sm float-right add">新增评论</a>
</div>

<table class="table table-bordered table-hover" id="commentStockTable_ajax"></table>
<?php modal('/commentStock/add_comment'); ?>

<form class="form-horizontal" id="add_comments">
    <div class="form-body">
        <div class="">
            <label class="control-label">快速添加评论内容（一行一个）</label>
            <textarea class="form-control" rows="7"></textarea>
        </div>
        <div class="">
            <button type="button" class="btn blue save">添加</button>
        </div>
    </div>
</form>

<script>
var table = null;
jQuery(document).ready(function() {
    table = new Datatable();
    table.init({
        src: $("#commentStockTable_ajax"),
        dataTable: {
            "columns": [
                { data: "id", name: "#" },
                { data: "content", name: "评论内容" },
                { data: "oper", name: "操作"},
                //{ data: "type", name: "评论类型"},
            ],
            "ajax": {
                "url": "/commentStock/list_comments"
            }
        },
        success: function(data){ },
    });

    $('a.add[href="#add_comment"]').on('click', function(){
        $('#add_comment').modal('show');
    });

    $("#add_comments .save").click(function(){
        var comments = $('#add_comments textarea').val().split(/\n/);

        $.post("/commentStock/addComments", { 'comments': comments }, function(data){
            data = data.data;
            if( data.result == 'ok'){
                table.submitFilter();
            }
        });
    });

    $("#commentStockTable_ajax").on('click', '.delete', function(){
        var tr = $(this).parents('tr');
        var id = tr.find('.db_id').text();

        $.post('/commentStock/deleteComments', { 'cids': [id] }, function( data ){
            data = data.data;
            if( data.result == 'ok'){
                table.submitFilter();
            }
        });
    });
});
</script>
