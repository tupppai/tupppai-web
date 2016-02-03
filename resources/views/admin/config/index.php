<ul class="breadcrumb">
    <li>
        <a href="#">系统模块</a>
    </li>
    <li>配置</li>
    <div class="btn-group pull-right">
        <a href="#edit_config" data-toggle="modal" class="add">添加配置</a>
    </div>
</ul>

<div class="form-inline">
    <div class="form-group">
        <input name="role_id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <input name="role_name" class="form-filter form-control" placeholder="角色名称">
    </div>
    <div class="form-group">
        <input name="role_display_name" class="form-filter form-control" placeholder="展示名称">
    </div>
    <div class="form-group">
        <input name="role_created_beg" class="form-filter form-control" placeholder="开始时间">
        <input name="role_created_end" class="form-filter form-control" placeholder="结束时间">
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<table id="config_table" class="table table-bordered table-hover"></table>

<?php modal('/config/edit_config'); ?>

<script>
var table = null;
$(function() {
    table = new Datatable();
    table.init({
        src: $("#config_table"),
        dataTable: {
            "columns": [
                { data: "name", name: "配置名称" },
                { data: "value", name: "配置数值"},
                { data: "remark", name: "描述"},
                { data: "oper", name: "操作"}
            ],
            "ajax": {
                "url": "/config/list_configs"
            }
        },
        success: function(){
        },
    });
    $('.add').on('click', function(){
        $('#edit_config .modal-title').text('添加配置');
        $('#edit_config form')[0].reset();
        $('#edit_config input[name="name"]').removeProp('readonly');
    });

    $('#config_table').on('click', ' .edit', function(){
        var tr = $(this).parents('tr');

        var id = tr.find(".db_id").text();
        var config_name = tr.find(".db_name").text();
        var config_value = tr.find(".db_value").text();
        var config_remark = tr.find(".db_remark").text();

        $('#edit_config .modal-title').text('编辑配置');
        $("#edit_config input[name='name']").val(config_name).prop('readonly', true);
        $("#edit_config input[name='value']").val(config_value);
        $("#edit_config input[name='remark']").val(config_remark);
    });
});

</script>
</div>
