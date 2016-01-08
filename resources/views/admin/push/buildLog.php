<ul class="breadcrumb">
  <li>
    <a href="#">系统模块</a>
  </li>
  <li>推荐App</li>
  <div class="btn-group pull-right">
        <a id="build" class="build">开始打包</a>
    </div>
</ul>

<h5>注意 ＝ ＝ 测试服务器才能打包</h5>
<textarea id="build-log" style="height: 600px; width:100%"></textarea>
<span id="build-tips"></span>

<script>

var log = '';
$("#build").click(function() {
    $.get('buildApk');
    log = '';
    loop();
    $("#build-tips").text('开始打测试包...');
});

function loop () {
    $.get('buildLog', function(data) {

        $("#build-log").val(data.data.log);
        document.getElementById("build-log").scrollTop = document.getElementById("build-log").scrollHeight;

        if(data.data.log == '') {
            $("#build-log").val('等待连接...');
            setTimeout(loop, 1000);
        }
        else if(log != data.data.log) {
            setTimeout(loop, 1000);
        }
        else {
            $("#build-tips").html('打包完成, 请用查看<a href="http://<?php echo env('ADMIN_HOST'); ?>/mobile/apk/load.apk">下载器</a>'
                +', 或者直接<a href="http://<?php echo env('ADMIN_HOST'); ?>/mobile/apk/tupai.apk">下载</a>'
                +', <a href="#" onclick="sendmail()">点击发送体验邮件</a>');
        }
        log = data.data.log;
    });
}

function sendmail() {
    $.get('mailApk');
}
loop();
</script>
