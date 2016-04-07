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
//ask/index                     求P大厅
//ask/detail                    求P贴内页
//modal/deleteComment           求P内页删除弹窗
//personal/ask                  我的求P
//personal/reply                我的帮P
//personal/work                 我的作品
//upload/ask                    发布求P
//upload/reply                  上传作品
//download/index                下载原图（大图）