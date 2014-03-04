<?php
class ProjectController extends Zend_Controller_Action
{
	public function init()
    {
    	if (!Zend_Auth::getInstance()->hasIdentity()) 
	   	{
	          $this->_redirect('/auth/logout/');
	    }
    }
	
	public function indexAction()
	{	
		$data = $this->getRequest()->getParams();
    	if(isset($data['msg']))
    	{
    		$this->view->msg = $data['msg'];
    	}
    	
    	if(isset($data['error']))
    	{
    		$this->view->error = $data['error'];
    	}
    	
		$taskproject = new Model_Project();
    	$this->view->projects = $taskproject->fetchAll();
	}
	
	public function createAction()
	{
		$request 	= $this->getRequest();
    	$form    	= new Form_Project();
    	$data		= $request->getPost();
    	if ($this->getRequest()->isPost()) 
    	{
    		if ($form->isValid($data)) 
    		{
    			$projectModel = new Model_Project();
    			if($projectModel->add($data))
    			{
    				$this->_forward('index','project','default',array('msg'=>'Project added'));
    			}else{
    				$this->_forward('index','project','default',array('error'=>'Project not added'));
    			}
    		}
    	}
    	$this->view->form = $form;
	}
	
	public function editAction()
	{
		
		$request 	= $this->getRequest();
    	$form    	= new Form_Project();
    	$data		= $request->getParams();
    	$projectModel = new Model_Project();
    	$projectModel->setId($data["id"]);
    	if ($this->getRequest()->isPost()) 
    	{
    		if ($form->isValid($data)) 
    		{
    			if($projectModel->update($data))
    			{
    				$this->_forward('index','project','default',array('msg'=>'Project saved'));
    			}else{
    				$this->_forward('index','project','default',array('error'=>'Project not saved'));
    			}
    		}
    	}
    	$project = $projectModel->fetch()->toArray();
    	$form->populate($project);
    	$this->view->form = $form;
	}
	
	public function deleteAction()
    {	
    	$id = $this->getRequest()->getParam('id');
    	$projectModel = new Model_Project();
    	$projectModel->deleteNotes($id);
    	if($projectModel->delete($id))
    	{
    		$this->_forward('index','project','default',array('msg'=>'Project deleted'));
    	}else{
    		$this->_forward('index','project','default',array('error'=>'Project not deleted'));
    	}
    }
    
    public function recordAction()
    {
   	 	$request 	= $this->getRequest();
    	$data		= $request->getParams();
    	$projectModel = new Model_Project();
    	
    if(isset($data['note']))
    	{	
    		//Zend_Debug::dump($data);die;
    		$taskModel = new Model_Activity();
    		$id_note = $taskModel->add($data);
    		if($id_note)
    		{
    			if($_FILES)
			    	{	
			    		
			    		$db = Zend_Registry::get('db');
			    		$adapter = new Zend_File_Transfer_Adapter_Http();
				    	// a call to $form->getValues() has been previously made
						foreach($adapter->getFileInfo() as $file => $info) {
						    if($adapter->isUploaded($file)) {
						    	$dbconf = $db->getConfig();
    		    	    		$FileRenamed = $info['name']; //md5($info['name']).'.'.substr($info['type'], 6);
    		    	    		$path =  PUBLIC_PATH.DIRECTORY_SEPARATOR.'notes'.DIRECTORY_SEPARATOR.$dbconf['dbname'].DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$id_note.DIRECTORY_SEPARATOR;
						        $location = '/notes/'.$dbconf['dbname'].'/files/'.$id_note.'/'.$FileRenamed;
    		    	    		$name   = $adapter->getFileName($file);
						        $fname  = $path . $FileRenamed;
						        /**
						         *  Let's inject the renaming filter
						         */
						        $adapter->addFilter(new Zend_Filter_File_Rename(array('target' => $fname, 'overwrite' => true)),
						        null, $file);
						    	/**
						         * And then we call receive manually
						         */
						    	if(file_exists($path))
			    		    	   	{	
			    		    	   		ini_set('max_execution_time', 0);
			    		    	   		$adapter->setDestination($path);
			    		    	   	}else{
			    		    	   		if(mkdir($path,0777,true))
			    		    	   		{
			    		    	   			ini_set('max_execution_time', 0);
			    		    	   			$adapter->setDestination($path);
			    		    	   		}
			    		    	   	}
						        $adapter->receive($file);
						        $modelFile = new Model_File();
							    if(substr($info['type'],0,5) == 'image')
						        {	
						       		$modelFile->add(array('id_note'=>$id_note, 'file'=>$location,'type'=>1));
						        }
						        elseif(substr($info['name'], -3) == 'pdf')
						        {
						        	$modelFile->add(array('id_note'=>$id_note, 'file'=>$location,'type'=>2));
						        }
						 		elseif(substr($info['name'], -3) == 'xls')
						        {
						        	$modelFile->add(array('id_note'=>$id_note, 'file'=>$location,'type'=>3));
						        }
						 		elseif(substr($info['name'], -3) == 'doc')
						        {
						        	$modelFile->add(array('id_note'=>$id_note, 'file'=>$location,'type'=>4));
						        }
								elseif(substr($info['name'], -3) == 'ppt')
						        {
						        	$modelFile->add(array('id_note'=>$id_note, 'file'=>$location,'type'=>5));
						        }
						        else
						        {
						        	$modelFile->add(array('id_note'=>$id_note, 'file'=>$location));
						        }
						    }
						}
			    	}
    		}
    	}
    	
    	if(isset($data["id"]))
    	{
	    	$projectModel->setId($data["id"]);
	    	$this->view->project = $projectModel->fetch()->toArray();
	    	$this->view->notes = $projectModel->getNotes();
	    	
	    	$groupModel = new Model_Group();
	    	$progileModel = new Model_Profile();
	    	
	    	$this->view->profiles = $progileModel->fetchAll();
	    	$this->view->groups = $groupModel->fetchAll();
    	}
    }
}