<?php
class Asset extends PaperclipAppModel {
	
	var $name = 'Asset';
	var $options = array(
		'attachment_folder'=>'assets',
		'permitted_types' => array('image/gif','image/jpeg','image/pjpeg','image/png') 		
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
		$this->log('step 1',7);		
		App::import('Vendor', 'Paperclip.getid3/getid3');
		$getID3 = new getID3;		
		$folder = new Folder();	
		if($folder->create('../webroot/assets')) {
			$this->log('step 2',7);	
			$file = new File($upload['tmp_name']);
			$this->log('step 3',7);	
			$fileinfo = $getID3->analyze($upload['tmp_name']);
			$this->log('step 4',7);	
			$this->data['filesize'] = $file->size();
			$this->data['filename'] = $file->safe($upload['name']);
			if(!empty($fileinfo['mime_type'])) {				
				$this->data['content_type'] = $fileinfo['mime_type'];	
			}
			if(!empty($fileinfo['jpg']['exif']['COMPUTED']['Height']) &&
			   !empty($fileinfo['jpg']['exif']['COMPUTED']['Width'])) {
				$this->data['width'] = $fileinfo['jpg']['exif']['COMPUTED']['Width'];
				$this->data['height'] = $fileinfo['jpg']['exif']['COMPUTED']['Height'];
			}
			if(!empty($fileinfo['png']['IHDR']['width']) &&
			   !empty($fileinfo['png']['IHDR']['height'])) {
				$this->data['width'] = $fileinfo['png']['IHDR']['width'];
				$this->data['height'] = $fileinfo['png']['IHDR']['height'];
			}
			if(!empty($fileinfo['gif']['header']['raw']['width']) &&
			   !empty($fileinfo['gif']['header']['raw']['height'])) {
				$this->data['width'] = $fileinfo['gif']['header']['raw']['width'];
				$this->data['height'] = $fileinfo['gif']['header']['raw']['height'];
			}			
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