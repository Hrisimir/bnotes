<?php
class Model_Activity extends Model_Abstract {
	protected $_dbTableClass = 'Model_DbTable_Activity';
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
	public function updateNote($data)
	{	
		if(isset($data['id']) && isset($data['text']))
		{
			$bind['note'] = $data['text'];
			$bind['author'] = $this->getCurrentUser()->id;
			$table = $this->getTable();
			$where = $table->getAdapter()->quoteInto('id = ?', $data['id']);
			
			return $table->update($bind, $where);
		}
		return 0;
	}
	
	public function fetchMain()
	{
		$table = $this->getTable();
		$data = $table->fetchRow('id = '.$this->_id)->toArray();
		if($data['pid'])return $data['pid'];
		return $this->_id;
	}
	
	public function fetch()
	{
		$table = $this->getTable();
		return $table->fetchRow('id = '.$this->_id)->toArray();
	}
	
	public function fetchComments($start = 0, $limit = 6)
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('pid = ?', $this->_id);
		$select = $db->select()	->from(array('n'=>'note'))
								->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
								->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email'))
								->joinLeft(array('proj'=>'case'),'proj.id = n.id_case',array('id_case'=>'id','case'=>'name'))
								->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
								->where($where)
								->order('n.id desc')
								->limit($limit, $start);;
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
		if(isset($data['note']))
		{	
			//Zend_Debug::dump($data);die;
			$table = $this->getTable();
			$newdata['note'] = $data['note'];
			$newdata['author'] = $this->getCurrentUser()->id;
			if(isset($data['id_company']) && $data['id_company']){$newdata['id_company'] = $data['id_company'];}else{$newdata['id_company'] = 0;}
			if(isset($data['id_person']) && $data['id_person']) $newdata['id_person'] = $data['id_person'];
			if(isset($data['id_case']) && $data['id_case']){$newdata['id_case'] = $data['id_case'];}
			if(isset($data['when']) && $data['when']){$newdata['when'] = date ("Y-m-d", strtotime($data['when']));}
			if(isset($data['pid'])){$newdata['pid'] = $data['pid'];}
			if(isset($data['access']))
			{
				$newdata['access'] = $data['access'];
				switch ($data['access'])
				{	
					case 0 :
					case 1 :
						break;
					case 2 :
						$newdata['group'] = $data['group'];
						break;
				}
			}
			return $table->insert($newdata);
		}
	}
	
	public function edit($data)
	{
	if(isset($data['note']))
		{	
			//Zend_Debug::dump($data);die;
			$table = $this->getTable();
			$newdata['note'] = $data['note'];
			$newdata['author'] = $this->getCurrentUser()->id;
			if(isset($data['id_company']) && $data['id_company']){$newdata['id_company'] = $data['id_company'];}else{$newdata['id_company'] = 0;}
			if(isset($data['id_person']) && $data['id_person']) $newdata['id_person'] = $data['id_person'];
			if(isset($data['id_case']) && $data['id_case']){$newdata['id_case'] = $data['id_case'];}
			if(isset($data['when']) && $data['when']){$newdata['when'] = date ("Y-m-d", strtotime($data['when']));}
			if(isset($data['pid'])){$newdata['pid'] = $data['pid'];}
			if(isset($data['access']))
			{
				$newdata['access'] = $data['access'];
				switch ($data['access'])
				{	
					case 0 :
					case 1 :
						break;
					case 2 :
						$newdata['group'] = $data['group'];
						break;
				}
			}
			$where = $table->getAdapter()->quoteInto('id = ?', $data['id']);
			return $table->update($newdata,$where);
		}
	
	}
	
	
	public function fetchAll($start = 0, $limit = 6)
	{	
		$table = $this->getTable();
		$db = $table->getAdapter();
		$user = $this->getCurrentUser();
		$select1 = $db->select()->from(array('pg'=>'profile_group'),'')
									->join(array('n'=>'note'), 'n.group = pg.id_group')
									->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
									->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email'))
									->joinLeft(array('proj'=>'case'),'proj.id = n.id_case',array('id_case'=>'id','case'=>'name'))
									->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
									->where('n.access = 2')
									->where('id_profile = ?',$user->id);
		
		$select2 = $db->select()->from(array('n'=>'note'))
									->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
									->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email'))
									->joinLeft(array('proj'=>'case'),'proj.id = n.id_case',array('id_case'=>'id','case'=>'name'))
									->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
									->where('n.access = 1')
									->where('n.author = ?',$user->id);
		
		$select3 = $db->select()->from(array('n'=>'note'))
									->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
									->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email'))
									->joinLeft(array('proj'=>'case'),'proj.id = n.id_case',array('id_case'=>'id','case'=>'name'))
									->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
									->where('n.access = 0');
								
		$select = $db->select()
	    ->union(array($select1, $select2, $select3))
	    ->order('id desc')->limit($limit, $start);				
		$comments = $db->fetchAll($select);
		foreach($comments as $i => $comment)
		{
			$fileModel = new Model_File();
			$comments[$i]['files'] = $fileModel->fetchByNote($comment['id']);
		}
		return $comments;
	}
}