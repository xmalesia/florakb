<?php

class insertonebyone extends Database {	
	
    /**
     * @todo insert a record to database
     * 
     * @param $table = table name
     * @param $data = array data to insert
     * @param $db2 = boolean using second database or not
     * 
     * @return lastid = id of inserted data
     * @return status = boolean status of data
     * 
     * */
	function insertData($table=false, $data=array(), $db2=false)
	{
        global $CONFIG, $basedomain;
		if (!$table and empty($data)) return false;

		$return = array();
		$return['status'] = false;
		
		foreach ($data as $key=>$val){
            if(!empty($val)){
                
                $sanitize = str_replace(array('\'', '"'), '', $val);
                
                $tmpfield[] = "`$key`";
                $tmpvalue[] = "'{$sanitize}'";
            }
		}
		
		$field = implode (',',$tmpfield);
		$value = implode (',',$tmpvalue);
		
		$sql = "INSERT INTO {$table} ({$field}) VALUES ({$value})";
        
        if($db2){
            $res = $this->query($sql,1);
        }else{
            $res = $this->query($sql);
        }
        
		if ($res){
			$return['lastid'] = $this->insert_id();
			$return['status'] = true;
		}
		return $return;
	}
    
    /**
     * @todo insert data transaction
     * 
     * @param $table = table name
     * @param $data = array data to insert
     * @param $db2 = boolean using second database or not
     * 
     * @return $insert = status and last id of inserted data
     * 
     * */
    function insertTransaction($table=false, $data=array()){
        
        global $CONFIG, $basedomain;
        
        if (!$table and empty($data)) return false;
        
        $startTransaction = $this->begin();
		if (!$startTransaction) return false;
        
		logFile('====TRANSACTION READY====');
        if($table == 'person'){
            $username = $data['username'];
            unset($data['username']);
        }
        
	    $insert = $this->insertData($table,$data);
        
		if ($insert['status'] == 0){
			$this->rollback();
			logFile('====ROLLBACK TRANSACTION====');
			$return['status'] = false;
		}else{
		  
            // if table person, insert generated password
            if($table == 'person'){
                $salt = $CONFIG['default']['salt'];
                
                //this is the generated password
                $genPass = $this->generate_pass();
                
                //this is the encrypted password
                $password = sha1($genPass.$salt);
                
                //insert password id, password, salt
                $dataPass = array('id' => $insert['lastid'], 'password' => $password, 'salt' => $salt, 'username' => $username);
                $insert_dataPas = $this->insertData('florakb_person',$dataPass,true);
                
                if ($insert_dataPas['status'] == 0){
        			$this->rollback();
        			logFile('====onebyone: failed insert to florakb_person====');
        			$return['status'] = false;
        		}else{
        			$return['status'] = true;
                    $return['lastid'] = $insert['lastid'];
                    
                    /* EMAIL */
                    // send mail before activate account
                    $dataArr['email'] = $data['email'];
                    $dataArr['username'] = $username;
                    $dataArr['token'] = sha1('register'.$data['email']);
                    $dataArr['validby'] = sha1(CODEKIR);
        
                    $inflatData = encode(serialize($dataArr));
                    logFile($inflatData);
        
        
                    $to = $data['email'];
                    $from = $CONFIG['email']['EMAIL_FROM_DEFAULT'];
                    $msg = "To activate your account please <a href='{$basedomain}login/validate/?ref={$inflatData}'>click here</a>";
                    // try to send mail 
                    $sendMail = sendGlobalMail($to, $from, $msg,false);
                    logFile('mail send '.serialize($sendMail));
        
                    if ($sendMail['result']){
        
                        $this->commit();
                        logFile('==onebyone: success create user==');
                        return true;
                        exit;
                    }else{
                        $this->commit();
                        logFile('====onebyone: failed sending email====');
                    }
                    
                    /* EMAIL */
        		}
            }else{
                $this->commit();
    			logFile('====onebyone: failed create user====');
    			$return['status'] = true;
                $return['lastid'] = $insert['lastid'];
            }
		}
        
        return $return;
		exit;
    }
    
    /**
     * @todo generate a random password
     * 
     * @param $length = length of character
     * 
     * @return $result = random character
     * 
     * */
    function generate_pass($length = 8){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);
    
        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }
    
        return $result;
    }
    
    /**
     * @todo get list location
     * 
     * @return sql result
     * 
     * */
    function list_locn(){
        $sql = "SELECT id, locality FROM locn GROUP BY locality ORDER BY locality";
		$res = $this->fetch($sql,1);
        return $res;
    }
    
    /**
     * @todo get list person
     * 
     * @return sql result
     * 
     * */
    function list_person(){
        $sql = "SELECT * FROM person";
		$res = $this->fetch($sql,1);
        return $res;
    }
    
    /**
     * @todo get list taxon
     * 
     * @return sql result
     * 
     * */
    function list_taxon(){
        $sql = "SELECT * FROM taxon GROUP BY fam, gen, sp, morphotype ORDER BY fam, gen, sp, morphotype";
		$res = $this->fetch($sql,1);
        return $res;
    }
    
    /**
     * @todo get list for auto taxon
     * 
     * @return sql result
     * 
     * */
    /*function list_autoTaxon($like){
        $sql = "SELECT * FROM taxon WHERE (fam LIKE '%$like%' OR gen LIKE 
'%$like%' OR sp LIKE '%$like%' OR morphotype LIKE '%$like%') GROUP BY fam, gen, sp, morphotype ORDER BY fam, gen, sp, morphotype";
		$res = $this->fetch($sql,1);
        return $res;
    }*/
    
    /**
     * @todo get list for auto taxon from plantlist table
     * 
     * @return sql result
     * 
     * */
    function list_autoTaxon($like){
        $sql = "SELECT * FROM (SELECT *, CONCAT('(',family,')',' ',genus,' ',species) as taxonName FROM plantlist) base WHERE taxonName LIKE '%$like%';";
		$res = $this->fetch($sql,1);
        return $res;
    }
    
    /**
     * @todo get enum of confid field in table det
     * @param $table = table name
     * @param $field = field name
     * 
     * @return preg match of sql result
     * 
     * */
    function get_enum($table, $field){
        $sql = "SHOW COLUMNS FROM `$table` WHERE Field = '$field'";
		$res = $this->fetch($sql,0);
        
        preg_match('/^enum\((.*)\)$/', $res['Type'], $matches);
        foreach( explode(',', $matches[1]) as $value )
        {
             $enum[] = trim( $value, "'" );
        }
        return $enum;
    }
    
    /**
     * @todo start sql transaction
     * 
     * @return boolean true/false
     * 
     * */
    function startTransaction()
	{
		$startTransaction = $this->begin();
		if (!$startTransaction) return false;
		logFile('====TRANSACTION READY====');
		return true;
	}
	
    /**
     * @todo rollback sql transaction
     * 
     * @return boolean true/false
     * 
     * */
	function rollbackTransaction()
	{
		$this->rollback();
		logFile('====ROLLBACK TRANSACTION====');
		return true;
	}
	
    /**
     * @todo commit sql transaction
     * 
     * @return boolean true/false
     * 
     * */
	function commitTransaction()
	{
		$this->commit();
		logFile('====COMMIT TRANSACTION====');
		return true;
	}
}

?>