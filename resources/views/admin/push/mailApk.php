<table style="width:100%;padding:20px;background-color:#efefef;" class="container">
    <tbody>
        <tr>
            <td>
            </td>
            <td style="width:640px;">
                <table style="width:100%;padding-top:30px;margin:10px auto;border-collapse:separate;"
                cellpadding="0" cellspacing="0" class="email-header">
                    <tbody>
                        <tr>
                            <td style="color:#ccc;font-size:14px;padding:15px 30px 5px 30px;text-align:left;">
                            </td>
                            <td style="color:#ccc;font-size:14px;padding:15px 30px 5px 30px;text-align:right;">
                                <a href=""
                                target="_blank" style="color:#999;text-decoration:none;line-height: 20px;" class="light-grey">
                                    图派
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table style="width:100%;margin:10px auto;background:#fff;border-radius:3px;"
                cellpadding="0" cellspacing="0" class="email-container">
                    <tbody>
                        <tr class="email-title">
                            <td style="padding:30px;text-align:center;color:#15a4fa;">
                                <div class="email-title">
                                    <div style="font-size:24px;line-height:1.2;margin:10px 0;" class="title">
                                        <?php 
                                        $title = array();
                                        foreach ($gitpushes as $push) { 
                                            $title[] = $push->title;
                                        }
                                        echo implode(',', $title);
                                        ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="email-body">
                            <td class="email-body-td">
                                <table class="email-body-item">
                                    <tbody>
                                        <tr>
                                            <td class="email-hello" >
                                                <div  class="content">
                                                    您好，
                                                    <a href="mailto:billqiang@qq.com" target="_blank">
                                                        <?php echo $email; ?>
                                                    </a>
                                                    ：
                                                </div>
                                                <?php 
                                                foreach($towerpushes as $tower) {
                                                    echo '<div  class="content">'.$tower->title.'</div>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="auth-code">
                                                <div class="content">
                                                    <a href="http://tadmin.tupppai.com/mobile/apk/tupai.apk" class="btn-primary" target="_blank">
                                                        点击下载
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table  class="email-body-item">
                                    <tbody>
                                        <tr>
                                            <td  class="email-meta">
                                                <div  class="email-extra">
                                                    按钮若无法点击？请尝试将下面的链接复制到浏览器地址栏再回车：
                                                </div>
                                                <div  class="email-extra">
                                                    <a href="http://tadmin.tupppai.com/mobile/apk/tupai.apk" target="_blank"> http://tadmin.tupppai.com/mobile/apk/tupai.apk </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table  class="email-body-item">
                                    <tbody>
                                        <tr class="email-footer">
                                            <td class="email-meta">
                                                <div style="" class="">
                                                    如果不知道为什么收到该邮件，请不要做任何操作。
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table style="width:100%;padding-bottom:30px;margin:10px auto;border-collapse:separate;"
                cellpadding="0" cellspacing="0" class="email-footer">
                    <tbody>
                        <tr>
                            <td style="color:#999;font-size:14px;padding:5px 30px;text-align:left;">
                                <a href=""
                                target="_blank" style="color:#999;text-decoration:none;" class="light-grey">
                                    图派 © 2015
                                </a>
                            </td>
                       
                        </tr>
                    </tbody>
                </table>
            </td>
            <td>
            </td>
        </tr>
    </tbody>
</table>

<style>
.email-footer .email-meta {
    color:#999;font-size:14px;padding:20px 0;
}
.email-footer  .email-extra {
.border-top:1px solid #eeeff2;padding-top:30px;    
}
.email-body-item {
    width: 100%;
    padding-top: 20px;
}
    a {
            color: #4e5d80;
    }
    table {
        border-color: grey;
    }
    .email-extra {
        margin-bottom: 10px;
    }
.email-body-item {
    width:100%;margin-bottom:10px;
}
table {
    font-family: "lucida Grande",Verdana;
}
.email-hello {
    .font-size:16px;line-height:24px;
}
.email-hello div {
    color:#323232;padding:5px 0;
}
.auth-code {
    .font-size:16px;line-height:24px; 
}
.auth-code .content {
    color:#323232;text-align:center;margin:15px 0;
}
.auth-code .content a {
    color:#fff;border:0;border-radius:40px;padding:8px 28px;font-size:14px;line-height:18px;font-weight:normal;text-align:center;vertical-align:middle;cursor:pointer;outline:none;display:inline-block;margin:5px 0;background:#15a4fa;text-decoration:none;
}
.auth-code .content h3 {
    color: #323232
}
.email-meta {
    color:#999;font-size:14px;padding:10px 0;
}
.email-meta .email-extra {
    .padding-top:30px;
    word-wrap:break-word;word-break:break-all;
}

.email-extra a {
    color:#999;text-decoration:none;
}
.email-body-td {
    padding:10px 30px;
}
</style>
