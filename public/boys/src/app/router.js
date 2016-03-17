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
/*

index/index     首页
selectmale/selectmale   制作
getavatar/getavatar     获得头像
shareavatar/shareavatar  分享成功   
uploadagain/uploadagain     重新上传头像
uploadsuccess/uploadsuccess     上传成功
uploadsuspend/uploadsuspend     头像制作达到上限

*/