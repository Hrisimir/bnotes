<?php

class IndexController extends Zend_Controller_Action
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
    	$noteModel = new Model_Activity();
    	$this->view->data = $noteModel->fetchAll();
    	$taskmodel = new Model_Task();
    	$this->view->tasks = $taskmodel->fetchAll();
    }
    public function autocompleterAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$value = $request->getParam('query','');
    	if($value === '') {die;}
    	$model = new Model_Person();
    	$this->view->result = $model->searchValue($value);
    }
    public function getnotesAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	switch($data['location'])
    	{	
    		case "projectrecord":
    			$taskModel = new Model_Project();
    			$taskModel->setId($data['id']);
	    		$this->view->data = $taskModel->getNotes($data['count']);
    			break;
    		case "personrecord":
    			$taskModel = new Model_Person();
    			$taskModel->setId($data['id']);
	    		$this->view->data = $taskModel->getNotes($data['count']);
    			break;
    		case "companyrecord":
    			$taskModel = new Model_Company();
    			$taskModel->setId($data['id']);
	    		$this->view->data = $taskModel->getNotes($data['count']);
    			break;
    		case "index":
    			$taskModel = new Model_Activity();
	    		$this->view->data = $taskModel->fetchAll($data['count']);
	    		
    			break;
    		default:
	    	$taskModel = new Model_Activity();
	    	$this->view->data = $taskModel->fetchAll($data['count']);
	    	break;
    	}
    }
    public function commentAction()
    {	
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	if(isset($data['id']) && $data['id'])
    	{
    		$modelActivity = new Model_Activity();
    		$modelActivity->setId($data['id']);
    		$idMain = $modelActivity->fetchMain();
    		$modelActivity->setId($idMain);
    		$commentForm = new Form_Comment();
    		
    		if($request->isPost())
    		{
    			$formData = $request->getPost();
	    		if($commentForm->isValid($formData))
	    		{	
	    			$formData['pid'] = $idMain;
	    			$modelNote = new Model_Activity();
	    			$id = $modelNote->add($formData);
		    		if($_FILES)
			    	{	
			    		$db = Zend_Registry::get('db');
			    		$adapter = new Zend_File_Transfer_Adapter_Http();
				    	// a call to $form->getValues() has been previously made
						foreach($adapter->getFileInfo() as $file => $info) {
						    if($adapter->isUploaded($file)) {
						    	$dbconf = $db->getConfig();
    		    	    		$FileRenamed = $info['name']; //md5($info['name']).'.'.substr($info['type'], 6);
    		    	    		$path =  PUBLIC_PATH.DIRECTORY_SEPARATOR.'notes'.DIRECTORY_SEPARATOR.$dbconf['dbname'].DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR;
						        $location = '/notes/'.$dbconf['dbname'].'/files/'.$id.'/'.$FileRenamed;
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
						       		$modelFile->add(array('id_note'=>$id, 'file'=>$location,'type'=>1));
						        }
						        elseif(substr($info['name'], -3) == 'pdf')
						        {
						        	$modelFile->add(array('id_note'=>$id, 'file'=>$location,'type'=>2));
						        }
						 		elseif(substr($info['name'], -3) == 'xls')
						        {
						        	$modelFile->add(array('id_note'=>$id, 'file'=>$location,'type'=>3));
						        }
						 		elseif(substr($info['name'], -3) == 'doc')
						        {
						        	$modelFile->add(array('id_note'=>$id, 'file'=>$location,'type'=>4));
						        }
								elseif(substr($info['name'], -3) == 'ppt')
						        {
						        	$modelFile->add(array('id_note'=>$id, 'file'=>$location,'type'=>5));
						        }
						        else
						        {
						        	$modelFile->add(array('id_note'=>$id, 'file'=>$location));
						        }
						    }
						}
			    	
			    	/*	foreach($_FILES as $k => $details)
			    		{	
			    		    switch ($details['type'])
			    		    {	
			    		    	case "image/png":
			    		    	case "image/jpeg":
			    		    	case "image/gif":
			    		    		$db = Zend_Registry::get('db');
			    		    	    $dbconf = $db->getConfig();
			    		    	    $FileRenamed = md5($details['name']).'.'.substr($details['type'], 6);
			    		    	    $filesObj = new Zend_File_Transfer_Adapter_Http();
			    		    	   	$path =  PUBLIC_PATH.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$dbconf['dbname'].DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR;
									Zend_Debug::dump($path.$FileRenamed);
			    		    	   	if(file_exists($path))
			    		    	   	{	
			    		    	   		ini_set('max_execution_time', 0);
			    		    	   		$filesObj->setDestination($path);
			    		    	   		$filesObj->addFilter('Rename', array('target'=>$path.$FileRenamed,'overwrite' => true));
			    		    	   	}else{
			    		    	   		if(mkdir($path,0777,true))
			    		    	   		{
			    		    	   			ini_set('max_execution_time', 0);
			    		    	   			$filesObj->setDestination($path);
			    		    	   			$filesObj->addFilter('Rename', array('target'=>$path.$FileRenamed,'overwrite' => true));
			    		    	   			
			    		    	   		}
			    		    	   	}
			    		    	   	$filesObj->receive();
			    		    		break;
			    		    	  
			    		    	default:
			    		    	    break;
			    		    }
		    			}*/
	    			}
    			}
    		}
    		$activity = $modelActivity->fetch();
    		if($activity["id_person"])
    		{	
    			$personModel = new Model_Person();
    			$personModel->setId($activity["id_person"]);
    			$activity['person'] = $personModel->fetch();
    		}
    		if($activity["id_company"])
    		{	
    			$companyModel =  new Model_Company();
    			$companyModel->setId($activity["id_company"]);
    			$activity['company'] = $companyModel->fetch();
    		}
    		$caseModel = new Model_Project();
    		if($activity["id_case"])
    		{	
    			$caseModel->setId($activity["id_case"]);
    			$activity['case'] = $caseModel->fetch()->toArray();
    		}
    		$this->view->projects = $caseModel->fetchAll();
    		
    		$activity['comments'] = $modelActivity->fetchComments();
    		//Zend_Debug::dump($activity);
    		$this->view->activity = $activity;
    		
    		unset($activity['note']);
    		$commentForm->populate($activity);
    		$this->view->cform = $commentForm;
    	}else{
    		$this->_redirect('/');
    	}
    }
    
    public function editnoteAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	if(isset($data['id']))
    	{
	    	$taskModel = new Model_Activity();
		    $data['result'] = $taskModel->updateNote($data);
    	}else{
    		$data['result'] = 0;
    	}
    	$this->view->data = $data;
    }
    
    public function deletenoteAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	if(isset($data['id']))
    	{
    		$taskModel = new Model_Activity();
    		$this->view->data  = $taskModel->deleteNote($data['id']);
    	}
    }
    
    
    
    public function editfancynoteAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	$taskModel = new Model_Activity();
    	$id_note = $data['id'];
    	$taskModel->setId($id_note);
    	$this->view->note = $taskModel->fetch();
    	if($request->isPost())
    	{
    		
	    	$update = $taskModel->edit($data);
    		if($_FILES)
			{	
			   	$db = Zend_Registry::get('db');
			   	$adapter = new Zend_File_Transfer_Adapter_Http();
				foreach($adapter->getFileInfo() as $file => $info) 
				{
					if($adapter->isUploaded($file)) 
					{
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
	    	$this->_redirect('/');
    	}
    	
    	$caseModel = new Model_Project();
    	$groupModel = new Model_Group();
    	$profileModel = new Model_Profile();
    	
    	$this->view->profiles = $profileModel->fetchAll();
    	$this->view->groups = $groupModel->fetchAll();
    	$this->view->projects = $caseModel->fetchAll();
    }
}

