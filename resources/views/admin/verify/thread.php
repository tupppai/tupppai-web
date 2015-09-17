<link rel="stylesheet" href="/main/css/test.css" type="text/css" >
<script type="text/javascript" src="/main/vendor/node_modules/underscore/underscore-min.js"></script>

<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>多图审核</li>
</ul>
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

<table class="table table-bordered table-hover" id="thread-data"></table>

<?php modal('/verify/thread_item'); ?>

<script>
var table = null;
jQuery(document).ready(function() {

    var columns = [
        { data: "0", name:"" },
        { data: "1", name:"" },
        { data: "2", name:"" },
        { data: "3", name:"" },
    ];
    table = new Datatable();
    table.init({
        src: $("#thread-data"),
        render: function(data) {
            var template = _.template($('#thread-item-template').html());
            var result = [];
            for(var i in data) {
                var arr = {};
                for(var j in data[i]){
                    arr[j] = template(data[i][j]);
                }

                result[i] = arr;
            }
            return result;
        },
        dataTable: {
            "columns": columns,
            "ajax": {
                "url": "/verify/list_threads"
            }
        },
        success: function( data ){
            // do nothing
        }
    });
});
</script>


