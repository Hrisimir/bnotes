<?php
class Model_Project extends Model_Abstract
{
	protected $_dbTableClass = 'Model_DbTable_Project';
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
	
	public function fetch() {
		return $this->getTable()->fetchRow('id = ' . $this->_id);
	}
	
	public function add($data)
	{	
		$table = $this->getTable();
		$data["author"] = $this->getCurrentUser()->id;
		$data["access"] = 0;
		$data = $this->unsetNonTableFields($data);
		unset($data["id"]);
		return $table->insert($data);
	}
	
	public function update($data)
	{
		$table = $this->getTable();
		$data["author"] = $this->getCurrentUser()->id;
		$data["access"] = 0;
		$data = $this->unsetNonTableFields($data);
		$id = $data["id"];
		unset($data["id"]);
		return $table->update($data, 'id = '.$id);
	}
	
	public function delete($id)
	{
		return $this->getTable()->delete('id = '.$id);
	}
	
	public function fetchCount()
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$select = $db->select()->from('case','count(id)');
		return $db->fetchOne($select);
	}
	
	public function getNotes($start = 0, $limit = 6)
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('n.id_case = ?',$this->_id);
		$select = $db->select()	->from(array('n'=>'note'))
								->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
								->join(array('proj'=>'case'),'proj.id = n.id_case',array('id_case'=>'id','case'=>'name'))
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
	
	public function deleteNotes($id)
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$where = $db->quoteInto('id_case = ?', $id);
		return $db->delete('note', $where);
	}
}