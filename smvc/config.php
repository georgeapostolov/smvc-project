<?

    session_start();
	mb_internal_encoding('utf8');

    const DS = DIRECTORY_SEPARATOR;
	define('ROOT_DIR', dirname(__FILE__) . DS);
    const PUBLIC_PATH = 'G:\xampp\htdocs\public_html' . DS;

    const CSSJS_REV = 'cssjs_revision_1';

    const CONTROLLER_DIR = ROOT_DIR . 'controller' . DS;
    const VIEW_DIR = ROOT_DIR . 'view' . DS;

	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
		$protocol = 'https://';
	}
	else {
		$protocol = 'http://';
	}

	define('ROOT_URL',			$protocol . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . '/');
    const STATIC_URL = '';
	define('CURRENT_URL',		$protocol . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''));

    const DEFAULT_ADMINPREFIX = 'admin';
    const DEFAULT_EXTENSION = '.html';

    const DB_HOST = 'localhost';
    const DB_USER = 'smvc_user';
    const DB_PASS = 'smvc_password';
    const DB_NAME = 'smvc_database';

	$LOCAL_IPS = array('127.0.0.1', '::1', '192.168.0.4');

	$IS_LOCAL_IP = (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $LOCAL_IPS));
	if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $LOCAL_IPS)) {
		if ($_SERVER['REMOTE_ADDR'] == '::1') { // hotfix for local webservers
			$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		}
		error_reporting(E_ALL);
	}
	else {
		error_reporting(0);
	}

	function print_pre($data, $var_dump = false) {
		global $LOCAL_IPS;
		if (in_array($_SERVER['REMOTE_ADDR'], $LOCAL_IPS)) {
			echo '<pre>';
			if ($var_dump) {
				var_dump($data);
			} else {
				print_r($data);
			}
			echo '</pre>';
		}
	}

	spl_autoload_register(function($class_name) {
		$array_path = explode('_', $class_name);
		$file = array_pop($array_path);
		$path = ROOT_DIR . implode(DS, $array_path) . DS;
		$file = mb_strtolower($path . $file . '.php');
		if (file_exists($file)) {
			require($file);
		}
		return true;
	});
