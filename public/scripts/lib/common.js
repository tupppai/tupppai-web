$(function() {

    $("#login_btn").click(function() {
        
        var username = $('#login_name').val();
        var password = $('#login_password').val();

        if (username == '') {
            alert('!');   
        } else if (password == '') {
            alert('?');    
        } else {
        user.data = {
            'username': username,
            'password': password
        };
        $.post("", data, function(){
            var loginModal = $('[data-remodal-id=login-modal]').remodal();
            if (loginModal.getState() == 'opened') {
                loginModal.close();    
            }
            window.location.reload();
        });
    });
});
