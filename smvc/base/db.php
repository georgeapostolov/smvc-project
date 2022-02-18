<?

	class base_db {

		protected $_db;
		protected $_connected = false;
		protected $_query;
		protected $_result;

		public function __construct() {
			if ($this->_connected) {
				return;
			}
			$this->_db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if (mysqli_connect_errno()) {
				throw new base_exception("Failed to connect to database:\n\n" . mysqli_connect_error());
			}
			if (!$this->_db->set_charset('utf8')) {
				throw new base_exception("Error loading character set utf8:\n\n" . $this->_db->error);
			}
			$this->_connected = true;
		}

		public function __destruct() {}

		public function close() {
			$this->_db->close();
			$this->_connected = false;
		}

		public function query($query) {
			$result = $this->_db->query($query);
			if ($this->_db->errno) {
				throw new base_exception("Could not execute query:\n\n" . $query . "\n\n" . $this->_db->error, $this->_db->errno);
			}
			$this->_result = $result;
			return $this->_result;
		}

		public function fetch() {
			return $this->_result->fetch_assoc();
		}

		public function fetch_field($query, $field = '') {
			$res = $this->query($query);
			$row = $this->fetch();
			if (false == $res) {
				return false;
			}
			return (isset($row[$field]) ? $row[$field] : false);
		}

		public function affected_rows() {
			return $this->_db->affected_rows;
		}

		public function escape($data) {
			if (is_array($data) && !empty($data)) {
				foreach ($data as $key => $value) {
					$data[$key] = $this->_db->real_escape_string($value);
				}
			}
			else {
				$data = $this->_db->real_escape_string($data);
			}
			return $data;
		}

		public function insert_id() {
			return $this->_db->insert_id;
		}

	}
