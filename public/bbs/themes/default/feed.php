<?php echo '<?xml version="1.0" encoding="utf-8" ?>'?>
<rss version="2.0">
	<channel>
		<title><![CDATA[<?php echo $settings['site_name']?>]]></title>
		<image>
		<title><![CDATA[<?php echo $settings['site_name']?>]]></title>
		<link></link>
		<url></url>
		</image>
		<description><![CDATA[<?php echo $settings['site_description']?>]]></description>
		<link><?php echo site_url('feed')?></link>
		<language>zh-cn</language>
		<?php foreach ($list as $v){?>
		<item>
			<title><![CDATA[<?php echo $v['title']?>]]></title>
			<link><?php echo site_url('topic/show/'.$v['topic_id']);?></link>
			<description><![CDATA[<?php echo mb_substr(strip_tags(@$v['content']), 0, 150, 'utf-8');?>]]></description>
			<pubDate><?php echo date(DATE_RSS,$v['updatetime']);?></pubDate>
			<guid><?php echo site_url('topic/show/'.$v['topic_id']);?></guid>
		</item>
		<?php } ?>
	</channel>
</rss> 
