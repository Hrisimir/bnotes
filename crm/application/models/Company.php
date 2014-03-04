<?php
class Model_Company extends Model_Abstract {
	protected $_dbTableClass = 'Model_DbTable_Company';
	protected $_id;
	
	public function __construct($user_id = null) {
	}
	/**
	 * @return the $_id
	 */
	public function getId() {
		return $this->_id;
	}
	
	/**
	 * @param field_type $_id
	 */
	public function setId($_id) {
		$this->_id = $_id;
	}
	
	public function merge($id, $newid)
	{	
		$table = $this->getTable();
		$db = $table->getAdapter();
		$newdata = $table->fetchRow('id = '.$newid)->toArray();
		$olddata = $table->fetchRow('id = '.$id)->toArray();
		$bind['id_company'] = $id;
		
		//update task
		$where = $db->quoteInto('id_company = ?', $newid);
		$db->update('task', $bind, $where);
		
		//update case_person
		$where = $db->quoteInto('id_company = ?', $newid);
		$db->update('case_company', $bind, $where);
		
		//update note
		$where = $db->quoteInto('id_company = ?', $newid);
		$db->update('note', $bind, $where);
		
		//update person_address
		$where = $db->quoteInto('id_company = ?', $newid);
		$db->update('company_address', $bind, $where);
		
		//update person_email
		$where = $db->quoteInto('id_company = ?', $newid);
		$db->update('company_email', $bind, $where);
		
		//update person_meta
		$where = $db->quoteInto('id_company = ?', $newid);
		$db->update('company_meta', $bind, $where);
		
		//update person_phone
		$where = $db->quoteInto('id_company = ?', $newid);
		$db->update('company_phone', $bind, $where);
		
		//update person_website
		$where = $db->quoteInto('id_company = ?', $newid);
		$db->update('company_website', $bind, $where);
		
		
		//update tag_ref
		$bindtr['id_ref'] = $id;
		$where = $db->quoteInto('id_ref = ? and type = 2', $newid);
		$db->update('tag_ref', $bindtr, $where);
		
		//update person
		$where = $db->quoteInto('id_company = ?', $newid);
		$db->update('person', $bind, $where);
		
		//update company
		foreach ($newdata as $k=>$v)
		{
			if(!$v)$newdata[$k] = $olddata[$k];
		}
		
		unset($newdata['id']);
		$where = $db->quoteInto('id = ?', $id);
		$table->update($newdata, $where);
		$where = $db->quoteInto('id = ?', $newid);
		$table->delete($where);
		
	}

