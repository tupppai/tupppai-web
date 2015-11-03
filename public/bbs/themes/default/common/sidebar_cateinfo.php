<div class="panel panel-default-10">
	<span class="title-icon bg-sprite"></span>
    <div class="panel-heading">
        <?php echo $cate['cname'];?>
    </div>
    <div class="panel-body">
        <p><?php echo $cate['content'];?></p>
        <!-- <a href="<?php echo url('node_show',$cate['node_id']);?>" class="btn btn-default btn-sm">此节点</a> -->
        <div class="topic-2">
        	
		<a href="<?php echo site_url('topic/add/'.$cate['node_id'])?>" class="topic">+ 新话题</a>
		<a href="<?php echo url('topic_show',$content['previous'])?>" class="upper"><i class="left-icon bg-sprite"></i>上一贴 </a>
		<a href="<?php echo url('topic_show',$content['next'])?>" class="below">下一贴 <i class="right-icon bg-sprite"></i></a>
        </div>
    </div>
</div>

