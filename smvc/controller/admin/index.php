<?
	class controller_admin_index extends base_controller {
		public function __construct() {
			parent::__construct();
		}
		public function index() {
			$this->view->set('title', 'admin index');
			$this->view->display('index/index');
		}
	}