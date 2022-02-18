
	var $window = $(window);
	var hash_config_shown = '';	var hash_body_last_scroll = 0;
	function hash_handler() {		hash = location.hash.replace(/^\#/, '');		if (hash_config_shown == hash) {			return;		}		if (hash_config_shown) {			hash_config[hash_config_shown].hide();			hash_config_shown = '';		}		if (hash && hash_config[hash]) {			hash_config[hash].show();			hash_config_shown = hash;		}	};
	var hash_config = {		mainmenu: {			show: function(){				$('#menu_wrapper').scrollTop(0);				$('#menu, #block').show();				$('#page').hide();				setTimeout(function(){					if ($('#menu').is(':visible')) {						$('body').css('overflow', 'hidden');					}				}, 1000);			},			hide: function(){				$('#menu, #block').hide();				$('#header .activated').removeClass('activated');				setTimeout(function(){					$('#page').show();					$('body').css('overflow', 'scroll');					$('#menu .activated').removeClass('activated');				}, 1);			}		}	};
	function hash_toggle(which) {		if (hash_config_shown == which) {			hash_remove();		}		else {			hash_body_last_scroll = $window.scrollTop();			location.hash = "#"+which;		}	};

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