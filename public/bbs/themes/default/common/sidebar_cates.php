<div class="panel panel-default">
    <div class="panel-heading">
        <a href="<?php echo site_url('node')?>">
            <span class="comment-icon-bbs bg-sprite"></span>
            <h3 class="panel-title">所有图派讨论区</h3>
        </a>
    </div>
    <div class="panel-body">
	    <?php if($catelist[0]){?>
    	<?php foreach ($catelist[0] as $v){?>
        <a href="<?php echo url('node_show',$v['node_id']);?>"><?php echo $v['cname'];?></a>
    	<!-- <p><span class="text-muted"><?php echo $v['cname']; ?></span></p> -->
        <?php if(isset($catelist[$v['node_id']])) foreach($catelist[$v['node_id']] as $c){?>
		<a href="<?php echo url('node_show',$c['node_id']);?>"><?php echo $c['cname']?></a>
		<?php }?>
		<?php }?>
		<?php }?>
    </div>
</div>
