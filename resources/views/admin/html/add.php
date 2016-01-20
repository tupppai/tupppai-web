<ul class="breadcrumb">
  <li>
    <a href="#">系统模块</a>
  </li>
  <li><a href="/html/index">静态页面</a></li>
  <li>添加静态页面</li>
</ul>

<script src="<?php echo $theme_dir; ?>assets/global/plugins/umeditor/umeditor.config.js" type="text/javascript"></script>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/umeditor/_examples/editor_api.js" type="text/javascript"></script>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/umeditor/lang/zh-cn/zh-cn.js" type="text/javascript"></script>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/umeditor/themes/default/_css/umeditor.css" type="text/css" rel="stylesheet">

<input id="screen-width" type='text' value='480'/> 
<button id="change">修改宽度</button>
<input type='hidden' id='id' value='<?php echo $id; ?>'/>
<input type='text' id='title' placeHolder='标题' value='<?php echo $title; ?>'/>
<button id='submit'>保存</button>

<!--style给定宽度可以影响编辑器的最终宽度-->
<script type="text/plain" id="editor" style="width:480px;height:640px; "><?php echo $content;?></script>

<script type="text/javascript">
var table = null;
//实例化编辑器
var um = UM.getEditor('editor');
um.addListener('blur',function(){ });
um.addListener('focus',function(){ });
//UM.getEditor('editor').getContent();
//UM.getEditor('editor').setContent('text', boolean);
//UM.getEditor('editor').hasContents();
$(function() {
    $('#change').click(function() {
        var value = $("#screen-width").val();
        $(".edui-container").css('width', value + 'px');
    });
    $('#submit').click(function() {
        $.post('/html/set_html', {
            content: UM.getEditor('editor').getContent(),
            title: $('#title').val(),
            id: $("#id").val()
        }, function(data) {
            location.href = '/html/index';
        });
    });
});
</script>
