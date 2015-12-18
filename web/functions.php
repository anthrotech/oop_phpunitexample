<?php

class SMS {
    public $db_host = '127.0.0.1';
    public $db_user = 'root';
    public $db_pass = '';
    public $db_database = 'samtt';

    public function get_auth_token() {
       // Creates Authorization Token from the Binary RegisterMo Binary
       $arg = json_encode($_REQUEST);
       return `../web/registermo $arg`;
    }
    
    public function save($msisdn,$operatorid,$shortcodeid,$text,$date,$token) {
    	try {
    		// Connect to Database using PDO
    		$db = new PDO("mysql:host=$this->db_host;dbname=$this->db_database", $this->db_user, $this->db_pass);
    		
    		// Define Parameters
    		$date        = date('Y-m-d H:i:s');
    		
    		// Turn off Emulation
    		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    		
      		// Being Transaction
      		$db->beginTransaction();
      			      			
      		// Prepare Insert Statement
      		$insert = $db->prepare("INSERT INTO mo (msisdn,operatorid,shortcodeid,text,auth_token,created_at) VALUES (:msisdn,:operatorid,:shortcodeid,:text,:auth_token,:created_at)");

      		// Bind Parameters
      		$insert->bindParam('msisdn',$msisdn);
      		$insert->bindParam('operatorid',$operatorid);
      		$insert->bindParam('shortcodeid',$shortcodeid);
      		$insert->bindParam('text',$text);
      		$insert->bindParam('auth_token',$token);
      		$insert->bindParam('created_at',$date);
      			
      		// Execute Insert Statement
      		$insert->execute();
      			
      		// Commit Transaction
      		$db->commit();
      			
      		// Print Success Statement
      		//echo '{"status": "ok"}';
      			
      		// Close Database Connection
      		$db =  null;    			
    	}
    	catch (PDOException $e) {
    		// Rollback
    		$db->rollback();
    		
    		// Print Error Message
    		echo '{"status": "'.$e->getMessage().'"}';    		
    	}
    }
    
    public function stats() {
    	try {
    		
    		// Set-up Query Variables
    		$response = array();    		
    		
    		// Connect to Database using PDO
    		$db = new PDO("mysql:host=$this->db_host;dbname=$this->db_database", $this->db_user, $this->db_pass);
    		
    		// Being Transaction
    		$db->beginTransaction();    		
    	
    		// Prepare Count Select Statement
    		$stats_count = $db->prepare("SELECT count(*) as count from mo where created_at >= DATE_SUB(NOW(),INTERVAL 15 MINUTE)");
    			 
    		// Execute Insert Statement
    		$stats_count->execute();
    			 
    		// Get the Count
    		while ($stats_count_row = $stats_count->fetch()) {
    			$response['last_15_min_mo_count'] = $stats_count_row['count'];
    		}

    		// Prepare Last 10,000 Records Select Statement
    		$stats_last_set = $db->prepare("SELECT min(created_at) as min, max(created_at) as max from mo order by id DESC limit 10000");
    		
    		// Execute Insert Statement
    		$stats_last_set->execute();
    		
    		// Get the Last 10,000 Records
    		while ($stats_last_set_row = $stats_last_set->fetch()) {
    			$response['time_span_last_10k'] = array($stats_last_set_row['min'],$stats_last_set_row['max']);
    		}   
    		
    		// Commit Transaction
    		$db->commit();
    		
    		// Print Success Statement
    		//echo '{"status": "ok"}';    		
    		    		
    		// Close Database Connection
    		$db =  null;    				
    			 
			// Return Data
			return $response;	 
    	}
    	catch (PDOException $e) {
    		// Rollback
    		$db->rollback();
    	
    		// Print Error Message
    		echo '{"status": "'.$e->getMessage().'"}';
    	}    	
    }

    public function unprocessed() {
    	try {
    
    		// Set-up Query Variables
    		$response = array();
    
    		// Connect to Database using PDO
    		$db = new PDO("mysql:host=$this->db_host;dbname=$this->db_database", $this->db_user, $this->db_pass);
    
    		// Being Transaction
    		$db->beginTransaction();
    		 
    		// Prepare Count Select Statement
    		$stats_count = $db->prepare("SELECT count(*) as count from mo where auth_token IS NULL");
    
    		// Execute Insert Statement
    		$stats_count->execute();
    
    		// Get the Count
    		while ($stats_count_row = $stats_count->fetch()) {
    			$response['unprocessed_mo'] = $stats_count_row['count'];
    		}
    
    		// Commit Transaction
    		$db->commit();
    
    		// Print Success Statement
    		//echo '{"status": "ok"}';
    
    		// Close Database Connection
    		$db =  null;
    
    		// Return Data
    		return $response;
    	}
    	catch (PDOException $e) {
    		// Rollback
    		$db->rollback();
    		 
    		// Print Error Message
    		echo '{"status": "'.$e->getMessage().'"}';
    	}
    }  
    
    public function unprocessed_remove() {
    	try {
    
    		// Set-up Query Variables
    		$response = array();
    
    		// Connect to Database using PDO
    		$db = new PDO("mysql:host=$this->db_host;dbname=$this->db_database", $this->db_user, $this->db_pass);
    
    		// Being Transaction
    		$db->beginTransaction();
    		 
    		// Prepare Count Select Statement
    		$stats_count = $db->prepare("SELECT count(*) as count from mo where auth_token IS NULL");
    
    		// Execute Insert Statement
    		$stats_count->execute();
    
    		// Get the Count
    		while ($stats_count_row = $stats_count->fetch()) {
    			$response['unprocessed_mo'] = $stats_count_row['count'];
    		}
    		
    		// Prepare Count Select Statement
    		$stats_delete = $db->prepare("DELETE from mo where auth_token IS NULL");
    		
    		// Execute Insert Statement
    		$stats_delete->execute();    

    		// How many rows deleted
    		$response['deleted_mo'] = $stats_delete->rowCount();
    
    		// Commit Transaction
    		$db->commit();
    
    		// Print Success Statement
    		//echo '{"status": "ok"}';
    
    		// Close Database Connection
    		$db =  null;
    
    		// Return Data
    		return $response;
    	}
    	catch (PDOException $e) {
    		// Rollback
    		$db->rollback();
    		 
    		// Print Error Message
    		echo '{"status": "'.$e->getMessage().'"}';
    	}
    }     
}
