<?php

	/*
	Revised code by Dominick Lee
	Original code derived from "Essential PHP Security" by Chriss Shiflett
	Last Modified 2/27/2017


	CREATE TABLE sessions
	(
		id varchar(32) NOT NULL,
		access int(10) unsigned,
		data text,
		PRIMARY KEY (id)
	);

	+--------+------------------+------+-----+---------+-------+
	| Field  | Type             | Null | Key | Default | Extra |
	+--------+------------------+------+-----+---------+-------+
	| id     | varchar(32)      |      | PRI |         |       |
	| access | int(10) unsigned | YES  |     | NULL    |       |
	| data   | text             | YES  |     | NULL    |       |
	+--------+------------------+------+-----+---------+-------+

	*/

class Session {
	var $database = 'session';
	var $username = 'session';
	var $password = '';
	var $hostname = 'localhost';
	var $options = [];
	var $debug = false;

	private $db;

	public function __construct(){

		// Instantiate new Database object
		try {
			$this->db = new PDO ("mysql:host=".$this->hostname.";dbname=".$this->database,$this->username,$this->password,$this->options);
		}
		catch(PDOException $e){
			if ($this->debug) printf("Error '%s' opening mysql database:%s on %s",$e->getMessage(),$this->database,$this->hostname);
		}

		// Set handler to overide SESSION
		session_set_save_handler(
		array($this, "_open"),
		array($this, "_close"),
		array($this, "_read"),
		array($this, "_write"),
		array($this, "_destroy"),
		array($this, "_gc")
		);

		// Start the session
		session_start();
	}
	public function _open(){
		return isset($this->db);
	}
	public function _close(){
		$this->db = null;
		return true;
	}
	public function _read($id){
		$st = $this->db->prepare('SELECT data FROM sessions WHERE id = ?');
		if ($st->execute([$id])){
			if ($row = $st->fetchColumn()) {
				return $row; 
			} else return '';
		} else {
			if ($this->debug) echo "Session Execute Read Error";
			return '';
		}
	}
	public function _write($id, $data){
		$access = time();
		if ($st = $this->db->prepare('REPLACE INTO sessions (id, access, data) VALUES (?,?,?)')) {
			if($st->execute([$id,$access,$data])){
				return true;
			}
		} 
		if ($this->debug) echo "Session Write Error";
		return false;
	}
	public function _destroy($id){
		$st = $this->db->prepare('DELETE FROM sessions WHERE id = ?');
		if ($st->execute([$id])) return true;
		if ($this->debug) echo "Session Destroy Failed";
		return false;
	} 
	public function _gc($max){
		$old = time() - $max;
		$st = $this->db->query('DELETE FROM sessions WHERE access < ?');
		if ($st->execute([$old])) return true; 
		if ($this->debug) echo "Session Garbage Collection Failed";
		return false;
	}
}
?>
