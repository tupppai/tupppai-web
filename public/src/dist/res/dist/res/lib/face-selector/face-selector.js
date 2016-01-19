//表情选择插件
(function($){  
	$.fn.emojiSelector = function(options) {
		var defaults = {
			id : 'facebox', //表情框id
			path : 'face/', 
			assign : 'content' //输入框id
		};

		var option = $.extend(defaults, options);
		var assign = $('#' + option.assign);
		var id = option.id;
		var path = option.path;
	
		// 表情文件对应shortname
		var faces = [
			'angry', 'anguished', 'astonished', 'blush', 'broken_heart', 'clap', 'cold_sweat', 'confounded', 'confused', 'cry', 'disappointed', 'dizzy_face', 'expressionless', 'fearful', 'flushed', 'frowning', 'grimacing', 'grin', 'grinning', 'heart_eyes', 'heart', 'hushed', 'innocent', 'joy', 'kissing_closed_eyes', 'kissing_heart', 'kissing', 'laughing', 'mask', 'muscle', 'ok_hand', 'open_mouth', 'pensive', 'persevere', 'point_down', 'point_left', 'point_right', 'pray', 'relaxed', 'relieved', 'satisfied', 'scream', 'sleeping', 'sleepy', 'smile', 'smiley', 'smirk', 'sob', 'stuck_out_tongue_closed_eyes', 'stuck_out_tongue_winking_eye', 'sunglasses', 'sweat_smile', 'sweat', 'thumbsdown', 'thumbsup', 'unamused', 'v', 'wink', 'wink2', 'zzz'
		];

		if(assign.length <= 0){
			alert('缺少表情赋值对象。');
			return false;
		}
		
		$(this).click(function(e){
			var strFace, labFace;
			if($('#' + id).length <= 0){
				strFace = '<div id="' + id + '" style="position:absolute;display:none;z-index:1000;" class="qqFace">' +
							  '<table border="0" cellspacing="0" cellpadding="0"><tr>';
				for(var i = 1; i <= 60; i++) {
					labFace = faces[i - 1];
					labFaceStr = ':' + labFace + ':';

					strFace += '<td><img class="emoji-selector-icon" src="' + path + labFace +'.png" onclick="$(\'#'+option.assign+'\').insertAtCaret(\'' + labFaceStr + '\');" /></td>';
					
					if( i % 15 == 0 ) strFace += '</tr><tr>';
				}
				strFace += '</tr></table></div>';
			}
			$(this).parent().append(strFace);
			
			var offset = $(this).position();
			var top = offset.top + $(this).outerHeight();

			$('#'+id).css('top',top);
			$('#'+id).css('left',offset.left);
			$('#'+id).show();

			e.stopPropagation();
		});

		$(document).click(function(){
			$('#' + id).hide();
			$('#' + id).remove();
		});
	};

})(jQuery);

jQuery.fn.extend({ 
	// 在光标位置插入表情
	insertAtCaret: function(textFeildValue, src) { 
		var textObj = $(this).get(0); 

		if(document.all && textObj.createTextRange && textObj.caretPos){ 
			var caretPos=textObj.caretPos; 

			caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? 
				textFeildValue + '' : textFeildValue; 
		} else if(textObj.setSelectionRange){ 
			var rangeStart = textObj.selectionStart; 
			var rangeEnd = textObj.selectionEnd; 
			var tempStr1 = textObj.value.substring(0, rangeStart); 
			var tempStr2 = textObj.value.substring(rangeEnd); 
			
			textObj.value = tempStr1 + textFeildValue + tempStr2; 
			textObj.focus(); 
			
			var len = textFeildValue.length; 
			textObj.setSelectionRange(rangeStart + len,rangeStart + len); 
			textObj.blur(); 
		}else{ 
			textObj.value += textFeildValue; 
		} 
	} 
});