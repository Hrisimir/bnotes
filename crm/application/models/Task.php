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
		$where = $db->quoteInto('author = ? or public = 1', $this->getCurrentUser()->id);
		$select = $db	->select()
						->from(array('t'=>'task'))
						->joinLeft(array('tc'=>'nmcl_taskcategory'),'tc.id = t.id_category',array('category'=>'name'));
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
		$newdata['duedate'] = date("Y-m-d H:i:s", strtotime($data['due']));
		$newdata['name'] = $data['name'];
		$newdata['id_category'] = $data['cat'];
		return $this->getTable()->insert($newdata);
	}
	
	public function delete($id)
	{
		return $this->getTable()->delete('id = '.$id);
	}
	
	public function getTaskNumber()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('author = ? or public = 1', $this->getCurrentUser()->id);
		$select = $db->select()->from('task','count(id)')->where($where);
		return $db->fetchOne($select);
	}
}