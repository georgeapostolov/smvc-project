<?

	class base_view {

		protected $_vars = [];
		public $view = null;
		public $css = [];
		public $js = [];
		public $user;
		public $no_mobile = false;

		public function __construct() {
			$this->view = $this;
			$this->user = new base_user();
		}

		public function set($name, $value) {
			$this->_vars[$name] = $value;
			return true;
		}

		public function set_css($file) {
			$this->css[] = $file;
			return true;
		}

		public function set_js($file) {
			$this->js[] = $file;
			return true;
		}

		public function get($name) {
			return (isset($this->_vars[$name]) ? $this->_vars[$name] : null);
		}

		public function get_css() {
			return $this->css;
		}
		public function get_js() {
			return $this->js;
		}

		public function check_cssjs_is_internal_file($file) {
			if (substr($file, 0, 1) == '/') {
				return false;
			}
			if (substr($file, 0, 4) == 'http') {
				return false;
			}
			return true;
		}

		public function display($file, $echo = true) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$device = 'desktop';
			if (preg_match('@Windows|Macintosh@isu', $user_agent)) {
				$device = 'desktop';
			} elseif (preg_match('@Android|iPhone@isu', $user_agent)) {
				$device = 'mobile';
			}
			if (true == $this->no_mobile) {
				$device = 'desktop';
			}
			$file_path = VIEW_DIR . base_router::$module . DS . $device . DS . $file . '.phtml';
			if (file_exists($file_path)) {
				extract($this->_vars);
				ob_start();
				require($file_path);
			}
			else {
				if ($device == 'mobile') {
					$device = 'desktop';
					$file_path = VIEW_DIR . base_router::$module . DS . $device . DS . $file . '.phtml';
					if (file_exists($file_path)) {
						extract($this->_vars);
						ob_start();
						require($file_path);
					}
				} else {
					throw new base_exception('Template file not found: ' . $file_path);
					return false;
				}
			}
			$content = ob_get_contents();
			ob_end_clean();
			if ($echo) {
				echo $content;
				return true;
			}
			else {
				return $content;
			}
		}

	}
