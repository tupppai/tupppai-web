<ul class="breadcrumb">
    <li>
        <a href="#">分类管理</a>
    </li>
    <li>标签管理</li>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="tag_id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <input name="tag_name" class="form-filter form-control" placeholder="名称">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<table id="tag_table" class="table table-bordered table-hover"></table>
<?php modal('/tag/upload_tag'); ?>
<script>
var table = null;
$(function() {
    table = new Datatable();
    table.init({
        src: $("#tag_table"),
        dataTable: {
            "columns": [
                { data: "id", name: "#" },
                { data: "name", name: "名称" },
                { data: "user_count", name: "使用用户数"},
                { data: "thread_count", name: "内容数" },
                { data: "collection_name", name: "合集名称" },
                { data: "remark", name: "文案" },
                { data: "cover", name: "封面图" },
                { data: "oper", name: "操作"},
            ],
            "ajax": {
                "url": "/tag/list_tags"
            }
        },
        success: function(){}
    });

    $('#tag_table').on('click', '.btn.offline', function(){
        if( !confirm('确定要从首页撤下该分类吗？') ){
            return false;
        }
        var tag_id = $(this).parents('tr').find('td.db_id').text();
        var status = 1;

        $.post('/tag/update_status', { tag_id: tag_id, status: status }, function( data ){
            if( data.data.result == 'ok' ){
                toastr['success']('下架成功');
                table.submitFilter();
            }
        })
    });
});

</script>
