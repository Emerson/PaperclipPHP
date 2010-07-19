<?php
class Asset extends PaperclipAppModel {
	
	var $name = 'Asset';
	var $options = array(
		'asset_folder'=>'assets', // webroot/assets/
		'valid_types' => array('image/gif','image/jpeg','image/pjpeg','image/png','application/postscript',
								'audio/aiff','audio/x-aiff','video/avi','application/rtf','application/x-rtf','	text/richtext',
								'text/plain','audio/wav','audio/x-wav','application/msword','application/xml','text/xml',
								'application/x-compressed','application/x-zip-compressed','application/zip','multipart/x-zip',
								'audio/mpeg3','audio/x-mpeg-3','video/mpeg','video/x-mpeg'),
		'image_types' => array('image/jpeg','image/pjpeg','image/png'),
		'styles' => array(
			'preview'	=> array(50,38,'square'),
			'thumbnail' => array(150,110,'square'),
			'medium'	=> array(300,300,'square')
		)	
	);				
	

	/*
	*	Returns the full asset folder path with trailing slash
	*/
	function path() {
		return WWW_ROOT . $this->options['asset_folder'] . DS;
	}
	
	
	/*
	*	Creates the style folders for a given id
	*/
	function create_folders($id) {
		$result['status'] = false;
		$folder = New Folder();
		foreach($this->options['styles'] as $name=>$value)	{			
			if($folder->create($this->path(). $id . DS . $name)) {
				$result['status'] = true;
			}else{
				$result['message'] = 'there was a problem creating the folder';
			}
		}
		return $result;
	}
	
	
	function save_asset($file) {		
		$result['status'] = false;
		$this->log('file info: ',7);
		$this->log($file,7);
		$info = $this->fileinfo($file);
		if(empty($info['content_type']) || !in_array($info['content_type'],$this->options['valid_types'])) {
			$this->log('not saving, invalid filetype...',7);
			$result['message'] = 'Invalid filetype';
			return $result;
		}
		if($this->save($info)) {
			$this->log('saved on line 52',7);
			$folder = new Folder();
			if($folder->create($this->path() . $this->id . DS . 'original')) {
				move_uploaded_file($file['tmp_name'],$this->path() . $this->id . DS . 'original' . DS . $info['filename']);
				if(in_array($info['content_type'],$this->options['image_types'])) {													
					if(	$this->create_folders($this->id) && $this->process_styles($this->id,$info)) {
						$result['message'] = 'files resized and saved';
						$result['status'] = true;
					}
				}				
			}else{
				$result['message'] = "There was a problem creating a folder, check your permissions";
				return $result;
			}			
		}
		return $result;				
	}
	
	
	/*
	*	Returns an array of uploaded file information
	*	
	*	Accepts a $file 
	*	Returns an array: array('filesize','filename','content_type','width','height')
	*/
	function fileinfo($file) {		
		App::import('Vendor', 'Paperclip.getid3/getid3');
		$getID3 = new getID3;		
		$file_class = new File($file['tmp_name']);	
		$fileinfo = $getID3->analyze($file['tmp_name']);	
		if(!empty($fileinfo['mime_type'])) {				
				$results['content_type'] = $fileinfo['mime_type'];	
		}
		if(!empty($fileinfo['jpg']['exif']['COMPUTED']['Height']) &&
		   !empty($fileinfo['jpg']['exif']['COMPUTED']['Width'])) {
			$results['width'] = $fileinfo['jpg']['exif']['COMPUTED']['Width'];
			$results['height'] = $fileinfo['jpg']['exif']['COMPUTED']['Height'];
		}
		if(!empty($fileinfo['png']['IHDR']['width']) &&
		   !empty($fileinfo['png']['IHDR']['height'])) {
			$results['width'] = $fileinfo['png']['IHDR']['width'];
			$results['height'] = $fileinfo['png']['IHDR']['height'];
		}
		if(!empty($fileinfo['gif']['header']['raw']['width']) &&
		   !empty($fileinfo['gif']['header']['raw']['height'])) {
			$results['width'] = $fileinfo['gif']['header']['raw']['width'];
			$results['height'] = $fileinfo['gif']['header']['raw']['height'];
		}
		$results['filename'] = $file_class->safe($file['name']);
		$results['filesize'] = $file_class->size();
		return $results;	
	}
	
	/*
	*	Takes care of all intial image processing
	*	
	*	Accepts an $id and file $info 
	*	Returns an array: array('filesize','filename','content_type','width','height')
	*/
	function process_styles($id,$info) {
		$result = false;
		$orig_ratio = $info['width'] / $info['height'];
		if(App::import('Vendor', 'Paperclip.PHPthumb', array('file'=>'PHPthumb'.DS.'ThumbLib.inc.php'))) {
			$this->log('looks good to me',7);
		}
		foreach($this->options['styles'] as $name=>$options) {			
			try {
			$this->log($this->path() . $this->id . DS . 'original' . DS . $info['filename'],7);
			$thumb = PhpThumbFactory::create($this->path() . $this->id . DS . 'original' . DS . $info['filename']);
			$this->log('PHPThumb has something',7);
			}
			catch(Exception $e) {
				$this->log('There was a problem creating the thumbnail',7);
				return false;
			}
			$this->log('starting resize',7);
			$thumb->adaptiveResize($options[0],$options[1]);
			$thumb->save($this->path() . $this->id . DS . $name . DS . $info['filename']);
			$result = true;
		}
		return $result;
	}
}
?>