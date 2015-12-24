define([ 'common', 'uploadify','app/views/Base'],
    function (Conmmon, uploadify, View) {
        "use strict";
        
        return View.extend({

            construct: function () {
                var self = this; 
                $(".ask-uploading-popup").fancybox({ 
                    afterShow: function(){
                        $(".new-label span").unbind('click').bind('click', self.spanChange);
                    }
                });
                Common.upload("#upload_picture", function(data){
                    $("#ask-uploading-popup input[name='show-picture']").val(data.data.url);
                    $("#ask-uploading-popup .show-picture").attr("src", data.data.url);
                    $('.upload-middle').addClass('opacity'); 
                    $('.show-picture').removeClass('opacity');                  
                    $("#upload_picture").attr("upload-id", data.data.id);
                    $(".upload-accomplish").removeClass('disable').unbind('click').bind('click', self.upload);
                }, null, {
                     url: '/upload'
                });
            },
            spanChange: function(e) {
                $(e.currentTarget).toggleClass("new-change");
            },
            upload: function() {
                if ($("#ask-content-textarea").val().length > 0 && $(".new-label span").hasClass("new-change")) {
                    var upload_id = $("#upload_picture").attr("upload-id");
                    var category_id = $("#attrChannelId").attr("data-id");
                    var desc      = $("#ask-uploading-popup .ask-content").val();
                    var tag_ids   = [];
                    for(var i = 0; i < $(".new-label span").length; i++) {
                        if($(".new-label span").eq(i).hasClass("new-change")) {
                            tag_ids.push($(".new-label span").eq(i).attr("id"));
                        }
                    };
                    if( !upload_id ) {
                        error('上传求P图','上传求P图');
                        return false;
                    }
                    $.post('asks/save', {
                        upload_id: upload_id,
                        desc: desc,
                        tag_ids: tag_ids,
                        category_id: category_id
                    }, function(data) {
                        // $.fancybox.close();
                        history.go(-1);
                        toast('上传成功',function(){
                            location.href = '/#askflows/'+ category_id;
                            $(".menu-bar-item").removeClass('active');
                        });
                    });
                    $("#upload_picture").attr("upload-id", '');
                    $(".upload-accomplish").parent().parent().find(".ask-content").val('');
                } else {
                    alert("请描述并选择标签！")
                }


            }
        });
    });
