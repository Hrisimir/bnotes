<?php

class Application_Model_Register extends Application_Model_Abstract
{
	protected $_dbname;
	protected $_dbTableClass = 'Application_Model_DbTable_User'; 
	
	/**
	 * Sets database a name
	 * @param string $name  */
	public function setDatabase($name)
	{
		$this->_dbname = $name;
	}
	/**
	 * Checks if a company with that name already exists.
	 * @param string $name
	 * @return boolean  */
	
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
	
	/**
	 * Generates a company name.
	 * @return string Company name */
	
	public function getName()
	{
		$company = $this->query ( 'select id  from `company` order by id desc limit 1' );
		if (isset ( $company [0] )) {
			$company [0] ["id"] = $company [0] ["id"] + 1;
			Return 'company' . $company [0] ["id"];
		} else {
			Return 'company0';
		}
		
	}
	
	/**
	 * Checks if a username is already taken.
	 * @param string $name
	 * @return boolean  */
	
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
	
	/**
	 * Creates a database and makes a record in the database about the new company and user.
	 * Creates an ini file with database connection data
	 * @param array $data
	 * @return int $id - The ID of the company  */
	
	
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
	
	/**
	 * Creates login and profile for the user in the newly created database.
	 * @param array $data  - The array should containt firstname, lastname, email, username and password*/

	public function registerUser($data)
	{	
		$db2 = Zend_Registry::get('db2');
		$db2->insert('profile',array('id_role'=>1,'firstname'=>$data['firstname'],'lastname'=>$data['lastname'], 'email'=>$data['username']));
		$db2->insert('user',array('id_profile'=>1, 'username'=>$data['username'], 'password'=>$data['password']));
		$data = $this->unsetNonTableFields($data);
		$this->create($data);
	}
}

