<!DOCTYPE html>
<html>
<head>
<title><?php echo $content['title']?> - <?php echo $settings['site_name']?></title>
<meta charset='UTF-8'>
<meta content='True' name='HandheldFriendly'>
<meta content='width=device-width, initial-scale=1.0' name='viewport'>
<meta name="keywords" content="<?php echo $content['keywords']?>" />
<meta name="description" content="<?php echo $content['description'];?>" />
<?php $this->load->view('common/header-meta');?>
<script src="<?php echo base_url('static/common/js/topic.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('static/common/js/plugins.js')?>" type="text/javascript"></script>
<script src="<?php echo base_url('static/common/js/jquery.upload.js')?>" type="text/javascript"></script>
<?php if($this->config->item('storage_set')=='local'){?>
<script src="<?php echo base_url('static/common/js/local.file.js')?>" type="text/javascript"></script>
<?php } else{?>
<script src="<?php echo base_url('static/common/js/qiniu.js')?>" type="text/javascript"></script>
<?php }?>
    
</head>
<body name="top">
<?php $this->load->view('common/header'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default-1">
                    <div class="panel-heading topic-detail-heading">
                        <div class="pull-right">
                            <a href="<?php echo site_url('user/profile/'.$content['uid']);?>">
                                <img src="<?php echo $content['avatar'];?>" alt="<?php echo $content['username']?>';?>">
                            </a>
                        </div>
                        <p>
                        <a class="homeColor" href="<?php echo base_url();?>">首页</a> / <a href="<?php echo site_url('node/show/'.$cate['node_id']);?>"><?php echo $cate['cname'];?></a> / <a><?php echo $content['title']?></a></p>
                        <h2 class="panel-title"><?php echo $content['title']?></h2>
                        <small class="text-muted">
                            <span>By <a href="<?php echo site_url('user/profile/'.$content['uid']);?>"><?php echo $content['username']; ?></a></span>&nbsp;•&nbsp;
                            <span><?php echo date('Y-m-d H:i:s',$content['addtime']);?></span>&nbsp;•&nbsp;
                            <span><?php echo $content['views']?>次点击</span>
                            <?php if($this->session->userdata('uid')){?>
                            <span>• <a href="javascript:void(0)" class="reply">回复</a></span>
<!--
							<?php if($in_favorites){?>
							<span><a href="<?php echo site_url('favorites/del/'.$content['topic_id']);?>" title="取消收藏">取消收藏</a></span>
							<?php } else {?>
							<span><a href="<?php echo site_url('favorites/add/'.$content['topic_id']);?>" title="点击收藏">收藏</a></span>
                            <?php } ?>                 
-->
                            <?php } ?>
                        </small>
                    </div>
                    <?php if($page==1){?>
                    <div class="panel-body content">
                    <span class="work-icon-1">
                        <!-- <span class="work-icon bg-sprite"></span> -->
                        <?php echo $content['content']?>
                        <?php if(isset($tag_list) && $this->auth->is_admin()){?>
						<p class="tag">
						<?php foreach($tag_list as $tag){?>
						<a href='<?php echo site_url($tag['tag_url']);?>'><?php echo $tag['tag_title'];?></a>&nbsp;
						<?php }?>
						</p>
						<?php }?>
                    </span>
                    </div>
                    <?php }?>
                    <div class="panel-footer">
						<?php if($this->auth->is_user($content['uid']) || $this->auth->is_admin() || $this->auth->is_master($cate['node_id'])){?>
						
						<a href="<?php echo site_url('topic/edit/'.$content['topic_id']);?>" class="btn btn-default btn-sm unbookmark" data-method="edit" rel="nofollow">编辑</a>
						<a href="javascript:if(confirm('确实要删除吗?'))location='<?php echo site_url('topic/del/'.$content['topic_id'].'/'.$content['node_id'].'/'.$content['uid']);?>'" class="btn btn-sm btn-danger" data-method="edit" rel="nofollow">删除</a>
						<?php }?>
						<?php if($this->auth->is_admin() || $this->auth->is_master($cate['node_id'])){?>
						<a href="<?php echo site_url('topic/show/'.$content['topic_id'].'?act=set_top');?>" class="btn btn-default btn-sm unbookmark" data-method="edit" rel="nofollow">
						<?php if($content['is_top']==0){?>
						置顶
						<?php } else {?>
						取消置顶
						<?php }?>
						</a>
						<?php }?>
                    </div>
                </div><!-- /.panel content -->
                <div class="panel panel-default-2">
                    <div class="panel-heading">
                        <h5><span id="comments"><?php echo $content['comments']?></span><span> 回复 <span class="line-icon-1">|</span> 直到<?php echo date('Y-m-d H:i',time()); ?></span><a href="javascript:void(0)" class="pull-right"><span class="text-muted">添加回复</span></a></h5>
                    </div>
                    <div class="panel-body">
	                       <ul id="comment_list">
                            <?php foreach ($comment as $key=>$v):?>
                             <div class="row" id="r<?php echo ($page-1)*10+$key+1;?>">
                                <div class="col-md-1">
                                    <a href="<?php echo site_url('user/profile/'.$v['uid']);?>">
                                    <img class="img-rounded" src="<?php echo $v['avatar'];?>" alt="<?php echo $v['username'].'_avatar';?>">
                                    </a></div>
                                    <div class="col-md-11 reply-body"><h5><span><a href="<?php echo site_url('user/profile/'.$v['uid']);?>"><?php echo $v['username']; ?></a>&nbsp;&nbsp;<?php echo friendly_date($v['replytime'])?></span><span class='right' id="r<?php echo ($page-1)*10+$key+1;?>">#<?php echo ($page-1)*10+$key+1;?> <a href="#reply" class="clickable"  data-mention="<?php echo $v['username']?>">回复</a></span></h5>
                                    <p>
                                    <?php echo $v['content']; ?>
                                    </p>
									<?php if($this->auth->is_admin() || $this->auth->is_master($cate['node_id'])){?>
									<p class="pull-right link-text-muted"><a href="javascript:if(confirm('确实要删除吗?'))location='<?php echo site_url('comment/del/'.$content['node_id'].'/'.$v['topic_id'].'/'.$v['id']);?>'"><span class="glyphicon glyphicon-trash"></span> 删除</a><?php }?>
									<?php if($this->auth->is_user($v['uid']) || $this->auth->is_admin() || $this->auth->is_master($cate['node_id'])){?>
									 <a href="<?php echo site_url('comment/edit/'.$content['node_id'].'/'.$v['topic_id'].'/'.$v['id']);?>"><span class="glyphicon glyphicon-align-left"></span> 编辑</a></p>
									 <?php }?>
                                    </div>

                                <!-- 评论对应回复框 -->
                                <div class="col-md-11 right mt-l reply-comment-area hide">
                                    <textarea class="form-control reply-comment-textarea"></textarea>
                                    <button class="right btn-primary btn reply-comment-btn mt-m">回复</button>
                                </div>
                            </div>
                            <hr class="smallhr">
                            <?php endforeach; ?>
                            </ul>        
                        <?php if($pagination):?><nav class="nav"><ul class="pager"><?php echo $pagination;?></ul></nav><?php endif?>
                        
                    </div>
                </div><!-- /.panel comment -->
                <div id="error"></div>
                <div class="panel panel-default-01" id="Reply">
                    <div class="panel-heading">
                        <h4 class="panel-title">回复</h4>
                    </div>
                    <div class="panel-body">
                        <?php if($this->auth->is_login()):?>
                        <input type="hidden" id="token" name="<?php echo $csrf_name;?>" value="<?php echo $csrf_token;?>">
						<input name="topic_id" id="topic_id" type="hidden" value="<?php echo $content['topic_id']?>" />
						<input name="is_top" id="is_top" type="hidden" value="<?php echo $content['is_top']?>" />
						<input name="username" id="username" type="hidden" value="<?php echo $myinfo['username']?>" />
						<input name="avatar" id="avatar" type="hidden" value="<?php echo $myinfo['avatar']?>" />
						<input name="lastpost" id="lastpost" type="hidden" value="<?php echo $myinfo['lastpost']?>" />
                            <div class="form-group">
	                            <textarea class="form-control" id="post_content" name="comment" rows="5"></textarea>
	                            <span class="help-block red"><?php echo form_error('content');?></span>
							    <p>
								<!-- <span class="text-muted">可直接粘贴链接和图片地址/发代码用&lt;pre&gt;标签</span> -->
								<span class="pull-right">
								<?php if($this->config->item('storage_set')=='local'){?>
								<input id="upload_file" type="button" value="图片/附件" name="file" class="btn btn-default pull-right">
								<?php } else {?>
								<input id="upload_file" type="button" value="图片/附件"  class="btn btn-default">
								<?php }?></span>
								</p>
                            </div>
                            <button type="submit" class="btn btn-primary" id="comment-submit">回复</button>
                        <?php else : ?>
                            <div class="text-center">
                                <a class="btn-default-11" href="<?php echo site_url('user/login');?>">登录</a>发表 or 还没有账号？去<a class="btn-default-12" href="<?php echo site_url('user/register');?>">注册</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div><!-- /.panel add comment -->
            </div><!-- /.col-md-8 -->

			<div class="col-md-4">
			<?php $this->load->view('common/sidebar_login');?>
			<?php $this->load->view('common/sidebar_cateinfo');?>
			<?php $this->load->view('common/sidebar_cates');?>
			<?php $this->load->view('common/sidebar_related_topic');?>
			<?php $this->load->view('common/sidebar_ad');?>
			</div><!-- /.col-md-4 -->

        </div><!-- /.row -->
    </div><!-- /.container -->



<?php $this->load->view('common/footer');?>
</body>
</html>
<script>
    var src = $('.work-icon-1 img').attr('src');
    if( src == undefined) {
        $('.work-icon').addClass('hide');
    }
</script>
