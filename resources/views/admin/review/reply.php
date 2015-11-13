<link href="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/datetimepicker/jquery.datetimepicker.js" type="text/javascript"></script>
<script type="text/javascript" src="/theme/assets/global/plugins/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="/theme/assets/global/plugins/select2/select2.css"/>

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
    <div class="hidden">
        <input class="form-filter" type="hidden" name="type" value="2" />
        <input class="form-filter" type="hidden" name="status" value="<?php echo $status; ?>" />
    </div>
    <div class="form-group">
    <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</div>

<?php
$data = array(
    -5=>'待审核',
    -1=>'预发布',
    -3=>'审核拒绝',
     1=>'已发布'
);
echo '<div class="tabbable-line"><ul class="nav nav-tabs">';
foreach($data as $key=>$val){
    if($key == $status)
        echo '<li class="active"><a href="?status='.$key.'">'.$val.'</a></li>';
    else 
        echo '<li><a href="?status='.$key.'">'.$val.'</a></li>';
}
echo '</div></ul>';

modal('/review/review_batch');
?>

<table class="table table-bordered table-hover" id="review_ajax"></table>
<button id="submit" class="btn btn-success" style="width: 100%">设置作品内容</button>
<style>
.db_upload_view { position: relative; width: 120px; }
.uploadify { left: 60px; top: 10px; }
.user-portrait { left: 10px; position: absolute; }
</style>

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
                { data: "release_time", name: "发布时间" },
                { data: "checkbox", name: "<input id='checkall' type='checkbox'/>", orderable: false },
            ],
            "ajax": {
                "url": "/review/list_reviews"
            }
        },
        success: function(data){
            var views = $("input.form-control[type=file]");
            _.each(views, function(view) {
                Common.upload("#" + view.id, function(data, upload_id){
                    var data = data.data;
                    $(upload_id).prev().attr("src", data.url);
                    $(upload_id).next().val(data.id);
                }, null, {
                    url: '/image/upload'
                });
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
            var length = select.find("option").length;
            var index  = parseInt(Math.random()*length);
            var value  = select.find("option:eq("+index+")").attr("value");
            select.val(value==""?1:value);

            $("select[name='puppet_uid']").select2();
            $('input[name="release_time"]').datetimepicker({
                lang: 'ch',
                format: 'Y-m-d H:i', 
                value: new Date().Format("yyyy-MM-dd hh:mm:ss")
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
                obj.id  = $(row).find('.db_id').text().trim();
                obj.uid = $(row).find('select[name="puppet_uid"]').val();
                obj.upload_id = $(row).find('input[name="upload_id"]').val();
                obj.desc = $(row).find('input[name="desc"]').val();
                obj.release_time = $(row).find('input[name="release_time"]').val();

                for(var i in obj) {
                    if(!obj[i] || obj[i] == ''){
                        alert(i + ' not define');
                        flag = true;
                        return false;
                    }
                }
                data.push(obj);
            }
        });

        if(flag) {
            return false;
        }

        $.post('/review/set_batch_reply', {data: data}, function(data) {
        });
    });
});
</script>
