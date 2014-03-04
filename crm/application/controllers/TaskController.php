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
    	$tasks = $taskModel->fetchAll();

    	$tasktoday= null;
    	$taskOverdue= null;
    	$taskFuture= null;
    	
    	$taskCategory = array();
    	$taskCategoryArray = array();

    	$owners = array();
    	$ownersArray = array();
    	
    	foreach($tasks as $task)
    	{
    		switch ($task)
    		{
    			case date('Y-m-d', strtotime($task['duedate'])) == date('Y-m-d'):
    				$tasktoday[] = $task;
    				break;
    			case date('Y-m-d', strtotime($task['duedate'])) < date('Y-m-d'):
    				$taskOverdue[] = $task;
    				break;
    			case date('Y-m-d', strtotime($task['duedate'])) > date('Y-m-d'):
    				$taskFuture[] = $task;
    			default:
    				break;
    		}
    	
    		if(!in_array($task['id_category'], $taskCategory))
    		{
    			array_push($taskCategory, $task['id_category']);
    			$tmpArr = array('id' => $task['id_category'], 'name' => $task['category']);
    			array_push($taskCategoryArray, $tmpArr);
    		}
    		
    		if(!in_array($task['owner'], $owners))
    		{
    			array_push($owners, $task['owner']);
    			$tmpArr = array('id' => $task['owner'], 'name' => $task['ownername']);
    			array_push($ownersArray, $tmpArr);
    		}
    		//if(date('Y-m-d', strtotime($task['duedate'])) == date('Y-m-d'))
    		//$tasktoday[] = $task;
    	
    		//if(date('Y-m-d', strtotime($task['duedate'])) < date('Y-m-d'))
    		//$taskOverdue[] = $task;
    	}
    	
    	$user = $taskModel->getCurrentUser();
    	
    	if(!in_array($user->id, $owners))
    	{
    		array_push($ownersArray, array('id' => $user->id, 'name' => $user->firstname .' '. $user->lastname));
    	}

    	$this->view->owners = $ownersArray;
    	$this->view->categories = $taskCategoryArray;
    	
    	$this->view->tasks = $tasks;
    	$this->view->taskOverdue = $taskOverdue;
    	$this->view->taskFuture = $taskFuture;
    	$this->view->tasktoday = $tasktoday;
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
    
    public function finishAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$id = $this->getRequest()->getParam('id');
    	$taskModel = new Model_Task();
    	$this->view->num = $taskModel->finish($id);
    }
    
    public function filtertaskAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	
    	unset($data['module']);
    	unset($data['controller']);
    	unset($data['action']);
    	
    	$taskModel = new Model_Task();
    	$this->view->filtertasks = $taskModel->filtertask($data);
    }
}