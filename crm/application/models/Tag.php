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
}