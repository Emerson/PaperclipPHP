<?php
//	Paperclip Includes (jQuery 1.4, Uploadify, and base CSS)
echo $html->css(array(
	'/paperclip/css/uploadify',
	'/paperclip/css/default',
	'/paperclip/css/paperclip'));
	
echo $javascript->link(array(
	'/paperclip/js/jquery-1.4.2.min.js',
	'/paperclip/js/swfobject.js',
	'/paperclip/js/jquery.uploadify.v2.1.0.min.js'));
?>

<script type="text/javascript">
$(document).ready(function() {
	
	$("#AssetUpload").uploadify({
	'uploader'       : '<?php echo $this->webroot;?>paperclip/flash/uploadify.swf',
	'script'         : '<?php echo $this->webroot;?>paperclip/assets/uploadify',
	'hideButton'	 : false,
	'cancelImg'      : '<?php echo $this->webroot;?>/paperclip/img/cancelIcon.png',	
	'buttonImg'		 : '<?php echo $this->webroot;?>/paperclip/img/fileButton.png',
	'multi'			 : false,
	'auto'           : true,
	'width'			 : '225',
	'height'		 : '31',
	'multi'			 : true,
	'wmode'			 : 'transparent',
	'scriptData'	 : {
		user_id: 'uploadify'
	},
	'onComplete'	: function(event,queueID,fileObj,response) {
		$(".uploadifyQueueItem").fadeOut(250, function() {
			console.log(event);
			console.log(queueID);
			console.log(fileObj);
			console.log(response);
		});
	}
	});
	
	$(".startUploads").show();
	$(".startUploads").click(function() {
		console.log('starting uploads');
		$("#assetUpload").uploadifyUpload();
		return false;
	});
	
	$(".addFiles").show();
	$(".traditionalSubmit").hide();
	
	$(".assetList ul  li:even").addClass('even');
	
});
</script>

<div id="paperclipWrapper">
	
	<div class="paperclipLeft">
		<div class="headerLeft">
		</div>
		<div class="assetList">
			<ul>
				<?php foreach($assets as $asset): ?>
					<?php echo $this->element('asset',array('asset'=>$asset)); ?>
				<?php endforeach; ?>	
			</ul>	
		</div>	
	</div>
	
	<div class="paperclipRight">
		<div class="headerRight">
			<a href="javascript:$('#AssetUpload').uploadifyUpload();" class="addFiles">Add Files and/or Images</a>			
		</div>
		<?php echo $form->create('Asset',array('type'=>'file','action'=>'upload'));?>

		<?php echo $form->file('Asset.upload');?>

		<?php echo $form->submit('Upload', array('class'=>'traditionalSubmit','div'=>false));?>

		<?php echo $form->end();?>
	</div>		
	
	

</div>