<?php

class Application_Model_User extends Application_Model_Abstract
{
	private $id;
	private $username;
	private $password;
	private $database;
	private $db_user;
	private $db_pass;
	private $db_host;
	private $last_login;
	private $last_attempt;
	private $number_attempt;
	private $password_recovery_hash;
	private $is_logged;
	private $company_code;
	
	/**
	 * @return the $company_code
	 */
	public function getCompany_code() {
		return $this->company_code;
	}

	/**
	 * @param field_type $company_code
	 */
	public function setCompany_code($company_code) {
		$this->company_code = $company_code;
	}

	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return the $username
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @return the $password
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @return the $database
	 */
	public function getDatabase() {
		return $this->database;
	}

	/**
	 * @return the $db_user
	 */
	public function getDb_user() {
		return $this->db_user;
	}

	/**
	 * @return the $db_pass
	 */
	public function getDb_pass() {
		return $this->db_pass;
	}

	/**
	 * @return the $db_host
	 */
	public function getDb_host() {
		return $this->db_host;
	}

	/**
	 * @return the $last_login
	 */
	public function getLast_login() {
		return $this->last_login;
	}

	/**
	 * @return the $last_attempt
	 */
	public function getLast_attempt() {
		return $this->last_attempt;
	}

	/**
	 * @return the $number_attempt
	 */
	public function getNumber_attempt() {
		return $this->number_attempt;
	}

	/**
	 * @return the $password_recovery_hash
	 */
	public function getPassword_recovery_hash() {
		return $this->password_recovery_hash;
	}

	/**
	 * @param field_type $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @param field_type $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @param field_type $database
	 */
	public function setDatabase($database) {
		$this->database = $database;
	}

	/**
	 * @param field_type $db_user
	 */
	public function setDb_user($db_user) {
		$this->db_user = $db_user;
	}

	/**
	 * @param field_type $db_pass
	 */
	public function setDb_pass($db_pass) {
		$this->db_pass = $db_pass;
	}

	/**
	 * @param field_type $db_host
	 */
	public function setDb_host($db_host) {
		$this->db_host = $db_host;
	}

	/**
	 * @param field_type $last_login
	 */
	public function setLast_login($last_login) {
		$this->last_login = $last_login;
	}

	/**
	 * @param field_type $last_attempt
	 */
	public function setLast_attempt($last_attempt) {
		$this->last_attempt = $last_attempt;
	}

	/**
	 * @param field_type $number_attempt
	 */
	public function setNumber_attempt($number_attempt) {
		$this->number_attempt = $number_attempt;
	}

	/**
	 * @param field_type $password_recovery_hash
	 */
	public function setPassword_recovery_hash($password_recovery_hash) {
		$this->password_recovery_hash = $password_recovery_hash;
	}

	public function __set($name, $value){
		$func = 'set'.ucfirst($name);
		if(method_exists($this, $func)){
			$this->$func($value);
		}
	}
	
	public function __get($name){
		return $this->$name;
	}
	
	public function __construct($data){
		foreach($data as $k=>$v){
			$this->__set($k, $v);
		}
		
		return $this->check();
	}
	
	private function check(){
		$username = $this->getUsername();
		$password = $this->getPassword();
		$table = new Application_Model_DbTable_User();
		$select = $table->select()
			->setIntegrityCheck(false)
			->from(array('u'=>'user'))
			->join(array('c'=>'company'), 'u.id_company = c.id','c.database')
			->where('u.username = ?', $username)
			->where('u.password = ?', $password);
		$user = $table->fetchRow($select);
		
		if($user){
			foreach($user->toArray() as $k=>$v){
				$this->__set($k, $v);
			}
			return $user->toArray();
		}
	}
	
	private function generateHash(){
		$hash = sha1(time().md5(mt_rand(0, 999999999)));
		return $hash;
	}
	
	private function forgotten_password(){
		$hash = $this->generateHash();
	}
	
	public function OAuthHash()
	{
		$hash =  $this->generateHash();
		$table = new Application_Model_DbTable_Session();
		$res = $table->insert(
					array(
						'id_user'	=> $this->getId(),
						'session_salt'		=> $hash
					)
		); 
		
		if($res){
			return array('u'=> $hash, 'c'=> $this->getCompany_code(), 'f'=>'OAuth');
		} else {
			return false;
		}
	}

}

