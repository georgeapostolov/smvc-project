<?	class base_user {		public $id = 0;		public $is_logged = false;		public $data = [];		public function __construct() {			if (isset($_SESSION['user_data'])) {				$this->id = $_SESSION['user_data']['id'];				$this->is_logged = true;				$this->data = $_SESSION['user_data'];			}		}	}