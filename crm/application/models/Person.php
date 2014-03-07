<?php
class Model_Person extends Model_Abstract
{
	 
	protected $_dbTableClass = 'Model_DbTable_Person';
	protected $id;
  
    /**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
	
	public function deleteNotes()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_person = ?', $this->id);
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
	
	public function fetchCookieLatest($string = '')
	{
		$data = Zend_Json::decode($string);
		$flag = false;
		$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		
		//->joinLeft(array('g'=>'groups'),'p.publ = g.id','')
		//->joinLeft(array('pg'=>'profile_group'),'pg.id_group = g.id','')
		//->where('p.author = ? or p.publ = 0 or pg.id_profile = ?', $userId)
		
		$selectCompany = null;
		$selectPerson =null;
		
		if(isset($data['person']) && $data['person'])
		{	
			$where = 'p.id in (';
			foreach ($data['person'] as $idperson)
			{
				$where .= $idperson.',';
			}
			$where .= '0)';
			
			$flag = true;
			$selectPerson = $db->select()	->from(array('p'=>'person'),array('p.id', 'p.email','p.website','name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)"), 'type' => new Zend_Db_Expr("1")))
			->join(array('c'=>'company'),'p.id_company = c.id', array('company'=>'c.name', 'id_company'=>'id'));
			$selectPerson->where($where);
		}
		
		if(isset($data['company']) && $data['company'])
		{
			$where = 'c.id in (';
			foreach ($data['company'] as $idperson)
			{
				$where .= $idperson.',';
			}
			$where .= '0)';
			
			$flag = true;
			$selectCompany = 	$db->select()->from(array('c'=>'company'),array('c.id', 'c.email','c.website','c.name', 'type' => new Zend_Db_Expr("2"), 'c.name', 'c.id'));
			$selectCompany->where($where);
		}
		
		if($flag && $selectCompany && $selectPerson){
			$select = $db->select()->union(array($selectPerson,$selectCompany))
			->order('id desc')
			->limit(8);
			return $db->fetchAll($select);
		}elseif($flag && $selectCompany){
			$select = $selectCompany
			->order('id desc')
			->limit(8);
			return $db->fetchAll($select);
		}elseif($flag && $selectPerson)
		{
			$select = $selectPerson
			->order('id desc')
			->limit(8);
			return $db->fetchAll($select);
		}
		return null;
	}
	
	public function searchValue($value)
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto("name like ?", '%'.$value.'%');
		$where2 = $db->quoteInto("firstname like ? or lastname like ?", '%'.$value.'%');
		$sql1 = $db->select()->from('person',array('id', 'type'=> new Zend_Db_Expr("1"), 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)")))->where($where2);
		$sql2 = $db->select()->from('company', array('id', 'type'=> new Zend_Db_Expr("2"),'name'))->where($where);
		$sql3 = $db->select()->from('case',array('id', 'type'=> new Zend_Db_Expr("3"),'name'))->where($where);
		$select = $db->select()
	    ->union(array($sql1, $sql2, $sql3))
	    ->order("id");
		$data =  $db->fetchAll($select);
		$result['query'] = $value;
		$result['data'] = array();
		$result['suggestions'] = array();
		if($data)
		{
			foreach($data as $i=>$row)
			{
				$result['data'][$i] = $row['id'].$row['type'];
				$result['suggestions'][$i] = $row['name'];
			}
		}
		return $result;
	}
	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	public function fetchByCompany($id, $limitend = 0, $limitstart = 6)
	{
		$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		$selectPerson = $db->select()	
							->from(array('p'=>'person'),array('p.id',  'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)"), 'type' =>new Zend_Db_Expr("1"), 'phone'=>'p.phone1','p.email'))
							->where('id_company = ?', $id)
							->order('id desc');
							//->limit($limitend, $limitstart);
		return $db->fetchAll($selectPerson);
	
	}
	public function fetchPhones()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_person = ?', $this->id);
		$sql = $db->select()->from('person_phone')->where($where);
		return $db->fetchAll($sql);
	}
	
	public function fetchEmails()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_person = ?', $this->id);
		$sql = $db->select()->from('person_email')->where($where);
		return $db->fetchAll($sql);
	}
	
	public function fetchAdresses()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_person = ?', $this->id);
		$sql = $db->select()->from('person_address')->where($where);
		
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
		$where = $db->quoteInto('id_person = ?', $this->id);
		$sql = $db->select()->from('person_website')->where($where);
		return $db->fetchAll($sql);
	}
	
	public function merge($id, $newid)
	{	
		$table = $this->getTable();
		$db = $table->getAdapter();
		$newdata = $table->fetchRow('id = '.$newid)->toArray();
		$olddata = $table->fetchRow('id = '.$id)->toArray();
		$bind['id_person'] = $id;
		
		//update task
		$where = $db->quoteInto('id_person = ?', $newid);
		$db->update('task', $bind, $where);
		
		//update case_person
		$where = $db->quoteInto('id_person = ?', $newid);
		$db->update('case_person', $bind, $where);
		
		//update note
		$where = $db->quoteInto('id_person = ?', $newid);
		$db->update('note', $bind, $where);
		
		//update person_address
		$where = $db->quoteInto('id_person = ?', $newid);
		$db->update('person_address', $bind, $where);
		
		//update person_email
		$where = $db->quoteInto('id_person = ?', $newid);
		$db->update('person_email', $bind, $where);
		
		//update person_meta
		$where = $db->quoteInto('id_person = ?', $newid);
		$db->update('person_meta', $bind, $where);
		
		//update person_phone
		$where = $db->quoteInto('id_person = ?', $newid);
		$db->update('person_phone', $bind, $where);
		
		//update person_website
		$where = $db->quoteInto('id_person = ?', $newid);
		$db->update('person_website', $bind, $where);
		
		
		//update tag_ref
		$bindtr['id_ref'] = $id;
		$where = $db->quoteInto('id_ref = ? and type = 1', $newid);
		$db->update('tag_ref', $bindtr, $where);
		
		//update person
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
	
	public function fetchAll($limit = 20)
    {
    	$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		$select = $db->select()	->from(array('p'=>'person'),array('p.id',  'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)")))
								->join(array('c'=>'company'),'p.id_company = c.id', array('company'=>'c.name'))
								->joinLeft(array('g'=>'groups'),'p.publ = g.id','')
								->joinLeft(array('pg'=>'profile_group'),'pg.id_group = g.id','')
								->where('p.author = ? or p.publ = 0 or pg.id_profile = ?', $userId)
								->order('p.id desc')
								->limit($limit);
		return $db->fetchAll($select);
    }
    
    public function fetchContagtsByTag($tag, $limitstart = 0, $limitend = 6)
    {
    	$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		
		$selectPerson = $db->select()->from(array('tr'=>'tag_ref'),'')
							->join(array('p'=>'person'),'p.id = tr.id_ref and tr.type = 1' ,array('p.id',  'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)"), 'type' =>new Zend_Db_Expr("1")))
							->where('tr.id_tag = ?', $tag);
		$selectCompany = $db->select()->from(array('tr'=>'tag_ref'),'')
							->join(array('c'=>'company'),'c.id = tr.id_ref and tr.type = 2',array('c.id',  'c.name', 'type' =>new Zend_Db_Expr("0")))
							->where('tr.id_tag = ?', $tag);
		$select = $db->select()->union(array($selectPerson, $selectCompany))
							->order('name asc')
							->limit($limitend, $limitstart);
		return $db->fetchAll($select);
    }
    
    public function getContactsNumber()
    {
    	$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		$select = $db->select()	->from(array('p'=>'person'),array('count(p.id) as num'));
		$select1 = $db->select()	->from(array('c'=>'company'),array('count(c.id) as num'));
		return $db->fetchOne($select)+ $db->fetchOne($select1);
    }
    
    public function delete()
    {
    	$table = $this->getTable();
    	$table->delete('id = '.$this->id);
    }
    
    public function fetchAllConatacts($limitstart = 0, $limitend = 6, $filters = null)
    {	
    	
    	$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		$selectPerson = $db->select()->from(array('p'=>'person'),array('p.id',  'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)"), 'type' =>new Zend_Db_Expr("1"), 'p.email','phone'=>'p.phone1'))->join(array('c'=>'company'), 'c.id = p.id_company', array('avatar','id_company'=>'id'));
		$selectCompany = $db->select()->from(array('c'=>'company'),array('c.id',  'c.name', 'type' =>new Zend_Db_Expr("0"),'c.email', 'c.phone', 'c.avatar','id_company'=>'c.id'));
		
		$select = $db->select();
		
		if($filters)
		{
			foreach ($filters as $k=>$v)
			{	
				if($k == 'type')
				{
					if($v == 1)
					{
						$selectCompany->where('0 = 1');
					}else{
						$selectPerson->where('0 = 1');
					}
					continue;
				}
				
				$is_required = $this->query('select contact_type from required_filters where name = "'.$k.'"');
				
				if(isset($is_required))
				{
					if($is_required['0']['contact_type'] == '1')
					{
						$condP = $db->quoteInto('`p`.'.$k.' like "%'.$v.'%"', '');
						$selectPerson->where($condP);
						$selectCompany->where('0 = 1');
					} else {
						if($is_required['0']['contact_type'] == '2')
						{
							$condP = $db->quoteInto('`p`.'.$k.' like "%'.$v.'%"', '');
							$selectPerson->where($condP);
							$selectCompany->where('0 = 1');
						} else {
							$condC = $db->quoteInto('`c`.'.$k.' like "%'.$v.'%"', '');
							$condP = $db->quoteInto('`p`.'.$k.' like "%'.$v.'%"', '');
							$selectCompany->where($condC);
							$selectPerson->where($condP);
						}
					} 
				} else {
					$condP = $db->quoteInto('`p`.'.$k.' = ?', $v);
					$condC = $db->quoteInto('`c`.'.$k.' = ?', $v);
					
					$selectPerson->where($condP);
					$selectCompany->where($condC);
				}
			}
		}
		$select ->union(array($selectPerson, $selectCompany))
								->order('id desc')
								->limit($limitend, $limitstart);
		
		//Zend_Debug::dump($select->__toString());die;
		$contact = $db->fetchAll($select);
		
		if($contact)
		{
			$tagObj = new Model_Tag();
			
			foreach ($contact as $key => $value)
			{
				switch ($value['type'])
				{
					case 0:
						$contact[$key]['tags'] = $tagObj->fetchTags($value["id"], 2);
						break;
					case 1:
						$contact[$key]['tags'] = $tagObj->fetchTags($value["id"], 1);
						break;
					default:
						$contact[$key]['tags'] = $tagObj->fetchTags($value["id"], 1);
						break;
				}
				
			}
		}

		return $contact;
    }
    
    public function fetchLatest()
    {
    	$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		$selectPerson = $db->select()	->from(array('p'=>'person'),array('p.id', 'p.email','p.website','name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)"), 'type' => new Zend_Db_Expr("1")))
								->join(array('c'=>'company'),'p.id_company = c.id', array('company'=>'c.name', 'id_company'=>'id'));
								//->joinLeft(array('g'=>'groups'),'p.publ = g.id','')
								//->joinLeft(array('pg'=>'profile_group'),'pg.id_group = g.id','')
								//->where('p.author = ? or p.publ = 0 or pg.id_profile = ?', $userId)
		$selectCompany = 	$db->select()->from(array('c'=>'company'),array('c.id', 'c.email','c.website','c.name', 'type' => new Zend_Db_Expr("2"), 'c.name', 'c.id'));			
		$selectCase = 		$db->select()->from(array('proj'=>'case'),array('proj.id', 'proj.name','proj.name','proj.name', 'type' => new Zend_Db_Expr("3"), 'proj.name', 'proj.id'));
		
		$select = $db->select()->union(array($selectPerson,$selectCompany, $selectCase))
								->order('id desc')
								->limit(5);
								
		return $db->fetchAll($select);
    }
    
    public function edit($data)
    {
    	$table = $this->getTable();
    	$db = $table->getAdapter();
   		$data['id_company'] = $data['company'];
   		$where = $db->quoteInto('id_person = ?', $data["id"]);
   		
   		
   		$db->delete('person_phone',$where);
   		$db->delete('person_email',$where);
   		$db->delete('person_website',$where);
   		$db->delete('person_address',$where);
   		
    	if(isset($data['phone']) && $data['phone'])
    	{	
    		foreach($data['phone'] as $phone)
    		{	
    			$bind = array('id_person'=>$data["id"], 'phone'=>$phone, 'phone_type'=>1);
    			$db->insert('person_phone', $bind);
    		}
    	}
    	if(isset($data['email1']) && $data['email1'])
    	{	
    		foreach($data['email1'] as $email)
    		{	
    			$bind = array('id_person'=>$data["id"], 'name'=>$email, 'email_type'=>1);
    			$db->insert('person_email', $bind);
    		}
    	}
    	if(isset($data['website1']) && $data['website1'])
    	{
    		foreach($data['website1'] as $email)
    		{	
    			$bind = array('id_person'=>$data["id"], 'name'=>$email, 'website_type'=>1);
    			$db->insert('person_website', $bind);
    		}
    	}
    	if(isset($data['address1']) && $data['address1'])
    	{
    		$person_address = array();
    		$person_address_columns = array('address1', 'city1', 'state1', 'zip1', 'country1', 'address_type1');

    		foreach($data as $key => $value)
    		{
    			if(in_array($key, $person_address_columns))
    			{
    				foreach($value as $k => $v)
    				{
    					$person_address[$k][substr($key, 0, -1)] = $v;
    				}
    			}
    		}
    		
    		foreach($person_address as $person)
    		{
    			$person['id_person'] = $data["id"];
    			$db->insert('person_address', $person);
    		}
    		
    		/*
    		foreach($data['address1'] as $email)
    		{	
    			$bind = array('id_person'=>$data["id"], 'address'=>$email, 'address_type'=>1);
    			$db->insert('person_address', $bind);
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
    	//Zend_Debug::dump($data);die;
    	$data = $this->unsetNonTableFields($data);
    	$where = $db->quoteInto('id = ?', $data['id']);
    	
    	$table->update($data, $where);
    	return true;
    }
    
    public function fetch()
    {
    	$table = $this->getTable();
    	return $table->fetchRow('id = '.$this->id)->toArray();
    }
    
 	public function fetchRecord()
    {
    	$table = $this->getTable();
    	$person = $table->fetchRow('id = '.$this->id)->toArray();
    	$db = $table->getAdapter();
    	if(isset($person['id']))
    	{
	    	$selectCompany = $db->select()->from('company')->where('id = '.$person['id_company']);
	    	$person['company'] = $db->fetchRow($selectCompany);
    	}
    	return $person;
    }
    
    public function getNotes($start = 0, $limit = 6)
    {
    	$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('n.id_person = ?',$this->id);
		$select = $db->select()	->from(array('n'=>'note'))
								->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
								->join(array('per'=>'person'),'per.id = '.$this->id, array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
								->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email','avatar'))
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
    
	public function add($data)
	{	
		
		if(isset($data['firstname']) && isset($data['lastname']) && isset($data['id_company']))
		{	
			$data['author'] = $this->getCurrentUser()->id;
			$newdata = $this->unsetNonTableFields($data);
			
			$table = $this->getTable();
			$person = $table->fetchRow('firstname = \''.$data['firstname'].'\' and lastname = \''.$data['lastname'].'\' and id_company = \''.$data['id_company'].'\' ');
			if($person)
			{	
				$person = $person->toArray();
				return $person['id'];
			}
		
			return $table->insert($newdata);
		}
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
	
	public function changePermissionAllContacts($access, $group)
	{
		try {
			$table = $this->getTable();
			$db = $table->getAdapter();
			
			$db->beginTransaction();
			//$user = $this->getCurrentUser()->id
			//$where = $db->quoteInto('id_autohr = ?', $user);
		
			$sqlPerson = 'UPDATE person
					SET access = "'.$access.'"';

			$sqlCompany = 'UPDATE company
					SET access = "'.$access.'"';
		
			if($group)
			{
				$sqlGroup = ', `group` = "'.$group.'"';
			} else {
				$sqlGroup = ', `group` = NULL';
			}
			
			$sqlPerson .= $sqlGroup;
			$sqlCompany .= $sqlGroup;
			
			$db->query($sqlPerson);
			$db->query($sqlCompany);
			
			$db->commit();
		} catch (Exception $e)
		{
			$db->rollBack();
			Zend_Debug::dump($e->getMessage());die;
		}
		
		return true;
	}
	
	public function getPersons()
	{
		$userId = $this->getCurrentUser()->id;
		$table = $this->getTable();
		$db = $table->getAdapter();
		$select = $db->select()	->from(array('p'=>'person'),array('p.id', 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)")))
			->joinLeft(array('g'=>'groups'),'p.publ = g.id','')
			->joinLeft(array('pg'=>'profile_group'),'pg.id_group = g.id','')
			->where('author = ? or publ = 0 or pg.id_profile = ?', $userId);
		return $db->fetchAll($select);
	}
}