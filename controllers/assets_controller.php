<?php
class AssetsController extends PaperclipAppController {
	
	var $name = 'Assets';
	var $helpers = array('Html','Form','Javascript','Ajax');
	var $components = array('Session','RequestHandler');
	
	function index() {
		$this->paginate = array(			
			'limit'=>13
		);		
		$this->set('assets',$this->paginate());	
	}
	
	function upload() {		
		$this->log($this->data,7);
		$this->log('something is happening',7);
		$this->log($_POST,7);
		$this->log($_FILES,7);
		if(!empty($this->data) && is_uploaded_file($this->data['Asset']['upload']['tmp_name']))	{		
			if($this->Asset->save_asset($this->data['Asset']['upload'])) {
				$this->Session->setFlash('Your upload was complete');
			}else{
				$this->Session->setFlash('Your upload was not complete','default',array('class'=>'flashError'));
			}
		}				
		$this->redirect(array('action'=>'index'));		
	}
	
	function uploadify() {
		$result['status'] = 'failure';
		if(!empty($_FILES) && is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			$this->log('file data is there',7);
			$filedata = array(
				'name' => $_FILES['Filedata']['name'],
				'tmp_name' => $_FILES['Filedata']['tmp_name']
				);			
			if($this->Asset->save_asset($filedata)) {
				$result['status'] = 'success';
				$this->log('saved',7);
			}else{				
				$this->log('not saved',7);				
			}
		}	
		// $this->log($_FILES,7);
		$this->log($result,7);
		$this->set('result',$result);
		$this->render('../elements/json', 'ajax');
	}
	
	function getassets() {
		$result['status'] = 'failure';
		$options = array(
			'per_page'=>10,
			'page'=>1
			);
		$result['assets'] = $this->Asset->getFiles($options);
		if(!empty($result['assets'])) {
			$result['status'] = 'success';
		}
		$this->set('result',$result);
		$this->render('../elements/json', 'ajax');
	}
	
	function process() {		
		$this->Asset->create_folders(304);		
	}

}
?>