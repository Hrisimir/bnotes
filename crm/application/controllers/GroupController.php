<?php
class GroupController extends Zend_Controller_Action
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
    	
    	$groupModel = new Model_Group();
    	$this->view->groups = $groupModel->fetchAll();
    }
    
	public function addAction()
    {
    	$request 	= $this->getRequest();
    	$form    	= new Form_Group();
    	$data		= $request->getPost();
    	if ($this->getRequest()->isPost()) 
    	{
    		//if ($form->isValid($data)) 
    		//{
    			$groupModel = new Model_Group();
    			if($groupModel->add($data))
    			{
    				$this->_forward('index','group','default',array('confirm'=>'Group added'));
    			}else{
    				$this->_forward('index','group','default',array('msg'=>'Group not added'));
    			}
    		//}
    	}
    	$this->view->form = $form;
    }
    
	public function editAction()
    {
    	$request 	= $this->getRequest();
    	$form    	= new Form_Group();
    	$data		= $request->getParams();
    	if(isset($data["id"]))
    	{
	    	$groupModel = new Model_Group();
	    	$groupModel->setId($data["id"]);
	    	if($request ->isPost())
	    	{
		    	if($groupModel->edit($data))
		    	{
		    		$this->_forward('index','group','default',array('confirm'=>'Group added'));
		    	}else{
		    		$this->_forward('index','group','default',array('msg'=>'Group not added'));
		    	}
	    	}
	    	
	    	$values = $groupModel->fetch();
	    	$form->populate($values);
	    	$form->people->setValue(array_keys($values["people"]));
	    	$this->view->form = $form;
    	}
    }
    
	public function deleteAction()
    {
    	$request 	= $this->getRequest();
    	$data		= $request->getParams();
    	if(isset($data["id"]))
    	{
	    	$groupModel = new Model_Group();
	    	$groupModel->setId($data["id"]);
	    	if($groupModel->delete())
	    	{
	    		$this->_forward('index','group','default',array('confirm'=>'Group deleted'));
	    	}else{
	    		$this->_forward('index','group','default',array('msg'=>'Group not deleted'));
	    	}
    	}
    	$this->_forward('index','group','default',array('msg'=>'Group not deleted'));
    }
}