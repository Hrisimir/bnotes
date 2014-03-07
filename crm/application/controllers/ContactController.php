<?php
class ContactController extends Zend_Controller_Action
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
    	
    	
    	$personModel = new Model_Person();
    	
    	$this->view->allcontacts = $personModel->fetchAllConatacts();
    	
    	$customModel = new Model_Customtag();
    	
    	$custom = $customModel->fetchAll();
    	$requiredFields = $customModel->query('select name, label from required_filters');

    	foreach ($requiredFields as $field)
    	{
    		array_push($custom, array('name' => $field['name'], 'label' => $field['label']));
    	}
    	 
    	$this->view->custom = $custom;
    	
    	$tagModel = new Model_Tag();
    	$this->view->tags = $tagModel->sortAndIndexArray($tagModel->fetchAll());

    	$this->view->contacttags = $tagModel->fetchAllTags();
    	
    	$modelGroup = new Model_Group();
    	$this->view->groups = $modelGroup->fetchAll();
    }

    public function addtagAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	$tagModel = new Model_Tag();
    	$tagModel->add($data);
    	$this->view->data = $tagModel->fetchTags($data['id_ref'], $data['type']);
    }
    
    public function addtagallAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	
    	$tags = array();
    	$tags['person'] =array();
    	$tags['company'] =array();
    	
    	$tagModel = new Model_Tag();
    	
    	if(isset($data["tag"]))
    	{
    		if(isset($data["contact"]))
    		{
    			$newdata = array();
    			
    			foreach ($data["contact"] as $key => $value)
    			{
    				$newdata['tag'] = $data["tag"];
    				$newdata['type'] = $value["type"];
    				$newdata['id_ref'] = $value["id"];
    				
    				$tagModel->add($newdata);
    				
    				if($value["type"] == 1)
    				{
    					array_push($tags['person'], $tagModel->fetchTags($value["id"], $value["type"]));
    				} else {
    					array_push($tags['company'], $tagModel->fetchTags($value["id"], $value["type"]));
    				}
    			}
    			
    			
    		}
    	}
    	
    	$this->view->data = $tags;
    }
    
    public function addtagallcontactsAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    
    	//Zend_Debug::dump($data);die;
    	 
    	if(isset($data["tag"]))
    	{
    		$tagModel = new Model_Tag();
    		$tagModel->addTagToAll($data);
    	}
    	 
    	$this->view->data = 1;
    }
    
    public function edittagAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	$tagModel = new Model_Tag();
   		$this->view->data =	$tagModel->edit($data);
    }
    
 	public function removetagAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	$tagModel = new Model_Tag();
    	$tagModel->removetag($data);
    	$this->view->data = $tagModel->fetchTags($data['id_ref'], $data['type']);
    }
    
    public function customfieldsAction()
    {	
		$customModel = new Model_Customtag();
		$this->view->tags = $customModel->fetchAll();
    }
    
    public function addcustomfieldAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	$modelCustom = new Model_Customtag();
    	$this->view->data = $modelCustom->add($data);
    }
    
 	public function removecustomfieldAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	$modelCustom = new Model_Customtag();
    	$this->view->data = $modelCustom->remove($data);
    
    }
    
	public function updatecustomfieldAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	$modelCustom = new Model_Customtag();
    	$this->view->data = $modelCustom->rename($data);
    
    }
    
    public function deletetagAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	$tagModel = new Model_Tag();
    	$this->view->data = $tagModel->deletetag($data['id']);
    }
    
    public function sortAction()
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
    	$personModel = new Model_Person();
    	
    	if($this->getRequest()->isXmlHttpRequest())
    	{
    		$this->_helper->layout()->disableLayout();
    		$this->view->allcontacts = $personModel->fetchContagtsByTag($data['tag'], $data['count'], 6);
    	}else{
    		$this->view->allcontacts = $personModel->fetchContagtsByTag($data['tag']);
    		
    		$tagModel = new Model_Tag();
    		$tagModel->setId($data['tag']);
    		$this->view->tag = $tagModel->fetch();
    		$this->view->firstload = 1;
    	}
    }
    public function mergepersonAction()
    {
    	$request 	= $this->getRequest();
    	$data		= $request->getParams();
    	
    	if(isset($data['id']))
    	{	
    		$personModel = new Model_Person();
    		$personModel->setId($data["id"]);
    		$this->view->person = $personModel->fetch();
    		$this->view->people = $personModel->fetchAll(0);
    		$this->view->id = $data["id"];
    		if(isset($data['mergeid']) && $data['mergeid']){
    			$personModel->merge($data["id"],  $data['mergeid']);
    		}
    	}
    }
    
    public function mergecompanyAction()
    {
    	$request 	= $this->getRequest();
    	$data		= $request->getParams();
    	 
    	if(isset($data['id']))
    	{
    		$companyModel = new Model_Company();
    		$companyModel->setId($data["id"]);
    		$this->view->company = $companyModel->fetch();
    		$this->view->companies = $companyModel->fetchAll(0);
    		$this->view->id = $data["id"];
    		if(isset($data['mergeid']) && $data['mergeid']){
    			$companyModel->merge($data["id"],  $data['mergeid']);
    		}
    	}
    }
    
    
	public function editpersonAction()
    {	
   		$request 	= $this->getRequest();
    	$form    	= new Form_Person();
    	$data		= $request->getParams();
    	
    	if(isset($data["id"]))
    	{	
    		$this->view->id = $data["id"];
	    	$groupModel = new Model_Person();
	    	$groupModel->setId($data["id"]);
	    	if($request ->isPost())
	    	{	
	    		//Zend_Debug::dump($data);die;
		    	if($groupModel->edit($data))
		    	{
		    		$this->_forward('index','contact','default',array('msg'=>'Contact saved'));
		    	}else{
		    		$this->_forward('index','contact','default',array('error'=>'Contact not saved'));
		    	}
	    	}
	    	$values = $groupModel->fetch();
	    	
	    	$modelCustomFileds = new Model_Customtag();
	    	$fields = $modelCustomFileds->fetchAll();
	    	$this->view->fields = $fields;
	    	if($fields)
	    	{	
	    		foreach($fields as $field)
	    		{
	    			$form->addElement('text',$field['name'],array('label'=>$field['label']));
	    		}
	    	}
	    	
	    	$newGroupModel = new Model_Group();
	    	
	    	$this->view->values = $values;
	    	
	    	$form->populate($values);
	    	$form->company->setValue($values['id_company']);
	    	$this->view->phones = $groupModel->fetchPhones();
	    	$this->view->emails = $groupModel->fetchEmails();
	    	$this->view->addresses = $groupModel->fetchAdresses();
	    	$this->view->websites = $groupModel->fetchWebsites();
	    	$this->view->publ = 1;
	    	$this->view->form = $form;
	    	$this->view->groups = $newGroupModel->fetchAll();
    	}
    	
    }
    
	public function editcompanyAction()
    {	
   		$request 	= $this->getRequest();
    	$form    	= new Form_Company();
    	$data		= $request->getParams();
    	if(isset($data["id"]))
    	{	
    		$this->view->id = $data["id"];
	    	$groupModel = new Model_Company();
	    	$groupModel->setId($data["id"]);
	    	if($request ->isPost())
	    	{		
	    		if($_FILES)
	    		{
	    			$db = Zend_Registry::get('db');
	    			$adapter = new Zend_File_Transfer_Adapter_Http();
	    			// a call to $form->getValues() has been previously made
	    			foreach($adapter->getFileInfo() as $file => $info) {
	    				if($adapter->isUploaded($file)) {
	    					$dbconf = $db->getConfig();
	    					$FileRenamed = $data["id"].'.'.substr($info['type'], 6);
	    					
	    					$path =  PUBLIC_PATH.DIRECTORY_SEPARATOR.'avatar'.DIRECTORY_SEPARATOR.$dbconf['dbname'].DIRECTORY_SEPARATOR.$data["id"].DIRECTORY_SEPARATOR;
	    					$location = '/avatar/'.$dbconf['dbname'].'/'.$data["id"].'/'.$FileRenamed;
	    					$name   = $adapter->getFileName($file);
	    					$fname  = $path . $FileRenamed;
	    					
	    					$data['avatar'] = $location;
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
	    					 
	    				}
	    			}
	    		}
	    		
		    	if($groupModel->edit($data))
		    	{	
		    		$this->_forward('index','contact','default',array('msg'=>'Contact saved'));
		    	}else{
		    		$this->_forward('index','contact','default',array('error'=>'Contact not saved'));
		    	}
	    	}
	    	$values = $groupModel->fetch();
	    	
	    	$modelCustomFileds = new Model_Customtag();
	    	$fields = $modelCustomFileds->fetchAll();
	    	$this->view->fields = $fields;
	    	if($fields)
	    	{
	    		foreach($fields as $field)
	    		{
	    			$form->addElement('text',$field['name'],array('label'=>$field['label']));
	    		}
	    	}
	    	
	    	$newGroupModel = new Model_Group();
	    	$this->view->groups = $newGroupModel->fetchAll();
	    	
	    	$this->view->values = $values;
	    	
	    	$form->populate($values);
	    	$this->view->phones = $groupModel->fetchPhones();
	    	$this->view->emails = $groupModel->fetchEmails();
	    	$this->view->addresses = $groupModel->fetchAdresses();
	    	$this->view->websites = $groupModel->fetchWebsites();
	    	$this->view->form = $form;
    	}
    	
    }
    
    public function deletepersonAction()
    {
    	$id = $this->getRequest()->getParam('id');
    	if($id)
    	{
	    	$personModel = new Model_Person();
	    	$personModel->setId($id);
	    	try
	    	{	 
	    		$personModel->deleteNotes();
	    		$personModel->delete();
	    		$this->_forward("index","contact","default", array('msg'=>'Contact deleted'));
	    	}
	    	catch (Exception $e)
	    	{	
	    		$this->_forward("index","contact","default", array('error'=>'Contact can not be deleted. There are associated.'));
	    	}
    	}
    }
    
	public function deletecompanyAction()
    {
    	$id = $this->getRequest()->getParam('id');
    	if($id)
    	{
	    	$personModel = new Model_Company();
	    	$personModel->setId($id);
	    	try
	    	{	
	    		$personModel->deleteNotes();
	    		$personModel->delete();
	    		$this->_forward("index","contact","default", array('msg'=>'Contact deleted'));
	    	}
	    	catch (Exception $e)
	    	{	
	    		$this->_forward("index","contact","default", array('error'=>'Contact can not be deleted. There are people associated.'));
	    	}
    	}
    }
    
    public function addcompanyAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	
    	if(isset($data['company']) && $data['company'])
    	{
    		$companyModel = new Model_Company();
    		try{
    			$data['id'] = $companyModel->add($data);
    			$this->view->data = $data;
    		}catch (Exception $e)
    		{	
    			$data['error'] = $e->getMessage();
    			$this->view->data = 0;
    		}
    	}else{
    			$this->view->data = 0;
    	}
    	
    }
    
    public function addpersonAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	
    	if(isset($data['firstname']) && $data['firstname'] && isset($data['lastname']) && $data['lastname'] && isset($data['id_company']) && $data['id_company'])
    	{
    		$personModel = new Model_Person();
    		try{
    			$data['id'] = $personModel->create($data);
    			$this->view->data = $data;
    		}catch (Exception $e)
    		{	
    			$data['error'] = $e->getMessage();
    			$this->view->data = 0;
    		}
    	}else{
    			$this->view->data = 0;
    	}
    }
    
    public function personrecordAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getParam('id');
    	$data = $request->getParams();
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
    	
    	$personModel = new Model_Person();
    	$personModel->setId($id);
    	
    	$caseModel = new Model_Project();
    	$groupModel = new Model_Group();
    	$progileModel = new Model_Profile();
    	
    	$tagModel = new Model_Tag();
    	$this->view->tags = $tagModel->fetchTags($id, 1);
    	
    	$this->view->profiless = $progileModel->fetchAll();
    	$this->view->groups = $groupModel->fetchAll();
    	$this->view->projects = $caseModel->fetchAll();
    	
    	$this->view->person = $personModel->fetchRecord();
    	$addtionalModel = new Model_Customtag();
    	$this->view->additional = $addtionalModel->fetchAll();
    	$this->view->phones = $personModel->fetchPhones();
    	$this->view->emails = $personModel->fetchEmails();
    	$this->view->addresses = $personModel->fetchAdresses();
    	$this->view->websites = $personModel->fetchWebsites();
    	$this->view->notes = $personModel->getNotes();
    }
    public function companyrecordAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getParam('id');
   		$data = $request->getParams();
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
    	$tagModel = new Model_Tag();
    	$this->view->tags = $tagModel->fetchTags($id, 2);
    	
    	$personModel = new Model_Company();
    	$personModel->setId($id);
    	
    	$caseModel = new Model_Project();
    	$groupModel = new Model_Group();
    	$progileModel = new Model_Profile();
    	
    	$this->view->countPpl = $personModel->getNumberOfPeople();
    	$this->view->profiless = $progileModel->fetchAll();
    	$this->view->groups = $groupModel->fetchAll();
    	$this->view->projects = $caseModel->fetchAll();
    	
    	$this->view->company = $personModel->fetchRecord();
    	$addtionalModel = new Model_Customtag();
    	$this->view->additional = $addtionalModel->fetchAll();
    	$this->view->phones = $personModel->fetchPhones();
    	$this->view->emails = $personModel->fetchEmails();
    	$this->view->addresses = $personModel->fetchAdresses();
    	$this->view->websites = $personModel->fetchWebsites();
    	$this->view->notes = $personModel->getNotes();
    }
    
    public function companyrecordpAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$id = $request->getParam('id');
    	
   		/*$data = $request->getParams();
    	$personModel = new Model_Company();
    	$personModel->setId($id);
    	$tagModel = new Model_Tag();
    	$this->view->tags = $tagModel->fetchTags($id, 2);
    	$this->view->company = $personModel->fetchRecord();
    	$this->view->phones = $personModel->fetchPhones();
    	$this->view->emails = $personModel->fetchEmails();
    	$this->view->addresses = $personModel->fetchAdresses();
    	$this->view->websites = $personModel->fetchWebsites();*/
    	
    	$modelperon = new Model_Person();
    	$this->view->allcontacts = $modelperon->fetchByCompany($id);
    }
    
 	public function getcontactsAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	$personModel = new Model_Person();
    	if(isset($data['filters']))
    	{
    		$this->view->data = $personModel->fetchAllConatacts($data['count'],6,$data['filters']);
    	}else{
    		$this->view->data = $personModel->fetchAllConatacts($data['count']);
    	}
    }
    
    public function importAction()
    {
    	$form = new Form_Import();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	if($form->isValid($data))
    	{	
    		if($_FILES)
			{	
	    		$db = Zend_Registry::get('db');
	    		$adapter = new Zend_File_Transfer_Adapter_Http();
		    	// a call to $form->getValues() has been previously made
				foreach($adapter->getFileInfo() as $file => $info) 
				{
					if($adapter->isUploaded($file)) 
					{
						$dbconf = $db->getConfig();
						$modelImport = new Model_Import();
						$idUser = $modelImport->getCurrentUser()->id;
    		    	    $FileRenamed = 'import.xls';
    		    	    $path =  PUBLIC_PATH.DIRECTORY_SEPARATOR.'import'.DIRECTORY_SEPARATOR.$dbconf['dbname'].DIRECTORY_SEPARATOR.$idUser.DIRECTORY_SEPARATOR;
    		    	    $name   = $adapter->getFileName($file);
						$fname  = $path . $FileRenamed;
				        $adapter->addFilter(new Zend_Filter_File_Rename(array('target' => $fname, 'overwrite' => true)),
				        null, $file);
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
						$this->_redirect('/contact/importoptions');
					}
				}
			}
    		
    		
    	}
    	$this->view->form = $form;
    }
    
    public function importoptionsAction()
    {	
    	//$this->_helper->layout()->disableLayout();
    	$db = Zend_Registry::get('db');
    	$dbconf = $db->getConfig();
		$modelImport = new Model_Import();
		$idUser = $modelImport->getCurrentUser()->id;
    	$fname = $path =  PUBLIC_PATH.DIRECTORY_SEPARATOR.'import'.DIRECTORY_SEPARATOR.$dbconf['dbname'].DIRECTORY_SEPARATOR.$idUser.DIRECTORY_SEPARATOR.'import.xls';
    	$data = $this->getRequest()->getParams();
    	
   		if(!isset($data['currentrow']))
		{
			$currentRow = 1;
		}else{
			$currentRow = $data['currentrow'];
		}
    	
    	ini_set('memory_limit', '-1');
    	
    	$inputFileType = 'Excel5';
		//    $inputFileType = 'Excel2007';
		//    $inputFileType = 'Excel2003XML';
		//    $inputFileType = 'OOCalc';
		//    $inputFileType = 'SYLK';
		//    $inputFileType = 'Gnumeric';
		//    $inputFileType = 'CSV';
		
		/**  Create a new Reader of the type defined in $inputFileType  **/
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objReader->setReadDataOnly(true);
		$filter = new PHPExcel_Filterarow();
		$filter->setRows($currentRow);
		$objReader->setReadFilter($filter);
		
		$objPHPExcel = $objReader->load($fname);
		$objWorksheet = $objPHPExcel->getActiveSheet();
		
		$lastColumm = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
		$this->view->currentrow = $currentRow;
		
		$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
		$array_data = array();
		foreach($rowIterator as $row){
		    $cellIterator = $row->getCellIterator();
		    $cellIterator->setIterateOnlyExistingCells(true); // Loop all cells, even if it is not set
		   // if(1 == $row->getRowIndex ()) continue;//skip first row
		    $rowIndex = $row->getRowIndex ();
		     
		    foreach ($cellIterator as $j=>$cell) 
		    {
		    	$array_data[$rowIndex][$j] = $cell->getValue();
		    }
		}
		if($array_data)
		{
			$this->view->data = $array_data;
		}
		//$this->view->data = $list;
		//$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		//Zend_Debug::dump($sheetData);die;
    }
    
    public function importsaveAction()
    {
    	$data = $this->getRequest()->getParams();
    	$this->view->data = $data;
    }
    
    public function importdataAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	
    	IF(isset($data['field_type']))
    	{
	    	ini_set('memory_limit', '-1');
	    	
	    	$db = Zend_Registry::get('db');
	    	$dbconf = $db->getConfig();
	    	
	    	$modelImport = new Model_Import();
	    	$modelCompany = new Model_Company();
	    	$modelPerson = new Model_Person();
	    	
	    	$idUser = $modelImport->getCurrentUser()->id;
	    	$fname = $path =  PUBLIC_PATH.DIRECTORY_SEPARATOR.'import'.DIRECTORY_SEPARATOR.$dbconf['dbname'].DIRECTORY_SEPARATOR.$idUser.DIRECTORY_SEPARATOR.'import.xls';
	    	
	    	$modelCompany = new Model_Company();
	    	$modelPerson = new Model_Person();
	    	
	    	$inputFileType = 'Excel5';
	    	//    $inputFileType = 'Excel2007';
	    	//    $inputFileType = 'Excel2003XML';
	    	//    $inputFileType = 'OOCalc';
	    	//    $inputFileType = 'SYLK';
	    	//    $inputFileType = 'Gnumeric';
	    	//    $inputFileType = 'CSV';
	    	
	    	/**  Create a new Reader of the type defined in $inputFileType  **/
	    	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	    	/**  Load $inputFileName to a PHPExcel Object  **/
	    	$objReader->setReadDataOnly(true);
	    //	$filter = new PHPExcel_Filterarow();
	    //	$filter->setRows($currentRow);
	    //	$objReader->setReadFilter($filter);
	    	
	    	 
	    	//$objPHPExcel = $objReader->load($fname);
	    	//$objWorksheet = $objPHPExcel->getActiveSheet();
	    	
	    	//$lastColumm = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
	    	//$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
	    	$array_data = array();
			
			$objPHPExcel = PHPExcel_IOFactory::load($fname);
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) 
			{
			    $worksheetTitle     = $worksheet->getTitle();
			    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
			    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
			    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			    $nrColumns = ord($highestColumn) - 64;
			    
			    
			    for ($row = 1; $row <= $highestRow; ++ $row) {
			    	
				        for ($col = 0; $col < $highestColumnIndex; ++ $col) {
				            $cell = $worksheet->getCellByColumnAndRow($col, $row);
				            $val = $cell->getValue();
				            $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
				            
				            if($data['field_type'][$col])
				            $array_data[$row][$data['field_type'][$col]] = $val;
				        }
				        
				       if(isset($array_data[$row]['type']) && $array_data[$row]['type'] == 'Person' )
				       {
					       	if(isset($array_data[$row]['firstname']) && $array_data[$row]['firstname'])
					       	{
					       		if(isset($array_data[$row]['lastname']) && $array_data[$row]['lastname'])
					       		{	
					       			if(isset($array_data[$row]['company']) && $array_data[$row]['company'])
					       			{	
					       				try{
					       					$dataperson = $array_data[$row];
						       				$idCompany = $modelCompany->add($array_data[$row]);
						       				$dataperson['id_company'] = $idCompany;
						       				$modelPerson->add($dataperson);
					       				}
					       			catch (Exception $e)
					       			{	
					       				$error[$row]['error'] = $e->getMessage();
					       			}
					       				
					       			}else{
					       				$error[$row]['error'] = 'Company name is required in order to Import a Person';
					       			}
					       				
					       		}else{
					       			$error[$row]['error'] = 'Lastname is required in order to Import a Person';
					       		}
					       	}else{
					       		$error[$row]['error'] = 'Firstname is required in order to Import a Person';
					       	}		
				       }
				       elseif(isset($array_data['type']) && $array_data['type'] == 'Company')
				       {	
				       		if(isset($array_data[$row]['company']) && $array_data[$row]['company'])
					       	{	
					       		try{
					       			$modelCompany->add($array_data[$row]);
					       		}
					       		catch (Exception $e)
					       		{	
					       			$error[$row]['error'] = $e->getMessage();
					       		}
					       	}else{
					       		$error[$row]['error'] = 'Company name is required in order to Import a Company';
					       	}
				       }
			    }
	    
			}
			
	    }
	    if($error)
	    {
	    	$this->view->data = $error;
	    }
    }
    /*
    public function importnotesAction()
    {	
    	
    	set_time_limit(0);
    	ini_set('memory_limit', '-1');
    	$files = scandir('C:\Users\Richard\Downloads\highrise-export-8562-0412\contacts');
    //	Zend_Debug::dump($files);die;
    	$notesModel = new Model_DbTable_Activity();
    	$db = $notesModel->getAdapter();
    	foreach ($files as $file)
    	{	
    		
    		if($file != '.' && $file != '..')
    		{	
    			try{
    				foreach(Zend_Config_Yaml::decode(file_get_contents('C:\\Users\\Richard\\Downloads\\highrise-export-8562-0412\\contacts\\'.$file)) as $key=>$contactdata)
    				{
    					if($key != '0' && $key != 'ID' && $key != 'Name' && $key != 'Company' && $key != 'Contact' && $key != 'Background')
    					{
    						//Zend_Debug::dump($contactdata);die;
    						
    						if(isset($contactdata['0']) && is_array($contactdata['0']) && isset( $contactdata['0']['Author']) )
    						$newcontactdata['author'] = $contactdata['0']['Author'];
    						
    						if(isset($contactdata['2']) && is_array($contactdata['2']) && isset( $contactdata['2']['About']) )
    						$newcontactdata['about'] = 		 $contactdata['2']['About'];
    						
    						if(isset($contactdata['1']) && is_array($contactdata['1']) && isset( $contactdata['1']['Written']) )
    						$newcontactdata['when'] = date('Y:m:d H:i:s' , strtotime($contactdata['1']['Written']));
    						
    						if(isset($contactdata['3']) && is_array($contactdata['3']) && isset( $contactdata['3']['Body']) )
    						$newcontactdata['note'] = $contactdata['3']['Body'];
    						
    						$notes[] = $newcontactdata;
    						
    						
    						$db->insert('imported_notes', $newcontactdata);
    						//Zend_Debug::dump($notes);die;
    					}
    				}
    			}
    			catch(Exception $e)
    			{
    				//$errors[] = $e->getMessage();
    			}
    			
    		}
    	}
    	
    	Zend_Debug::dump($notes);
    	//Zend_Debug::dump($errors);
    	die;
    	
    	
    	
    }*/
    
    public function deletecontactsAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	if(isset($data['person']) && $data['person'])
    	{
    		$modelPerson = new Model_Person();
    		foreach ($data['person'] as $person)
    		{
    			$modelPerson->setId($person);
    			$modelPerson->deleteNotes();
    			$modelPerson->delete();
    		}
    	}
    	
    	if(isset($data['company']) && $data['company'])
    	{	
    		$modelCompany = new Model_Company();
    		foreach($data['company'] as $company)
    		{	
    			$modelCompany->setId($company);
    			$modelCompany->deleteNotes();
    			$modelCompany->delete();
    		}
    	}
    }
    
    public function getinfoAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	
    	if(isset($data['id_person']) && $data['id_person'])
    	{
    		$modelPerson = new Model_Person();
    		$modelPerson->setId($data['id_person']);
    		$this->view->data = $modelPerson->fetch();
    		$modelCompany = new Model_Company();
    		$modelCompany->setId($this->view->data['id_company']);
    		$this->view->data['company'] = $modelCompany->fetch();
    	}
    	elseif(isset($data['id_company']) && $data['id_company'])
    	{
    		$modelCompany = new Model_Company();
    		$modelCompany->setId($data['id_company']);
    		$this->view->data = $modelCompany->fetch();
    	}
    	
    }
    
    public function changepermissionAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	
    	$persModel = new Model_Person();
    	$compModel = new Model_Company();
    	
    	if(isset($data['access']))
    	{
    		$permission = explode('&', $data['access']);
    		 
    		$accessArr = explode('=', $permission['0']);
    		$groupArr = explode('=', $permission['1']);
    		 
    		$access = $accessArr['1'];
    		$group = $groupArr['1'];
    		
    		switch ($access)
    		{
    			case 0 :
    			case 1 :
    				$tmpdata['group'] = null;
    				break;
    			case 2 :
    				$tmpdata['group'] = $group;
    				break;
    		}
    		
    		$group = $tmpdata['group'];
    		
    		if(isset($data["contact"]))
    		{
    			$newdata = array();
    			 
    			foreach ($data["contact"] as $key => $value)
    			{
    				$newdata['id'] = $value["id"];
    				$newdata['access'] = $access;
    				$newdata['group'] = $group;
    		
    				if($value["type"] == 1)
    				{
    					$persModel->changePermission($newdata);
    					//array_push($tags['person'], $tagModel->fetchTags($value["id"], $value["type"]));
    				} else {
    					$compModel->changePermission($newdata);
    					//array_push($tags['company'], $tagModel->fetchTags($value["id"], $value["type"]));
    				}
    			}
    		}
    	}
    	
    	$this->view->data = 1;
    }
    
    public function changepermissioncontactsAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	 
    	$persModel = new Model_Person();
    	$compModel = new Model_Company();
    	 
    	if(isset($data['access']))
    	{
    		$permission = explode('&', $data['access']);
    		 
    		$accessArr = explode('=', $permission['0']);
    		$groupArr = explode('=', $permission['1']);
    		 
    		$access = $accessArr['1'];
    		$group = $groupArr['1'];
    	
    		switch ($access)
    		{
    			case 0 :
    			case 1 :
    				$tmpdata['group'] = null;
    				break;
    			case 2 :
    				$tmpdata['group'] = $group;
    				break;
    		}
    	
    		$group = $tmpdata['group'];
    	
    		$persModel->changePermissionAllContacts($access, $group);
    	}
    	 
    	$this->view->data = 1;
    }
    
    public function shownotesbytagAction()
    {
    	$tag = $this->getRequest()->getParam('tag');
    	$tagName = $this->getRequest()->getParam('tagname');
    	
    	if($tag && $tagName)
    	{
    		$modelActivity = new Model_Activity();
    		$this->view->result = $modelActivity->showNotesByTag($tag);
    		
    		$this->view->tag = $tagName;
    	}
    }
}