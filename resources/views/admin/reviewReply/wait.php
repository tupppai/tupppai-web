<link href="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.js" type="text/javascript"></script>
<script type="text/javascript" src="/theme/assets/global/plugins/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="/theme/assets/global/plugins/select2/select2.css"/>
<style>
.db_upload_view { position: relative; width: 120px; }
.db_puppet_uid{width: 180px;}
.uploadify { left: 60px; top: 10px; }
.user-portrait { left: 10px; position: absolute; }
.db_puppet_uid .select2-container { width: 100%; }
</style>

<ul class="breadcrumb">
  <li>
    <a href="#">运营模块</a>
  </li>
  <li>审核作品</li>
    <div class="btn-group pull-right">
        <a href="#review-batch" data-toggle="modal" class="add">批量处理</a>
    </div>
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
        <input name="role_created_beg" class="form-filter form-control" placeholder="开始时间">
        <input name="role_created_end" class="form-filter form-control" placeholder="结束时间">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<div class="tabbable-line">
    <ul class="nav nav-tabs">
      <li class="active">
        <a href="wait">
          待编辑</a>
      </li>
      <li>
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

<? modal('/review/review_batch'); ?>

<table class="table table-bordered table-hover" id="review_ajax"></table>
<button id="submit" class="btn btn-success" style="width: 20%">设置作品内容</button>
<button class="btn btn-danger hide_thread" style="width: 20%">隐藏</button>