	public function fetchPhones()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_company = ?', $this->_id);
		$sql = $db->select()->from('company_phone')->where($where);
		return $db->fetchAll($sql);
	}
	
	public function getNumberOfPeople()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_company = ?', $this->_id);
		$select = $db->select()->from('person','id')->where($where);
		
		return $db->fetchAll($select);
	}
	
	public function fetchEmails()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_company = ?', $this->_id);
		$sql = $db->select()->from('company_email')->where($where);
		return $db->fetchAll($sql);
	}
	
	public function fetchAdresses()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_company = ?', $this->_id);
		$sql = $db->select()->from('company_address')->where($where);
		
		$addresses = $db->fetchAll($sql);
		
		if($addresses)
		{
			$coutries = $db->query('select id, name from nmcl_country')->fetchAll();
			$address_type = $db->query('select id, name from nmcl_addresstype')->fetchAll();
				
			foreach ($addresses as $key => $value)
			{
				$addresses[$key]['country'] = $coutries;
				$addresses[$key]['address_type'] = $address_type;
			}
		}
		
		return $addresses;
	}
	
	public function fetchWebsites()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_company = ?', $this->_id);
		$sql = $db->select()->from('company_website')->where($where);
		return $db->fetchAll($sql);
	}
	
	public function add($data)
	{	
		if(isset($data['company']))
		{	
			$data['name'] = $data['company'];
			$data['author'] = $this->getCurrentUser()->id;
			$newdata = $this->unsetNonTableFields($data);
			$table = $this->getTable();
			$Company = $table->fetchRow('name = \''.$data['name'].'\'');
			if($Company)
			{	
				$Company = $Company->toArray();
				return $Company['id'];
			}
			return $table->insert($newdata);
		}
	}
	
	public function getCompanies()
	{	
		$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		$select = $db->select()	->from(array('c'=>'company'),array('c.id','c.name'))
								->joinLeft(array('g'=>'groups'),'c.public = g.id','')
								->joinLeft(array('pg'=>'profile_group'),'pg.id_group = g.id','')
								->where('author = ? or public = 0 or pg.id_profile = ?', $userId);
		return $db->fetchAll($select);
	}
	
	public function fetchPairs()
	{	
		$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		$select = $db->select()	->from(array('c'=>'company'),array('c.id','c.name'))
								->joinLeft(array('g'=>'groups'),'c.public = g.id','')
								->joinLeft(array('pg'=>'profile_group'),'pg.id_group = g.id','')
								->where('author = ? or public = 0 or pg.id_profile = ?', $userId);
		return $db->fetchPairs($select);
	}
	
	public function fetchRecord()
    {
    	$table = $this->getTable();
    	$person = $table->fetchRow('id = '.$this->_id)->toArray();
    	return $person;
    }
    
 	public function getNotes($start = 0, $limit = 6)
    {
    	$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('n.id_company = ?',$this->_id);
		$select = $db->select()	->from(array('n'=>'note'))
								->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
								->join(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email','avatar'))
								->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
								->where($where)
								->order('n.id desc')->limit($limit, $start);
		$comments = $db->fetchAll($select);
		foreach($comments as $i => $comment)
		{
			$fileModel = new Model_File();
			$comments[$i]['files'] = $fileModel->fetchByNote($comment['id']);
		}
		return $comments;
    }
    
	public function deleteNotes()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_company = ?', $this->_id);
		$select = $db->select()->from('note')->where($where);
		$notes = $db->fetchAll($select);
		$modelActivity = new Model_Activity();
		foreach ($notes as $note)
		{
			$modelActivity->setId($note['id']);
				
			try{
				$modelActivity->deleteNote($note['id']);
			}catch(Exception $e){
		
			}
		}
		return $db->delete('note', $where);
	}
    
    public function delete()
    {
    	$table = $this->getTable();
    	$company = $table->fetchRow('id = '.$this->_id)->toArray();
    	$db = $table->getAdapter();
    	$dbconf = $db->getConfig();
    	$location = PUBLIC_PATH.$company['avatar'];
    	
    	if($table->delete('id = '.$this->_id))
    	{
	    	try{
	    		unlink($location);
	    	}
	    	catch (Exception $e)
	    	{
	    		
	    	}
    	}
    	
    }
    
    public function fetch()
    {
    	$table = $this->getTable();
    	return $table->fetchRow('id = '.$this->_id)->toArray();
    }
    
    public function edit($data)
    {
    	$table = $this->getTable();
    	$db = $table->getAdapter();
    	
    	$where = $db->quoteInto('id_company = ?', $data["id"]);
   		
   		
   		$db->delete('company_phone',$where);
   		$db->delete('company_email',$where);
   		$db->delete('company_website',$where);
   		$db->delete('company_address',$where);
   		
    	if(isset($data['phone1']) && $data['phone1'])
    	{	
    		foreach($data['phone1'] as $phone)
    		{	
    			$bind = array('id_company'=>$data["id"], 'phone'=>$phone, 'phone_type'=>1);
    			$db->insert('company_phone', $bind);
    		}
    	}
    	if(isset($data['email1']) && $data['email1'])
    	{	
    		foreach($data['email1'] as $email)
    		{	
    			$bind = array('id_company'=>$data["id"], 'name'=>$email, 'email_type'=>1);
    			$db->insert('company_email', $bind);
    		}
    	}
    	if(isset($data['website1']) && $data['website1'])
    	{
    		foreach($data['website1'] as $email)
    		{	
    			$bind = array('id_company'=>$data["id"], 'name'=>$email, 'website_type'=>1);
    			$db->insert('company_website', $bind);
    		}
    	}
    	if(isset($data['address1']) && $data['address1'])
    	{
    		
    		$company_address = array();
    		$company_address_columns = array('address1', 'city1', 'state1', 'zip1', 'country1', 'address_type1');
    		
    		foreach($data as $key => $value)
    		{
    			if(in_array($key, $company_address_columns))
    			{
    				foreach($value as $k => $v)
    				{
    					$company_address[$k][substr($key, 0, -1)] = $v;
    				}
    			}
    		}
    		
    		foreach($company_address as $company)
    		{
    			$company['id_company'] = $data["id"];
    			$db->insert('company_address', $company);
    		}
    		
    		/*
    		foreach($data['address1'] as $email)
    		{	
    			$bind = array('id_company'=>$data["id"], 'address'=>$email, 'address_type'=>1);
    			$db->insert('company_address', $bind);
    		}
    		*/
    	}
    	
    	if(isset($data['access']))
    	{
    		switch ($data['access'])
    		{
    			case 0 :
    			case 1 :
    				$newdata['group'] = null;
    				break;
    			case 2 :
    				$newdata['group'] = $data['group'];
    				break;
    		}
    	
    		$data['group'] = $newdata['group'];
    	}
    	
    	$data = $this->unsetNonTableFields($data);
    	$where = $db->quoteInto('id = ?', $data['id']);
    	$table->update($data, $where);
    	return true;
    }
    
    public function changePermission($data)
    {
    	$table = $this->getTable();
    	$db = $table->getAdapter();
    
    	$data = $this->unsetNonTableFields($data);
    	$where = $db->quoteInto('id = ?', $data['id']);
    		
    	$table->update($data, $where);
    	return true;
    }
}