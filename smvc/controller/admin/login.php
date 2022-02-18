<?
	class controller_admin_login extends base_controller {
		public function __construct() {
			parent::__construct();
		}
		public function index() {
			$result = array();
			if (!empty($_POST) && isset($_POST['action']) && $_POST['action'] == 'login') {
				$user = new model_public_user();
				$username = (isset($_POST['username']) && $_POST['username'] ? trim($_POST['username']) : '');
				$password = (isset($_POST['password']) && $_POST['password'] ? trim($_POST['password']) : '');
				$result = $user->login($username, $password);
				if ($result['success'] == 1) {
					header('Location: ' . ROOT_URL . 'admin');
				} else {
					if (isset($_SESSION['2fa_enabled'])) {
						header('Location: ' . ROOT_URL . 'admin/login/tfa');
						return;
					}					
				}
			}
			$this->view->set('result', $result);
			$this->view->set('title', 'admin login index');
			$this->view->display('login/index');
		}

		public function tfa() {			
			$result = [];			
			if (!empty($_POST) && isset($_POST['action']) && $_POST['action'] == '2fa') {				
				$user = new model_public_user();
				$username = (isset($_SESSION['2fa_enabled']) && $_SESSION['2fa_enabled'] ? $_SESSION['2fa_enabled'] : '');
				$tfa_code = (isset($_POST['2fa']) && $_POST['2fa'] ? trim($_POST['2fa']) : '');				
				$result = $user->login($username, $tfa_code);
				if ($result['success'] == 1) {
					header('Location: ' . ROOT_URL . 'admin');
				}				
			}			
			$this->view->set('result', $result);
			$this->view->set('title', 'admin login two factor');
			$this->view->display('login/tfa');			
		}
	}