<?

	class controller_admin_logout extends base_controller {

		public function __construct() {
			parent::__construct();
		}

		public function index() {
			session_destroy();
			header('Location: ' . ROOT_URL . 'admin/login');
			exit;
		}

	}
