@section('content')
<head>
<link rel="stylesheet" href="/main/css/download.css" type="text/css" >
</head>
<style>
   .container {
        background-color: #FEF07B;
   } 
</style>
<div class="download-container">
   <div class="app-introduce">
       <span class="app-introduce-picture">
           <img src="/main/img/phonePicture.png" alt="">
       </span>
       <span class="app-item-introduce">
           <span class="app-name">图派</span>
           <span class="app-intro">用社交的方式处理图片</span>
           <span class="appp-abstract">更好玩的创意图片社区</span>
           <span class="app-download-QrCode">
               <!-- 苹果下载 -->
               <span class="Iphone-Qrcode">
                   <img src="/main/img/downloadQrcode.png" alt="">
                   <span class="Iphone-value"> 
                        <i class="Iphone-icon bg-sprite"></i>
                   </span>
               </span>
               <!-- 安卓下载 -->
               <span class="Android-Qrcode">
                   <img src="/main/img/downloadQrcode.png" alt="">
                   <span class="Android-value">
                       <i class="Android-icon bg-sprite"></i>
                   </span>
               </span>
           </span>
       </span>
   </div>
</div>

@endsection
