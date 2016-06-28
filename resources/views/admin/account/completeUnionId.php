<ul class="breadcrumb">
  <li>
    <a href="#">用户模块</a>
  </li>
  <li>补全unionid</li>
</ul>



<form class="" style="width: 40%;" name="new_msg" method="post" action="##">
	<fieldset>
		<div id="legend" class="">
			<legend class="">补全unionid</legend>
		</div>

		<div class="form-group">
			<!-- Button -->
			<div class="controls">
				<button class="btn btn-info merge">确定补全</button>
			</div>
		</div>
	</fieldset>
</form>

<script>
$(document).ready(function(){

	$('button.merge').on('click', function(e){
		e.preventDefault();

		var mergeAtSameTime = $('#merge').val();
		$.post('/account/completeUID', { merge_users: mergeAtSameTime }, function( data ){
			data = data.data;
			if( data.result == 'ok' ){
				toastr['success']('处理成功');
			}
		})
	});

});
</script>
