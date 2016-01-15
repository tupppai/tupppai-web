<ul class="breadcrumb">
  <li>
    <a href="#">交易模块</a>
  </li>
  <li>批量充值</li>
</ul>



<form class="" style="width: 40%;" name="new_msg" method="post" action="##">
	<fieldset>
		<div id="legend" class="">
			<legend class="">批量充值</legend>
		</div>

		<div class="form-group">
			<!-- Select Basic -->
			<label class="">充值金额</label>
			<div class="controls">
				<input type="text" name="amount" class="form-control"/>
			</div>
		</div>

		<div class="form-group">
			<!-- Search input-->
			<label class="">接受者</label>
			<div class="controls">
				<select name="receiver_uids" multiple>
					<?php
						foreach( $users as $user ){
							$oper = [];
							if( $user->status == -6 ){
								$oper[] = 'disabled="disabled"';
								$oper[] = 'readonly="readonly"';
							}
							$oper = implode(' ', $oper);
		                    echo "<option value=\"{$user['uid']}\" {$oper}>({$user['uid']}){$user['nickname']}</option>";
		                }
	                ?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class=""></label>

			<!-- Button -->
			<div class="controls">
				<button class="btn btn-info recharge">确定充值</button>
			</div>
		</div>
	</fieldset>
</form>

<link href="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-multiselect/bootstrap-multiselect.min.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript"></script>

<script>
$(document).ready(function(){
	$('select[name="receiver_uids"]').multiselect({
		enableFiltering: true,
		maxHeight: 200
	});
	$('button.recharge').on('click', function(e){
		e.preventDefault();
		if(!confirm('确定要批量充值吗？')){
			return false;
		}
		var uids=$('select[name="receiver_uids"]').val();
		var amount = $('input[name="amount"]').val();

		$.post('/account/recharge_for_users', { uids: uids, amount: amount }, function( data ){
			data = data.data;
			if( data.result == 'ok' ){
				toastr['success']('批量充值成功');
				location.reload();
			}
		})
	});

});
</script>
