<li>
	<input type="checkbox" value="<?php echo $asset['Asset']['id'];?>" />
	<?php echo $html->image('/assets/'.$asset['Asset']['id'].'/preview/'.$asset['Asset']['filename'],
						array('url'=>'/assets/'.$asset['Asset']['id'].'/original/'.$asset['Asset']['filename']));?>
						
	<?php echo $html->link($asset['Asset']['filename'],'/assets/'.$asset['Asset']['id'].'/original/'.$asset['Asset']['filename']);?>
	
</li>	