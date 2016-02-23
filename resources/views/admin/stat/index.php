<ul class="breadcrumb">
  <li>
    <a href="#">数据统计</a>
  </li>
  <li>后台数据统计</li>
</ul>

<form class="form-inline">
    <div class="form-group">
        <input name="date" class="form-filter form-control" value="<?php echo date("Ymd"); ?>">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search" >搜索</button>
    </div>
</form>

<br><br>
<div class="container">
<pre>
每日类：

    每日新增注册用户:<?php echo $today_user_count; ?>

    每日新增求P数:<?php echo $today_ask_count; ?>

    每日新增作品数:<?php echo $today_reply_count; ?>

    每日新增进行中数:<?php echo $today_download_count; ?>
</pre>

<pre>
用户类：
    总用户数:<?php echo $user_count; ?>

    纯求P用户总数:<?php echo $only_ask_user_count; ?>

    上传过作品用户总数:<?php echo $reply_user_count; ?>

    有进行中记录用户总数:<?php echo $download_user_count; ?>

    男性用户总数:<?php echo $male_count; ?>

    女性用户总数:<?php echo $female_count; ?>


求助类：
    求助总数量:<?php echo $ask_count; ?>

    求助未被处理求助总数量:<?php echo $ask_no_replies; ?>

    求助只有1个作品的求助总数量:<?php echo $ask_one_reply; ?>

    求助超过1个作品的求助总数量:<?php echo $ask_has_replies; ?>


作品类：
    作品总数量:<?php echo $reply_count; ?>


教程类：
    教程总数量:<?php echo $total_tutorial_count; ?>

    作业总数量:<?php echo $total_homework_count; ?>


其他类：
    进行中总数量:<?php echo $download_count; ?>

    总评论数:<?php echo $comment_count; ?>

    总点赞数:<?php echo $like_count; ?>

    总分享数:<?php echo $share_count; ?>

    总收藏数:<?php echo $collect_count; ?>

    总互相关注数量:<?php echo $follow_focus_count ?>

</pre>
</div>
