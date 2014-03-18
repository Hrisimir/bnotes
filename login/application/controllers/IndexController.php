<?php
class IndexController extends Zend_Controller_Action {
	/*
	 * Sets login layout   
	 */
	public function init() {
		$this->_helper->layout ()->setLayout ( 'login' );
	}
	/*
	 * If post is send trys ot authenticate the user.
	 * If a match is found puts a cookie so the user can be connected to the right database. Redirects the user to the second app.
	 */
		public function indexAction() {
		$request = $this->getRequest ();
		$form = new Application_Form_Login ();
		$data = $request->getPost ();
		
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $data )) {
				$user = new Application_Model_User ( $data );
				if ($user->id) { //a match
					$cookie = $user->OAuthHash ();
					if (is_array ( $cookie )) {
						$domain = Zend_Registry::get ( 'config2' )->production->domain;
						setcookie ( 'crmAuth', $cookie ['u'], 0, '/', $domain );
						$this->_redirect ( 'http://' . $user->database . $domain . '/auth/process/' );
					} else {
						$this->view->msg = "MSG_WRONG_NO_CONNECTION_TO_MASTER_DATABASE";
					}
				} else {
					$this->view->msg = "Authentication failed.";
				}
			}
		}
		if ($data ['msg'] = $request->getParam ( 'msg' )) {
			$this->view->confirm = $data ['msg'];
		}
		$this->view->form = $form;
	}
	public function registerAction() {
		$request = $this->getRequest ();
		$form = new Application_Form_Register ();
		$data = $request->getPost ();
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $data )) {
				$data ['company'] = preg_replace ( "/[^A-Za-z]/", '', $data ['companyname'] );
				$modelRegister = new Application_Model_Register ();
				if ($data ['company'] == "") {
					$company = $modelRegister->query ( 'select id  from `company` order by id desc limit 1' );
					if (isset ( $company [0] )) {
						$company [0] ["id"] = $company [0] ["id"] + 1;
						$data ['company'] = 'company' . $company [0] ["id"];
					} else {
						$data ['company'] = 'company0';
					}
				}
				
				if ($modelRegister->checkIfNotExists ( $data ['company'] )) {
					if ($modelRegister->checkIfNotExistsUser ( $data ['username'] )) {
						
						$modelRegister->setDatabase ( $data ['company'] );
						try {
							$companyid = $modelRegister->createDatabase ( $data );
							if ($companyid) {
								$data ['id_company'] = $companyid;
								$modelRegister->registerUser ( $data );
								$this->_forward ( 'index', 'index', 'default', array (
										'msg' => 'successfulregistration' 
								) );
							}
						} catch ( Exception $e ) {
							$this->view->msg = $e->getMessage ();
						}
					} else {
						$this->view->msg = "E-mail " . $data ['username'] . " already exists!";
					}
				} else {
					$this->view->msg = "Company called " . $data ['company'] . " already exists!";
				}
			}
		}
		$this->view->form = $form;
	}
}

