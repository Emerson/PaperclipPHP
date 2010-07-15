<li>
	<input type="checkbox" value="<?php echo $asset['Asset']['id'];?>" />
	<?php echo $html->image('/assets/'.$asset['Asset']['id'].'/thumbnail/'.$asset['Asset']['filename'],
						array('url'=>'/assets/'.$asset['Asset']['id'].'/original/'.$asset['Asset']['filename']));?>
	
</li>	