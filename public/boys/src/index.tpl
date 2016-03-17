<!DOCTYPE html>
<html>
<head> 
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="whenjonny">
    <meta name="keywords" content="whenjonny">
    <!-- h5  -->
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon" /> 
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <!-- require conifg-->
    <%
        var baseUri = 'boys';
        var code = new Date().getTime(); 
        var min = (env == 'dev')? '': '.min';
        var src = (env == 'dev')? 'src': 'res';
    %>
    <script>
        var baseUri = '/<%= baseUri %>';

        var require = {
            urlArgs : "v=<%= code %>",
            timeout : 100,
        };
    </script>    

    <link rel="stylesheet" type="text/css" href="/css/swiper<%= min %>.css?<%= code %>"  >
    <link rel="stylesheet" type="text/css" href="/css/main<%= min %>.css?<%= code %>"  >
    <!-- 合并后的js文件在script-build/src -->
    <script data-main="/<%= baseUri %>/<%= src %>/main" src="/<%= baseUri %>/<%= src %>/lib/require/require.js"></script>
    <!--[if IE]>
         <script src="./<%= baseUri %>/<%= src %>/lib/respond/respond.js" ></script>
         <script src="./<%= baseUri %>/<%= src %>/lib/es5/es5-sham.js" ></script>
         <script src="./<%= baseUri %>/<%= src %>/lib/mediaqueries/css3-mediaqueries.js" ></script>
         <script src="./<%= baseUri %>/<%= src %>/lib/PIE/PIE.js" ></script>
    <![endif]--> 
    <title>图派</title>
</head> 
 
<body>
    <div class="container">
        <div class="inner-container" id="contentView">
        </div>  
        <div class="clear"></div>        
    </div>   
    </body>
</html>
