define(["app/views/base","tpl!app/views/uploadsuspend/UploadSuspendView.html"],function(e,t){"use strict";return e.extend({tagName:"div",className:"",template:t,onShow:function(){var e={};e.code=$("body").attr("data-code"),share_friend(e,function(){},function(){}),share_friend_circle(e,function(){},function(){})}})});