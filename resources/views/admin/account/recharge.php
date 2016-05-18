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
				<input type="text" name="receiver_uids">
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

<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/css/token-input.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/css/token-input-facebook.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/css/token-input-mac.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-tokeninput/js/jquery.tokeninput.js" type="text/javascript"></script>
<script>

$(document).ready(function(){

	$('input[name="receiver_uids"]').tokenInput("/account/search_valid_users",{
		propertyToSearch: 'username',
		jsonContainer: 'data',
		theme: "facebook",
		// preventDuplicates: true,
		//tokenValue: 'data-id',
		resultsFormatter: function(item){
			var genderColor = item.sex == 1 ? 'deepskyblue' : 'hotpink';
			return "<li>" +
			"<img src='" + item.avatar + "' title='" + item.username + " " + item.nickname + "' height='25px' width='25px' />"+
			"<div style='display: inline-block; padding-left: 10px;'>"+
				"<div class='username' style='color:"+genderColor+"'>" + item.username + "</div>"+
				"<div class='nickname'>" + item.nickname + "</div>"+
			"</div>"+
			"</li>" },
		tokenFormatter: function(item) {
			return "<li class='token-input-token-facebook receiver_uids' data-id='"+item.uid+"'>" +
			"<a href='/user/profile/"+item.uid+"'>"+item.username + "</a>-" +
			item.nickname + "("+item.uid+')'+"</li>";
		},
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
