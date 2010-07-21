<li>
	<?php echo $form->input($asset['Asset']['id'],array('type'=>'checkbox','label'=>false,'div'=>false));?>	
	<?php echo $html->image('/assets/'.$asset['Asset']['id'].'/preview/'.$asset['Asset']['filename'],
						array('url'=>'/assets/'.$asset['Asset']['id'].'/original/'.$asset['Asset']['filename']));?>
						
	<?php echo $html->link($asset['Asset']['filename'],'/assets/'.$asset['Asset']['id'].'/original/'.$asset['Asset']['filename']);?>
	
</li>	