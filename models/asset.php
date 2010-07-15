<?php
class Asset extends PaperclipAppModel {
	
	var $name = 'Asset';
	var $options = array(
		'attachment_folder'=>'assets',
		'permitted_types' => array('image/gif','image/jpeg','image/pjpeg','image/png'),
		'styles' => array(
			'thumbnail' => array(50,50,'square'),
			'medium'	=> array(300,300,'square')			
		)	
	);
	
	/*
	*	saveFile($upload)
	*	
	*	The workhorse of Paperclip that takes the pain out of saving files and
	*	keeping track of all the details.
	*
	*	returns true on success, false on failure
	*
	*/
	function saveAsset($upload) {
		$this->log($this->options['styles'],7);
		$this->log('step 1',7);		
		App::import('Vendor', 'Paperclip.getid3/getid3');
		$getID3 = new getID3;		
		$folder = new Folder();	
		if($folder->create('../webroot/assets')) {			
			$file = new File($upload['tmp_name']);		
			$fileinfo = $getID3->analyze($upload['tmp_name']);
			
			/*
			TODO - Refactor for nicer code. Try and process file data only once with
			getID3 to avoid uneeded system thrash
			*/
			$info = array(
				'filesize'		=>	$file->size(),
				'filename'		=>	$file->safe($upload['name']),
				'content_type'	=>	'',
				'width'			=>	'',
				'height'		=>	''
			);
			
					
			$this->data['filesize'] = $file->size();
			$this->data['filename'] = $file->safe($upload['name']);						
			if(!empty($fileinfo['mime_type'])) {				
				$info['content_type'] = $fileinfo['mime_type'];	
			}
			if(!empty($fileinfo['jpg']['exif']['COMPUTED']['Height']) &&
			   !empty($fileinfo['jpg']['exif']['COMPUTED']['Width'])) {
				$info['width'] = $fileinfo['jpg']['exif']['COMPUTED']['Width'];
				$info['height'] = $fileinfo['jpg']['exif']['COMPUTED']['Height'];
			}
			if(!empty($fileinfo['png']['IHDR']['width']) &&
			   !empty($fileinfo['png']['IHDR']['height'])) {
				$info['width'] = $fileinfo['png']['IHDR']['width'];
				$info['height'] = $fileinfo['png']['IHDR']['height'];
			}
			if(!empty($fileinfo['gif']['header']['raw']['width']) &&
			   !empty($fileinfo['gif']['header']['raw']['height'])) {
				$info['width'] = $fileinfo['gif']['header']['raw']['width'];
				$info['height'] = $fileinfo['gif']['header']['raw']['height'];
			}
										
			$this->data['content_type'] = $info['content_type'];
			$this->data['width'] = $info['width'];
			$this->data['height'] = $info['height'];					
					
			if(isset($fileinfo['mime_type']) && $this->validMimeType($fileinfo['mime_type'])) {
				// Save the record
				$this->log('step 5',7);
				$this->log($this->data,7);	
				if($this->save($this->data)) {
					if($folder->create('../webroot/assets/'.$this->id.'/original')) {
						$save_path = new Folder('assets');
						$path = $save_path->pwd() . '/' . $this->id .'/original/'.$file->safe($upload['name']);												
						if(move_uploaded_file($upload['tmp_name'],$path)) {
							$this->log('file has been moved',7);
							foreach($this->options['styles'] as $name=>$values) {
								if($folder->create('../webroot/assets/'.$this->id.'/'.$name)) {									
									$orig_path = 'assets/'.$this->id.'/original/'.$info['filename'];
									if(file_exists($orig_path)) {
										$new_image = @imagecreatetruecolor($values[0],$values[1]);
										$tmp_image = @imagecreatefromjpeg($orig_path);
										@imagecopyresampled($new_image,$tmp_image,0,0,0,0,$values[0],$values[1],$info['width'],$info['height']);									
										if(imagejpeg($new_image,'assets/'.$this->id.'/'.$name.'/'.$info['filename'])) {
											$this->log('copied',7);
										}																													
									}
								}
							}							
							return true;
						}else{
							$this->log('file has not been moved',7);
							return false;
						}
					}else{
					
						return false;
					}
					
				}else{
					return false;
				}				
				return true;
			}else{
				return false;
			}	
		}else{
			return false;
		}								
		return true;
	}
	
	
	function validMimeType($mimeType) {
		// TODO - add mimetype validations
		return true;
	}
	
	
}
?>