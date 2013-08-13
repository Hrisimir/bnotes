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
    	$this->view->custom = $customModel->fetchAll();
    	
    	$tagModel = new Model_Tag();
    	$this->view->tags = $tagModel->sortAndIndexArray($tagModel->fetchAll());
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
    	$this->view->allcontacts = $personModel->fetchContagtsByTag($data['tag']);
    
    }
    
	public function editpersonAction()
    {	
   		$request 	= $this->getRequest();
    	$form    	= new Form_Person();
    	$data		= $request->getParams();
    	if(isset($data["id"]))
    	{
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
	    	
	    	$form->populate($values);
	    	$form->company->setValue($values['id_company']);
	    	$this->view->phones = $groupModel->fetchPhones();
	    	$this->view->emails = $groupModel->fetchEmails();
	    	$this->view->addresses = $groupModel->fetchAdresses();
	    	$this->view->websites = $groupModel->fetchWebsites();
	    	$this->view->form = $form;
    	}
    	
    }
    
	public function editcompanyAction()
    {	
   		$request 	= $this->getRequest();
    	$form    	= new Form_Company();
    	$data		= $request->getParams();
    	if(isset($data["id"]))
    	{
	    	$groupModel = new Model_Company();
	    	$groupModel->setId($data["id"]);
	    	if($request ->isPost())
	    	{
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
    	
    	$this->view->profiles = $progileModel->fetchAll();
    	$this->view->groups = $groupModel->fetchAll();
    	$this->view->projects = $caseModel->fetchAll();
    	
    	$this->view->person = $personModel->fetchRecord();
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
    	
    	
    	
    	$this->view->profiles = $progileModel->fetchAll();
    	$this->view->groups = $groupModel->fetchAll();
    	$this->view->projects = $caseModel->fetchAll();
    	
    	$this->view->company = $personModel->fetchRecord();
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
			if(isset($data['submit']))
			{	
				$modelCompany = new Model_Company();
				$id_company = $modelCompany->add($data);
				if($data['type'] == 'person')
				{	
					$data['id_company'] = $id_company;
					$modelPerson = new Model_Person();
					$modelPerson->add($data);
				}
				
			}
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
}