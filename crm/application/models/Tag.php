<?php
class Model_Tag extends Model_Abstract {
	protected $_dbTableClass = 'Model_DbTable_Tag';
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
	public function setId($_id) 
	{
		$this->_id = $_id;
	}
	
	public function deletetag($id)
	{
		$db = $this->getTable()->getAdapter();
		$where = $db->quoteInto('id_tag = ?', $id);
		$db->delete('tag_ref', $where);
		$where = $db->quoteInto('id = ?', $id);
		return $db->delete('tag',$where);
	}
	
	public function add($data)
	{
		$db = $this->getTable()->getAdapter();
		$select = $db->select()->from('tag','id')->where('name = ?', $data['tag']);
		$tagid = $db->fetchOne($select);
		if(!$tagid)
		{	
			$bind = array('name'=>$data['tag']);
			$db->insert('tag', $bind);
			$select = $db->select()->from('tag',array(new Zend_Db_Expr('max(id) as maxId')));
			$tagid = $db->fetchOne($select);
		}
		$select = $db->select()->from('tag_ref','id_ref')->where('id_ref = ?', $data['id_ref'])->where('type = ?', $data['type'])->where('id_tag = ?', $tagid);
		$check = $db->fetchAll($select);
		if(!$check)
		{
			$bind = array('id_tag'=>$tagid, 'type'=>$data['type'], 'id_ref'=>$data['id_ref']);
			$db->insert('tag_ref', $bind);
		}
	}
	
	public function addTagToAll($data)
	{
		try {
			$db = $this->getTable()->getAdapter();
			$db->beginTransaction();
			$select = $db->select()->from('tag','id')->where('name = ?', $data['tag']);
			$tagid = $db->fetchOne($select);
		
			if(!$tagid)
			{
				$bind = array('name'=>$data['tag']);
				$db->insert('tag', $bind);
				$select = $db->select()->from('tag',array(new Zend_Db_Expr('max(id) as maxId')));
				$tagid = $db->fetchOne($select);
			}
			
			$where = $db->quoteInto('id_tag = ?', $tagid);
			$db->delete('tag_ref', $where);
		
			$sql = 'INSERT INTO tag_ref 
				SELECT '.$tagid.', id, 1
				FROM person';
		
			$db->query($sql);
			
			$sql = 'INSERT INTO tag_ref
				SELECT '.$tagid.', id, 2
				FROM company';
			
			$db->query($sql);
			
			$db->commit();
		} catch (Exception $e)
		{
			$db->rollBack();
			Zend_Debug::dump($e->getMessage());die;
		}
		
		return true;
	}
	
	public function removetag($data)
	{
		$db = $this->getTable()->getAdapter();
		$where = $db->quoteInto('id_ref = ?', $data['id_ref']).$db->quoteInto(' and type = ?', $data['type']).$db->quoteInto(' and id_tag = ?', $data['id_tag']);
		$return = $db->delete('tag_ref', $where);
		
		$where = $db->quoteInto('type = ?', $data['type']).$db->quoteInto(' and id_tag = ?', $data['id_tag']);
		$select = $db->select()->from('tag_ref')->where($where);
		
		$tags = $db->fetchAll($select);
		$where = $db->quoteInto('id = ?', $data['id_tag']);
		if(!$tags) $db->delete('tag',$where);
		return $return;
	}
	
	function sortAndIndexArray($aArray)
	
	{
	
		sort($aArray);
		$aFinal = null;
		foreach($aArray as $sWord)
	
		{
	
			$aFinal[ mb_strtoupper(mb_substr($sWord['name'], 0, 1, 'UTF-8'),'UTF-8') ][] = array('name'=>ucfirst($sWord['name']),'id'=> $sWord['id']);
		}
		if($aFinal)
		ksort($aFinal);
		return $aFinal;
	
	}
	
	public function fetchTags($id_ref = null, $type = 1 )
	{
		$db = $this->getTable()->getAdapter();
		$select = $db->select()->from(array('tr'=>'tag_ref'),array('id_ref','type'))
		->join(array('t'=>'tag'),'t.id = tr.id_tag')
		->where('tr.id_ref = ?', $id_ref)->where('tr.type = ?', $type);
		
		return $db->fetchAll($select);
	}
	
	public function fetchAllTags()
	{
		$db = $this->getTable()->getAdapter();
		$select = $db->select()->from(array('t'=>'tag'),array('t.id', 't.name', 'tr.id_tag', 'tr.id_ref', 'tr.type'))
		->join(array('tr'=>'tag_ref'),'t.id = tr.id_tag');
		
		return $db->fetchAll($select);
	}
	
	public function fetch()
	{
		$table = $this->getTable();
		$result = $table->find($this->_id);
		//Zend_Debug::dump($result);die;
		if($result){
			$result = $result->toArray();
		}
		return $result['0'];
	}
	
	public function edit($data)
	{
		$table = $this->getTable();
		$data = $this->unsetNonTableFields($data);
		//Zend_Debug::dump($data);die;
		$db = $this->getTable()->getAdapter();
		$select = $db->select()->from('tag','id')->where('name = ?', $data['name']);
		$tagid = $db->fetchOne($select);
		
		if(!$tagid)
		{
			$where = $db->quoteInto('id = ?', $data['id']);
		
			return $table->update($data, $where);
		} else {
			return false;
		}
		
	}
}