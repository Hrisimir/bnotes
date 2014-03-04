<?php
class TaskController extends Zend_Controller_Action{
	
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
    	$taskModel = new Model_Task();
    	$this->view->tasks = $taskModel->fetchAll();
    }
    
	public function addAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	//$data['publ'] = 1;
    	
    	if(isset($data['name']) && isset($data['due']) && isset($data['cat']) && isset($data['publ']))
    	{	
    		$taskModel = new Model_Task();
    		$insert['id'] =  $taskModel->add($data);
    		$this->view->data = $insert;
    	}
    	
    }
    
    public function editAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	//$data['publ'] = 1;
    	//Zend_Debug::dump($data);die;
    	if(isset($data['id']) && isset($data['name']) && isset($data['due']) && isset($data['cat']) && isset($data['publ']))
    	{
    		$taskModel = new Model_Task();
    		$taskModel->setId($data['id']);
    		$this->view->data =  $taskModel->edit($data);
    	}
    }
    
 	public function deleteAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$id = $this->getRequest()->getParam('id');
    	$taskModel = new Model_Task();
    	$this->view->num = $taskModel->delete($id);
    }
    
    public function addcategoryAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	if(isset($data['cat']) && $data['cat'])
    	{
    		$cat = new Model_DbTable_Taskcategory();
    		try{
    			$data['id'] = $cat->insert(array('name'=>$data['cat']));
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
    
    public function gettaskAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$id = $this->getRequest()->getParam('id');
    	 
    	if($id)
    	{
    		$taskModel = new Model_Task();
    		$taskModel->setId($id);
    		$this->view->data = $taskModel->getTask();
    	}
    }
    
}