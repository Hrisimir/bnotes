<?php

class Application_Model_Register extends Application_Model_Abstract
{
	protected $_dbname;
	protected $_dbTableClass = 'Application_Model_DbTable_User'; 
	
	
	public function setDatabase($name)
	{
		$this->_dbname = $name;
	}
	
	public function checkIfNotExists($name)
	{
		$table = new Application_Model_DbTable_Company();
		$select = $table->select()
			->setIntegrityCheck(false)
			->from('company','id')
			->where('`database` = ?', $name);
		$user = $table->fetchRow($select);
		if($user) return false;
		return true;
	}
	
	public function checkIfNotExistsUser($name)
	{
		$table = new Application_Model_DbTable_Company();
		$select = $table->select()
			->setIntegrityCheck(false)
			->from('user','id')
			->where('`username` = ?', $name);
		$user = $table->fetchRow($select);
		if($user) return false;
		return true;
	}
	
	public function createDatabase($data)
	{
		$table = new Application_Model_DbTable_Company();
		$db = $table->getDefaultAdapter();
		$db->insert('company',array('name'=>$data['companyname'],'database'=>$this->_dbname));
		$db->query('Call create_database(?)', $this->_dbname);
		
		$configIni = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini');
		$configArray = $configIni->toArray();
		$configArray['database']['params']['dbname'] = $this->_dbname;
		$config = new Zend_Config($configArray);
		$newconfig = new Zend_Config_Writer_Ini();
		$newconfig->write(APPLICATION_PATH . '/../../crm/application/configs/'.strtolower($this->_dbname.'.ini'), $config);
		
		$db2 = Zend_Db::factory($config->database);
    	$db2->setFetchMode(Zend_Db::FETCH_ASSOC);
    	$db2->query(new Zend_Db_Expr('SET NAMES utf8'));
    	Zend_Registry::set('db2', $db2);
		
		return $db->fetchOne($db->select()->from('company','id')->where('`database` = ? ',$this->_dbname));
	}
	public function registerUser($data)
	{	
		$db2 = Zend_Registry::get('db2');
		$db2->insert('profile',array('id_role'=>1,'firstname'=>$data['firstname'],'lastname'=>$data['lastname'], 'email'=>$data['username']));
		$db2->insert('user',array('id_profile'=>1, 'username'=>$data['username'], 'password'=>$data['password']));
		//$db2->insert('company',array('name'=>$data['companyname'], 'author'=>1));
		$data = $this->unsetNonTableFields($data);
		$this->create($data);
	}
}

