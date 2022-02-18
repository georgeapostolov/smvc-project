
	var $window = $(window);
	var hash_config_shown = '';
	function hash_handler() {
	var hash_config = {
	function hash_toggle(which) {

	function hash_remove() {
		location.hash = "#";
		$window.scrollTop(hash_body_last_scroll);
	};

	$(document).ready(function(){

		if (window.location.hash && window.location.hash != '#') {
			var tmphsh = window.location.hash.replace(/^\#/, '');
			if (hash_config[tmphsh]) {
				window.location.hash = '#'; // Dont open menu on page load, even if hash is available
			}
		}
		$window.bind('hashchange', hash_handler);

		$('#header_menu_button, #menu_header_close_button').click(function(){
			hash_toggle("mainmenu");
		});
	});