<ul class="breadcrumb">
  <li>
    <a href="#">系统模块</a>
  </li>
  <li>推荐App</li>
  <div class="btn-group pull-right">
        <a href="#add_app" data-toggle="modal"  data-target="#add_app" class="add">添加App</a>
    </div>
</ul>

<form class="form-line" method="POST">
    <div class="form-group">
        <input name="uid" class="form-filter form-control" placeholder="自己的uid" value="577">
    </div>
    <div class="form-group">
        <select name="type" class="form-filter form-control">
            <option value='post_ask'>发布求助</option>
            <option value='post_reply'>发布作品</option>
            <option value='like_reply'>点赞作品</option>
            <option value='comment_ask'>评论求助</option>
            <option value='comment_reply'>评论作品</option>
            <option value='follow'>关注</option>
            <option value='like_ask' disabled='disabled'>点赞求助</option>
            <option value='comment_comment' disabled='disabled'>评论评论</option>
            <option value='invite' disabled='disabled'>邀请</option>
        </select>
    </div>
    <div class="form-group" id="post">
        <input name="target_id" class="form-filter form-control" placeholder="ID">
    </div>
    <div class="form-group">
        <button type="submit" class="form-filter form-control" id="search">搜索</button>
    </div>
</form>
