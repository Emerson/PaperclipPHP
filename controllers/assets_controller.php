<?php
class AssetsController extends PaperclipAppController {
	
	var $name = 'Assets';
	var $helpers = array('Html','Form','Javascript','Ajax');
	var $components = array('Session','RequestHandler');
	
	function index() {
		$assets = $this->Asset->find('all');
		$this->set('assets',$assets);
	}
	
	function upload() {		
		$this->log($this->data,7);
		$this->log('something is happening',7);
		$this->log($_POST,7);
		$this->log($_FILES,7);
		if(!empty($this->data) && is_uploaded_file($this->data['Asset']['upload']['tmp_name']))	{		
			if($this->Asset->saveAsset($this->data['Asset']['upload'])) {
				$this->Session->setFlash('Your upload was complete');
			}else{
				$this->Session->setFlash('Your upload was not complete','default',array('class'=>'flashError'));
			}
		}				
		$this->redirect(array('action'=>'index'));		
	}
	
	function uploadify() {
		if(!empty($_FILES) && is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			$this->log('file data is there',7);
			$filedata = array(
				'name' => $_FILES['Filedata']['name'],
				'tmp_name' => $_FILES['Filedata']['tmp_name']
				);			
			if($this->Asset->saveAsset($filedata)) {
				echo "success";
				$this->log('saved',7);
			}else{
				echo "fail";
				$this->log('not saved',7);				
			}
		}
		$this->log('we are getting data!',7);
		$this->log($_FILES,7);
		exit();
	}

}
?>