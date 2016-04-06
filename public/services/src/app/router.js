define('app/router', [ 'marionette' ], function (marionette) {
    'use strict';

    var url     = location.pathname.substr(baseUri.length + '/'.length);
    var paths   = url.split('/');
    if(paths.length == 1)
        url += '/index';
    if(paths[0] == '')
        url = 'index/index';

    require(['app/controllers/'+url], function (controller) {
        controller();
    });
});                
//index/index                   求P大厅+悬浮按钮
//myAsk/myAsk                   我的求P
//myHelp/myHelp                 我的帮P
//myWork/myWork                 我的作品
//post/post                     求P贴内页+回复弹窗+评论弹窗
//uploadOrigin/uploadOrigin     发布求P
//uploadWork/uploadWork         上传作品
//downloadOrigin/downloadOrigin 下载原图
//
//ask/index     求P大厅
//ask/post/page 求P贴内页
//ask/post/deleteComment
//mypage/ask    我的求P
//mypage/help   我的帮P
//mypage/work   我的作品
//upload/origin 发布求P
//upload/work   上传作品
//download      下载原图（大图）