<?php
	class Connection {
		private $host;
		private $port;
		private $db;
		private $username;
		private $dsn;
		private $password;
		private $connection;
		
		public function __construct() {
			$this->host = getenv("DB_HOST");
			$this->port = getenv("DB_PORT");
			$this->db = getenv("DB_NAME");
			$this->dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->db";
			$this->username = getenv("DB_USERNAME");
			$this->password = getenv("DB_PASSWORD");

			try {
				$this->connection = new PDO($this->dsn, $this->username, $this->password);
			} catch (PDOException $e) {
				print($e->getMessage() . "\n");
			}
		}

		public function getConnection() {
			return $this->connection;
		}
	}
?>