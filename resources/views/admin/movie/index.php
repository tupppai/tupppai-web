<ul class="breadcrumb">
  <li>
	<a href="#">VOD</a>
  </li>
  <li>上传视频</li>
</ul>

<form class="" style="width: 50%;" name="new_msg" method="post" action="##">
	<fieldset>
		<div id="legend" class="">
			<legend class="">上传视频</legend>
		</div>

		<div class="form-group">
			<label>选择文件</label>
			<div class="controls">
				<img id="logo_preview" class="img-display" />
				<input type="hidden" name="pic_url"/>
				<input type="file" id="video_upload" class="btn blue" value="上传视频" />
			</div>
		</div>
	</fieldset>
  </form>

  <ul id="movie_list">
  </ul>

  <video id="player" controls>
	<source src=""/>
  </video>

<script>
	function loadLogo(data){
		Common.preview('logo_preview', data);
		$('#logo_preview').attr('data-id', data.data.id);
		$("input[name='pic_url']").val(data.data.url);
	}

	$(function(){
		Common.upload('#video_upload',loadLogo, function(){refresh_list();} , {url:'/movie/upload'});
		refresh_list();

		$('#movie_list').on('click', '.playbtn', function(e){
			e.preventDefault();
			var player = document.getElementById('player');
			player.pause();

			var url = $(this).attr('data-url');
			var source = $('#player source').attr({'src': url});
			player.load();
			player.play();
		});
	});
	function refresh_list(){
		$.get('/movie/list', function( data ){
			data = data.data;
			var ul = $('#movie_list');
			ul.empty();
			$.each( data, function( i, n ){
				var a = $('<a>').text(n.savename);
				a.attr({'data-url':n.url, 'class': 'playbtn'});
				var li = $('<li>').append( a );
				ul.append( li );
			})
		});
	}
</script>
<style>
video{
	width: 400px;
	height: 300px;
	outline: 1px solid lime;
}
#logo_preview{
	height: 50px;
	width:50px;
	border-radius: 12px !important;
	border: 1px solid lightgray;
}
.uploadify {
	position: absolute;
	right: 0px;
	top: 50%;
}
</style>
