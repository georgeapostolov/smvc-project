<?

	class base_exception extends exception {

		public function __construct($message = '', $code = 0) {
			parent::__construct($message, $code);
			$user = new base_user();
 			$error = "Runtime Error [" . $this->getCode() . "]:\n" . $this->getMessage() . "\n\nFile: " . $this->getFile() . "\n\nLine: " . $this->getLine()."\n\nStack:\n".$this->getTraceAsString() . "\n";
			$error_details = array(
				'code'			=> $code,
				'message'		=> $error,
				'insert_date'	=> date("Y-m-d H:i:s"),
				'user_id'		=> $user->id,
				'ip_address'	=> (isset($_SERVER['REMOTE_ADDR']) ? ip2long($_SERVER['REMOTE_ADDR']) : 0),
				'browser'		=> (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''),
				'referer'		=> (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),
				'request_uri'	=> (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
			);
			$save_to_db = true;
			if ($save_to_db) {
				try {
					$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
					if (!mysqli_connect_errno()) {
						if ($db->set_charset('utf8')) {
							$db->query('
								INSERT INTO `mysql_errors` SET
									`code` = "' . $db->escape_string($error_details['code']) . '",
									`message` = "' . $db->escape_string($error_details['message']) . '",
									`insert_date` = UNIX_TIMESTAMP(),
									`user_id` = "' . $db->escape_string($error_details['user_id']) . '",
									`ip_address` = "' . $db->escape_string($error_details['ip_address']) . '",
									`browser` = "' . $db->escape_string($error_details['browser']) . '",
									`referer` = "' . $db->escape_string($error_details['referer']) . '",
									`request_uri` = "' . $db->escape_string($error_details['request_uri']) . '"
							');
						}
					}
				} catch (Exception $e) {
				}
			}
			global $IS_LOCAL_IP;
			if ($IS_LOCAL_IP) {
				echo '<pre>' . print_r($error_details, 1) . '</pre>';
			}
			else {
				header('Location: ' . ROOT_URL . 'error');
			}
			exit;
		}

	}
