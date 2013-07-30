<?php
class ProfileController extends Zend_Controller_Action
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
    	
    	$profileModel = new Model_Profile();
    	$this->view->profiles = $profileModel->fetchAll();
    }
 	public function makeadminAction()
    {	
    	$this->_helper->layout()->disableLayout();
    	$data = $this->getRequest()->getParams();
    	$profileModel = new Model_Profile();
    	if($data['admin'] == 1)
    	{
    		$this->view->data = $profileModel->makeadmin($data['id']);
    	}else{
    		$this->view->data = $profileModel->removeadmin($data['id']);
    	}
    }
    
    public function editAction()
    {	
    	//$this->_helper->layout()->setLayout('layout2');
    	$request 	= $this->getRequest();
		$data		= $request->getPost();
		
		$form    	= new Form_Profile();
		$userModel = new Model_Profile();
        
		if ($request->isPost()) 
        {	
        
            if ($form->isValid($data))
            {	
            	//Zend_Debug::dump($data);die;
            	try{
               		$userModel->save($data);
               		$this->view->confirm = 'Profile saved sucessfully';
            	}
            	catch (Exception $e)
            	{
            		$this->view->msg = $e->getMessage();
            	}
            }
        }
        
        $user = $userModel->getCurrentUser()->toArray();
        $userModel->setId($user['id']);
        $user = $userModel->fetch()->toArray();
        $form->populate($user);
        $this->view->form = $form;
    }
	
	public function addAction()
    {
   		$request 	= $this->getRequest();
    	$form    	= new Form_Register();
    	$data		= $request->getPost();
    	if ($this->getRequest()->isPost()) 
    	{
    		if ($form->isValid($data)) 
    		{
    			$profileModel = new Model_Profile();
    			if($profileModel->add($data))
    			{
    				$this->_forward('index','profile','default',array('confirm'=>'Successful registration.'));
    			}else{
    				$this->_forward('index','profile','default',array('msg'=>'Registration failed.'));
    			}
    		}
    	}
    	$this->view->form = $form;
    }
    
	public function deleteAction()
    {
    	
    }
}

