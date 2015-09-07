$(function() {
 
    /**
    * 登录代码
    * @author brandwang
    */
    $('#login_btn').click(function() {
        var username = $('#login_name').val();
        var password = $('#login_password').val();
        
        if (username == '') {
            alert('!');   
        } else if (password == '') {
            alert('?');    
        } else {
            var url  = "/user/login";
            var data = {
                'username': username,
                'password': password
            };
            psAjax(url, 'POST', data, function(data) {
                if (data.ret == 1) {
                    var loginModal = $('[data-remodal-id=login-modal]').remodal();
                    if (loginModal.getState() == 'opened') {
                        loginModal.close();    
                    }
                    //登录成功之后刷新页面
                    window.location.reload();
                } 
            });
        }
    }); 
});

//唤起登录框
function call_login_modal() {
    var loginModal = $('[data-remodal-id=login-modal]').remodal();
    if (loginModal.getState() == 'closed') {
        loginModal.open();    
    }
<<<<<<< HEAD
    var WechatQrcodeModal = $('[data-remodal-id=Wechar-Qrcode-modal]').remodal();

    var registerModal = $('[data-remodal-id=Register_modal').remodal();

    var uploadProductionModal = $('[data-remodal-id=uploading_modal]').remodal();
=======
>>>>>>> 43a91ef6bf014e610cb39868a8cc3fffd31d13f3
}

/**
 * 对ajax请求的二次封装
 * 对后台返回的信息进行统一处理
 *
 * @author brandwang
 */
function psAjax(url, type, params, callback) {
    $.ajax({
        url: url,
        type: type,
        data: params,
        success: function(data) {
            //请求失败
            if (data.ret == 0) {
                //user not login
                if (data.code == 1) {
                    call_login_modal();
                } else {
                    //TODO error handler
                    console.log(data.info);        
                }    
            } else {
                //成功后执行回调
                callback(data);
            }
        },
        error: function(info) {
            console.log(info);
        }
    });
}
