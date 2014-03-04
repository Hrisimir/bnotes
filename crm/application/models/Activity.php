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
			
			if(isset($data['prfile']))
			{
				$modelProfile = new Model_Profile();
				
				foreach ($data['prfile'] as $profile)
				{
					$modelProfile->setId($profile);
					
					$profileData = $modelProfile->fetch();
					
					if(isset($profileData['email']))
					{
						$mailData['content'] = $data['note'];
						$mailData['mailFrom'] = 'crm.com';
						$mailData['mailTo'] = $profileData['email'];
						$mailData['subject'] = 'New Note in crm.com';
						
						$this->send($mailData);
					}
				}
			}
			
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
	
	
	public function fetchAll($start = 0, $limit = 6, $author = null)
	{	
		$table = $this->getTable();
		$db = $table->getAdapter();
		$user = $this->getCurrentUser();
		$select1 = $db->select()->from(array('pg'=>'profile_group'),'')
									->join(array('n'=>'note'), 'n.group = pg.id_group',array('id','pid','id_person','id_company','id_case','note','author','cdate','when'=>new Zend_Db_Expr("IFNULL(`when`, cdate)"),'access','group'))
									->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
									->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email','avatar'))
									->joinLeft(array('proj'=>'case'),'proj.id = n.id_case',array('id_case'=>'id','case'=>'name'))
									->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
									->where('n.access = 2')
									->where('id_profile = ?',$user->id);
		
		$select2 = $db->select()->from(array('n'=>'note'),array('id','pid','id_person','id_company','id_case','note','author','cdate','when'=>new Zend_Db_Expr("IFNULL(`when`, cdate)"),'access','group'))
									->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
									->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email','avatar'))
									->joinLeft(array('proj'=>'case'),'proj.id = n.id_case',array('id_case'=>'id','case'=>'name'))
									->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
									->where('n.access = 1')
									->where('n.author = ?',$user->id);
		
		$select3 = $db->select()->from(array('n'=>'note'),array('id','pid','id_person','id_company','id_case','note','author','cdate','when'=>new Zend_Db_Expr("IFNULL(`when`, cdate)"),'access','group'))
									->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
									->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email', 'avatar'))
									->joinLeft(array('proj'=>'case'),'proj.id = n.id_case',array('id_case'=>'id','case'=>'name'))
									->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
									->where('n.access = 0');

		if($author){
			$select1->where('n.author = ?', $author);
			$select2->where('n.author = ?', $author);
			$select3->where('n.author = ?', $author);
		}
		$select = $db->select()
	    ->union(array($select1, $select2, $select3))
	    ->order('when desc')
	    ->order('id desc')->limit($limit, $start);	
		//Zend_Debug::dump($select->__toString());die;
		$comments = $db->fetchAll($select);
		foreach($comments as $i => $comment)
		{
			$fileModel = new Model_File();
			$comments[$i]['files'] = $fileModel->fetchByNote($comment['id']);
		}
		return $comments;
	}
	
	public function getEmails()
	{		
		$db = $this->getTable()->getAdapter();
		$config = Zend_Registry::get('config')->toArray();
		if(isset($config['email']))
		{
			$params = array(
					 	'user'     => $config['email']['username'],
					    'host'     => $config['email']['imap'],
					    'password' => $config['email']['password'],
					    'port'     => $config['email']['imapPort'],
					    'ssl'      => $config['email']['ssl'], 
			);
			
			$flags = array(Zend_Mail_Storage::FLAG_SEEN);
			try{
			$mailStorage = new Zend_Mail_Storage_Imap($params);
			
			foreach($mailStorage as $message)
			{	
				
				if(!$message->hasFlag("\Seen"))
				{	
					$messages[] = imap_utf8($message->subject);
					$headers = $message->getHeaders();
					if(isset($headers["bcc"]) )
					{
						$t = preg_match('/\+(.*?)@/s', $headers["bcc"], $matches);
					}
					elseif(isset($headers["delivered-to"]))
					{
						$t = preg_match('/\+(.*?)@/s', $headers["delivered-to"], $matches);
					}
					if($matches)
					{	
						
						$arr = explode("a", $matches[1]);
						$id_company = $arr[0];
						$author = $arr[1];
						
						
						
						$sql = $db->select()->from('gmail_incoming')->where('id = ?', $headers["message-id"]);
						$check = $db->fetchAll($sql);
						
						if(!$check)
						{
							$db->insert('gmail_incoming', array('id'=>$headers["message-id"]));
							
							$foundPart = null;
							foreach (new RecursiveIteratorIterator($message) as $part) {
								try {
									$m['text'] = imap_utf8($headers["from"])." \n";
									$m['encoding'] = $part->getHeaderField('content-type', 'charset');
									mb_internal_encoding("UTF-8");
									$m['subject'] = mb_decode_mimeheader($message->subject);
									
							//	if (strtok($part->contentType, ';') == 'text/html' || strtok($part->contentType, ';') == 'text/plain' )
								//{	
								
									switch($part->contentTransferEncoding )
									{
										case 'base64':
											$m['text'] .= mb_convert_encoding(base64_decode($part->getContent()), 'UTF-8', $m['encoding']);
											break;
										case 'quoted-printable':
											$m['text'] .= mb_convert_encoding(quoted_printable_decode( $part->getContent()), 'UTF-8',$m['encoding']);
											break;
										default:
											$m['text'] .= $part->getContent();
									}
									
									
								//}
								} catch (Zend_Mail_Exception $e) {
									// ignore
								}
								
								
							}
							
							try {
								IF($m['text'])
								{	
									//return $db->insert('note', array('note'=>$m['text'],'id_company'=>$id_company, 'author'=>$author));
									
									$db->insert('note', array('note'=>$m['text'],'id_company'=>$id_company, 'author'=>$author));
									
									$id_note = $db->lastInsertId();
									
									//Za ATTACHMENTS
									foreach (new RecursiveIteratorIterator($message) as $part) {
										$attachments = array();
										
										if($part->headerExists("content-disposition")){
											if(strtok($part->contentDisposition, ';') == 'attachment'){
												//Get the attacment file name
												$fileName = strtok('=');
												$fileName = strtok('"');
												str_replace("\"", "", $fileName);
												$fileType = strtok($part->contentType, ';');
												$fileContent = base64_decode($part->getContent());
												$fileSize = strlen($fileContent);
												//Get the attachement and decode
												$attachments[] = array("file_name" => $fileName,
														"file_type" => $fileType,
														"file_content" => $fileContent,
														"file_size" => $fileSize);
											}
										}
										
										//Zend_Debug::dump($attachments);
										if(isset($attachments['0']))
										{
											$filename = $attachments['0']['file_name'];
											$content = $attachments['0']['file_content'];
										} else {
											$filename = '';
											$content = '';
										}
										
										
										if($filename) {
												
											$dbconf = $db->getConfig();
										
											$path =  '/notes/'.$dbconf['dbname'].'/files/'.$id_note.'/'.$filename;
												
											//create a new file and write the attachement to the file
											$fh = fopen($path,'w') or die("can't open file");
											fwrite($fh,$content);
											fclose($fh);
		
											$modelFile = new Model_File();
											//ze tip na fajla razbiva imeto po to4ka i vzima posledniq element. Do kolko 6te sraboti ...
											$modelFile->add(array('id_note'=>$id_note, 'file'=>$path,'type'=>end(explode('.', $filename))));
										}
									} //end attachments
								}
							} catch (Exception $e)
							{
								Zend_Debug::dump($e->getMessage());
							}
						} //check
					} //matches
				}
			}
			
		}
		catch (Exception $e)
		{	
			return array();
		}
		}
		return array();
	}
	
	public function deleteNote($id)
	{
		$table = $this->getTable();
		$db = $table->getAdapter();
		$fileModel = new Model_File();
		
		$selectChildNotes = $db->select()->from('note','id')->where('pid = ?', $id);
		$children = $db->fetchAll($selectChildNotes);
		if($children)
		{
			foreach($children as $child)
			{
				$fileModel->deleteByNote($child['id']);
				$where = $db->quoteInto('id = ?', $child['id']);
				$db->delete('note',$where);
			}
		}
		
		$fileModel->deleteByNote($id);
		$where = $db->quoteInto('id = ?', $id);
		return $db->delete('note',$where);
		
	}
	
	public  function send($data)
	{
		try {
			$mail = new Zend_Mail('utf-8');
  			$settings = array('ssl'=>'ssl',
  					'port'=>465,
  					'auth' => 'login',
  					'username' => 'pacorabanpaco.petrov@gmail.com',
  					'password' => 'hppaviliondv5');
  			
  			//$dbConfig = Zend_Registry::get('config');
  			
  			//Zend_Debug::dump($dbConfig->email->address);die;
  			//$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/'.$cookie['c'].'.ini');
  			
  			$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $settings);
  			$mail->setDefaultTransport($transport);
  			//$mail->setBodyText('My Nice Test Text'); //за обикновен текст
	  		$mail->setBodyHtml($data['content'], 'utf-8', 'windows-1251');
	  		$mail->setFrom($data['mailFrom']);
	  		$mail->addTo($data['mailTo']);
	  		$mail->setSubject($data['subject']);
	  		
	  		try {
	  			return $mail->send();
	  		}catch(Exception $e)
	  		{
	  			Zend_Debug::dump($e->getMessage());
	  		}
	  			
		} catch (Exception $e){
	
		}
	}
	
	public function searchNotes($search_word)
	{
		$search_word = str_replace('"', "", $search_word);
		$search_word = str_replace("'", "", $search_word);
		
		$db = $this->getTable()->getAdapter();
		
		$select = $db->select()->from(array('n' => 'note'), array('n.id', 'n.pid', 'n.note', 'n.when', 'n.cdate'))
			->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
			->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
			->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email'))
			->where('n.note like \'%'.$search_word.'%\'', '');
			
		$notesCommentsEmail = $db->query($select)->fetchAll();
		
		if($notesCommentsEmail)
		{
			foreach ($notesCommentsEmail as $key => $value)
			{
				$select = $db->select()->from(array('f' => 'file'), array('file', 'type'))
					->where('f.id_note = ?', $value['id']);
				
				$notesCommentsEmail[$key]['files'] = $db->query($select)->fetchAll();
			}
			
			return $notesCommentsEmail;
		} else {
			return false;
		}
	}
	
	public function showNotesByTag($tag)
	{
		$tag = str_replace('"', "", $tag);
		$tag = str_replace("'", "", $tag);
		
		$db = $this->getTable()->getAdapter();
		
		$modelPerson = new Model_Person();
		$contacts = $modelPerson->fetchContagtsByTag($tag);
		
		if($contacts)
		{
			$modelCompany = new Model_Company();
			$notesArray = array();
			
			foreach ($contacts as $k => $contact)
			{
				if($contact['type'] == 1)
				{
					$select = $db->select()->from(array('n' => 'note'), array('n.id', 'n.pid', 'n.note', 'n.when', 'n.cdate'))
						->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
						->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
						->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email'))
						->where('n.id_person = ?', $contact['id']);
						
					$personNotes = $db->query($select)->fetchAll();
					
					if($personNotes)
					{
						foreach ($personNotes as $key => $value)
						{
							$select = $db->select()->from(array('f' => 'file'), array('file', 'type'))
								->where('f.id_note = ?', $value['id']);
							
							$personNotes[$key]['files'] = $db->query($select)->fetchAll();
						}
						
						$notesArray[] = $personNotes;
					}
				} else {
					$select = $db->select()->from(array('n' => 'note'), array('n.id', 'n.pid', 'n.note', 'n.when', 'n.cdate'))
						->joinLeft(array('per'=>'person'),'per.id = n.id_person', array('id_person'=>'id','personfirstname'=>'firstname','personlastname'=>'lastname','title'))
						->join(array('p'=>'profile'),'p.id = n.author',array( 'id_profile'=>'id','profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
						->joinLeft(array('c'=>'company'),'c.id = n.id_company',array('id_company'=>'id','company'=>'name','cemail'=>'email'))
						->where('n.id_company = ?', $contact['id']);
						
					$companyNotes = $db->query($select)->fetchAll();
					
					if($companyNotes)
					{
						foreach ($companyNotes as $key => $value)
						{
							$select = $db->select()->from(array('f' => 'file'), array('file', 'type'))
								->where('f.id_note = ?', $value['id']);
							
							$companyNotes[$key]['files'] = $db->query($select)->fetchAll();
						}
						
						$notesArray[] = $companyNotes;
					} 
				}
				
				
				
			}
			//Zend_Debug::dump($notesArray);die;
			return $notesArray;
		}
	}
}