<div id="upload_area" class="hide" style="position: absolute">
</div>
<script>
var table = null;
jQuery(document).ready(function() {


    table = new Datatable();
    table.init({
        src: $("#review_ajax"),
        dataTable: {
            "columns": [
                { data: "id", name: "ID"},
                { data: "avatar", name: "用户头像"},
                { data: "nickname", name: "用户昵称" },
                { data: "uid", name: "用户ID" },
                { data: "image_view", name: "原图" },
                { data: "desc", name: "描述" },
                { data: "puppet_uid", name: "马甲账号" },
                { data: "upload_view", name: "上传作品" },
                { data: "puppet_desc", name: "描述" },
                { data: "categories", name: "频道" },
                { data: "release_time", name: "发布时间" },
                { data: "checkbox", name: "<input id='checkall' type='checkbox'/>", orderable: false },
            ],
            "ajax": {
                "url": "/reviewReply/list_reviews?type=1&status=1"
            }
        },
        success: function(data){
            $(".user-portrait").mouseover(function(e){
                var id = $(this).attr('data-id');
                $("#upload_image").attr('data-id', id);

                $("#upload_area").removeClass('hide');
                var offset = $(e.target).offset();
                $("#upload_area").css('top', (offset.top-50) + 'px');
                $("#upload_area").css('left', (offset.left-10) + 'px');
                //$(this).after($("#upload_image"));
            });

            $("#checkall").click(function() {
                if(this.checked) {
                    $("input[type='checkbox']").attr("checked", true);
                    $("input[type='checkbox']").parent().addClass("checked");
                }
                else {
                    $("input[type='checkbox']").attr("checked", false);
                    $("input[type='checkbox']").parent().removeClass("checked");
                }
                //$("input.form-control[type='checkbox']:checked");
            });

            var select = $("select[name='puppet_uid']");

            _.each(select, function(row) {
                var length = $(row).find("option").length;
                var index  = parseInt(Math.random()*length);
                var value  = $(row).find("option:eq("+index+")").attr("value");
                select.val(value==""?1:value);
                $(row).select2();
            });

            $('input[name="release_time"]').datetimepicker({
                lang: 'ch',
                format: 'Y-m-d H:i',
                value: new Date().Format("yyyy-MM-dd hh:mm:ss")
            });

            $("#upload_area").append('<input id="upload_image" type="file" name="uploadify" type="button" value="添加">');

            Common.upload("#upload_image", function(data, upload_id){
                var data = data.data;
                var id = $(upload_id).attr("data-id");
                $("#upload_"+id).val(data.id);
                $("#preview_"+id).attr('src', data.url);
            }, null, {
                url: '/image/add'
            });
        }
    });

    $("#review-batch .save").click(function(){
        var times = $("input[name='release_time']");
        _.each(times, function(row, index) {
            var start_time  = $('input[name="start_time"]').val();
            var duration    = $('select[name="duration"]').val();
            if(duration == 0){
                $(row).val(start_time);
            }
            else if(duration == -1) {
                var date = new Date();
                date.setFullYear(start_time.substring(0,4));
                date.setMonth(start_time.substring(5,7)-1);
                date.setDate(start_time.substring(8,10));
                date.setHours(start_time.substring(11,13));
                date.setMinutes(start_time.substring(14,16));
                //date.setSeconds(start_time.substring(17,19));
                var time = Date.parse(date)/1000;

                var a = new Date(start_time);
                var b = new Date(start_time);
                b.setDate(a.getDate() + 1);

                a = a.getTime();
                b = b.getTime();
                time = Math.random()*(b-a) + a;

                var random_date = new Date(time).Format("yyyy-MM-dd hh:mm:ss");

                $(row).datetimepicker({
                    value: random_date,
                    format: 'Y-m-d H:i',
                });

            }
            else {
                var time = new Date(start_time);
                var random_date = new Date(time.getTime() + index*duration*1000).Format("yyyy-MM-dd hh:mm:ss");

                $(row).datetimepicker({
                    value: random_date,
                    format: 'Y-m-d H:i',
                });
            }
        });

        $("#review-batch").modal('hide');
    });

    $("#submit").click(function() {
        var rows = $("#review_ajax tbody tr");
        var data = [];
        var flag = false;
        _.each(rows, function(row) {
            var obj = {};
            if($(row).find("input.form-control[type='checkbox']:checked").length > 0) {
                obj.id   = $(row).find('.db_id').text().trim();
                obj.uid  = $(row).find('select[name="puppet_uid"]').val();
                obj.desc = $(row).find('input[name="desc"]').val();
                obj.review_id = $(row).find('.db_id').text().trim();
                obj.upload_id = $(row).find('input[name="upload_id"]').val();
                obj.release_time = $(row).find('input[name="release_time"]').val();
                cats = $(row).find('td.db_categories');

                var cat_ids = [];
                cats.find('input[name="th_cats"]').siblings('ul').find('li.cat_ids').each(function(){
                    cat_ids.push($(this).attr('data-id'));
                });
                obj.category_ids = cat_ids;

                for(var i in obj) {
                    if(!obj[i] || obj[i] == ''){
                        alert(i + ' not define');
                        flag = true;
                        return false;
                    }
                    if(Date.parse( obj.release_time ) < (new Date).getTime()/1000){
                        flag = true;
                        toastr['warning']('不能设置过去的时间');
                        return false;
                    }
                }
                data.push(obj);
            }
        });

        if(flag) {
            return false;
        }
        if( data.length == 0 ){
            return false;
        }

        $.post('/reviewReply/set_batch_reply', {data: data}, function(data) {
            if( data.data.result == 'ok' ){
                toastr['success']('success');
            }
        });
    });



    $('.hide_thread').on('click', function(){
        var ids = [];
        $('#review_ajax tr').each(function(i,n){
            if($(this).find('input[type="checkbox"]:checked').length != 0){
                var id = $(this).find('.db_id').text();
                ids.push( id );
            }
        });

        $.post('/reviewReply/set_status', {'ids': ids,'status':'hide' }, function( data ){
            if( data.data.result == 'ok' ){
                toastr['success']('隐藏成功');
            }
        });
    });

    $('#review_ajax').on('draw.dt', function(){
        $('td.db_categories').each(function(i,n){
            var input = $('<input>').attr({
                'type': 'text',
                'name': 'th_cats',
                'class': 'search-query'
            });
            $(n).append(input);
        });

        $('#review_ajax').trigger('addTokenInput');
    });
    $('#review_ajax').on( 'addTokenInput', function(){
        $('input[name="th_cats"]').tokenInput("/category/search_category",{
            propertyToSearch: 'display_name',
            jsonContainer: 'data',
            theme: "facebook",
            hintText: '输入频道名，以添加频道',
            noResultsText: '无相应结果',
            searchingText: '查找中',
            tokenLimit: 5,
            // preventDuplicates: true,
            //tokenValue: 'data-id',
            // resultsFormatter: function(item){
            //     var genderColor = item.sex == 1 ? 'deepskyblue' : 'hotpink';
            //     return "<li>" +
            //     "<img src='" + item.avatar + "' title='" + item.username + " " + item.nickname + "' height='25px' width='25px' />"+
            //     "<div style='display: inline-block; padding-left: 10px;'>"+
            //         "<div class='username' style='color:"+genderColor+"'>" + item.username + "</div>"+
            //         "<div class='nickname'>" + item.nickname + "</div>"+
            //     "</div>"+
            //     "</li>" },
            tokenFormatter: function(item) {
                return "<li class='token-input-token-facebook cat_ids' data-id='"+item.id+"'>"+
                item.display_name +"</li>";
            },
        });

    });
});
</script>

<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/css/token-input.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/css/token-input-facebook.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/css/token-input-mac.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/js/jquery.tokeninput.js" type="text/javascript"></script>
<style>
    ul.token-input-list,
    ul.token-input-list-facebook,
    ul.token-input-list-mac,
    div.token-input-dropdown,
    div.token-input-dropdown-facebook,
    div.token-input-dropdown-mac,
    ul.token-input-list li input,
    ul.token-input-list-facebook li input,
    ul.token-input-list-mac li input{
        width: 200px;
        display: inline-block;
        vertical-align: middle;
    }

    .thread_category.normal{ color: dodgerblue; }
    .thread_category.verifing{ color: darkkhaki; }
    .thread_category.verified{ color: lightgreen; }
    .thread_category.deleted{ color: magenta;  text-decoration: line-through; }
</style>
