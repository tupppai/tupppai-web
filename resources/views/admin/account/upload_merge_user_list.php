<ul class="breadcrumb">
  <li>
    <a href="#">用户模块</a>
  </li>
  <li>用户合并</li>
</ul>



<form class="" style="width: 40%;" name="new_msg" method="post" action="##">
	<fieldset>
		<div id="legend" class="">
			<legend class="">用户合并</legend>
		</div>

		<div class="form-group">
			<label class="">合并用户列表。（要求一行一对，左边的uid为旧帐号，右边的uid为新帐号，两个uid之间请用空格隔开。）左边的帐号内容会作为右边帐号的内容。</label>
			<textarea class='form-group' name='uid_list'></textarea>
		</div>

		<div class="form-group">
			<!-- Button -->
			<div class="controls">
				<button class="btn btn-info merge">确定合并</button>
			</div>
		</div>
	</fieldset>
</form>

<script>
$(document).ready(function(){

	$('button.merge').on('click', function(e){
		e.preventDefault();
		if(!confirm('要合并这些用户吗？')){
			return false;
		}

		if(prompt('确定要合并这些用户吗？\n请输入“确定”。') != '确定'){
			return false;
		}

		if(!confirm('合并用户是一个不可逆的过程，请再次确定。')){
			return false;
		}

		var uids = $('textarea[name="uid_list"]').val();
		if( !uids ){
			alert('请填写充值金额');
			return false;
		}

		$.post('/account/merge_user', { merge_uids: uids }, function( data ){
			data = data.data;
			if( data.result == 'ok' ){
				toastr['success']('用户合并成功');
				location.reload();
			}
		})
	});

});
</script>
