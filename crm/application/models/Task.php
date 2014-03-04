<?php
class Model_Task extends Model_Abstract
{
	protected $_dbTableClass = 'Model_DbTable_Task';
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
	
	public function fetchAll()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('author = ? or owner = ?', $this->getCurrentUser()->id);
		$select = $db	->select()
						->from(array('t'=>'task'))
						->joinLeft(array('tc'=>'nmcl_taskcategory'),'tc.id = t.id_category',array('category'=>'name'))
						->joinLeft(array('p'=>'profile'),'p.id = t.owner', array('ownername' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
						->where($where)
						->where('is_finished = ?', 0)
						->order('duedate desc');
		
		
		return $db->fetchAll($select);
	}
	
	public function fetchCategory()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$select = $db->select()->from('nmcl_taskcategory',array('id','name'));
		return  $db->fetchAll($select);
		
	}
	public function add($data)
	{
		$newdata['author'] = $this->getCurrentUser()->id;
		$newdata['public'] = $data['publ'];
		$newdata['owner'] = $data['owner'];
		$newdata['duedate'] = date("Y-m-d H:i:s", strtotime($data['due']));
		$newdata['name'] = $data['name'];
		$newdata['id_category'] = $data['cat'];
		if(isset($data['id_person']))$newdata['id_person'] = $data['id_person'];
		if(isset($data['id_company']))$newdata['id_company'] = $data['id_company'];
		if(isset($data['id_case']))$newdata['id_case'] = $data['id_case'];
		return $this->getTable()->insert($newdata);
	}
	
	public function delete($id)
	{
		return $this->getTable()->delete('id = '.$id);
	}
	
	public function finish($id)
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		
		$where = $db->quoteInto('id = ?', $id);
		
		return $table->update(array('is_finished' => 1), $where);
	}
	
	public function getTaskNumber()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('author = ? or owner = ?', $this->getCurrentUser()->id);
		$select = $db->select()->from('task','count(id)')->where($where);
		return $db->fetchOne($select);
	}
	
	public function getTask()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id = ?', $this->_id);
		$select = $db->select()->from('task')->where($where);

		$task = $db->query($select)->fetchAll();
		
		if($task)
		{
			if(isset($task['0']['duedate']))
			{
				$task['0']['duedate'] = date("d-m-Y", strtotime($task['0']['duedate']));
			}
			return $task['0'];
		} else {
			return false;
		}
	}
	
	public function edit($data)
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		
		$newdata['author'] = $this->getCurrentUser()->id;
		$newdata['public'] = $data['publ'];
		$newdata['owner'] = $data['owner'];
		$newdata['duedate'] = date("Y-m-d H:i:s", strtotime($data['due']));
		$newdata['name'] = $data['name'];
		$newdata['id_category'] = $data['cat'];
		if(isset($data['id_person']) && $data['id_person'] <> 'null') $newdata['id_person'] = $data['id_person'];
		if(isset($data['id_company']) && $data['id_company'] <> 'null')$newdata['id_company'] = $data['id_company'];
		if(isset($data['id_case']) && $data['id_case'] <> 'null')$newdata['id_case'] = $data['id_case'];
		
		$where = $db->quoteInto('id = ?', $this->_id);

		try {
			return $table->update($newdata, $where);
		} catch (Exception $e)
		{
			Zend_Debug::dump($e->getMessage());die;
		}
	}
	
	public function filtertask($data)
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		
		$select = $db	->select()
			->from(array('t'=>'task'), 't.id')
			->joinLeft(array('tc'=>'nmcl_taskcategory'),'tc.id = t.id_category', '')
			->joinLeft(array('p'=>'profile'),'p.id = t.owner', '')
			->where('is_finished = ?', 0)
			->order('duedate desc');
			
		
		if(isset($data['type']) && $data['type'] <> 0)
		{
			switch ($data['type'])
			{
				case 1:
					$whereType = $db->quoteInto('duedate > ?', date('Y-m-d'));
					break;
				case 2:
					$whereType = $db->quoteInto('is_finished = ?', 1);
					break;
				case 3:
					$whereType = $db->quoteInto('duedate = ?', date('Y-m-d'));
					break;
				default:
					break;	
			}
			$select->where($whereType);
		}
		
		if(isset($data['author']) && $data['author'] <> 0)
		{
			$whereAuthor = $db->quoteInto('author = ? or owner = ?', $data['author']);
			$select->where($whereAuthor);
		}
		
		if(isset($data['category']) && $data['category'] <> 0)
		{
			$whereCategory = $db->quoteInto('id_category = ?', $data['category']);
			$select->where($whereCategory);
		}
		
		return $db->fetchAll($select);
		
	}
}