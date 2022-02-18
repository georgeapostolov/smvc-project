<?

	class model_public_server extends base_model {

		public function __construct() {
			parent::__construct();
		}

		public function parse_data($file = '') {

			$parser = new model_public_lua();

			try {

				$parser->parseFile($file);
				$data = $parser->data;

				if ($data) {
					$wow = new model_public_wow();
					$factions_data = ['Alliance' => 469, 'Horde' => 67];
					$races_data = ['Human' => 1, 'Orc' => 2, 'Dwarf' => 3, 'Night Elf' => 4, 'Undead' => 5, 'Tauren' => 6, 'Gnome' => 7, 'Troll' => 8, 'Goblin' => 9, 'Blood Elf' => 10, 'Draenei' => 11, 'Worgen' => 22];
					$classes_data = ['Warrior' => 1, 'Paladin' => 2, 'Hunter' => 3, 'Rogue' => 4, 'Priest' => 5, 'Death Knight' => 6, 'Shaman' => 7, 'Mage' => 8, 'Warlock' => 9, 'Druid' => 10];
					foreach ($data['CensusPlus_Database']['Servers']['Blackwing Lair'] as $faction => $races) {
						foreach ($races as $race => $classes) {
							foreach ($classes as $class => $characters) {
								foreach ($characters as $character_name => $character_data) {
									$guild_id = $this->get_guild_id($factions_data[$faction], $character_data[1]);
									$query = '
										INSERT INTO `world_stats_characters` SET
											`faction_id` = "' . $factions_data[$faction] . '",
											`race_id` = "' . $races_data[$race] .'",
											`class_id` = "' . $classes_data[$class] . '",
											`name` = "' . $character_name . '",
											`level` = "' . $character_data[0] . '",
											`guild_id` = "' . $guild_id . '",
											`date_added` = "' . $character_data[2] . '"
										ON DUPLICATE KEY UPDATE
											`level` = "' . $character_data[0] . '",
											`guild_id` = "' . $guild_id . '",
											`date_updated` = "' . $character_data[2] . '"
									';
									$this->db->query($query);
								}
							}
						}
					}
					$query = 'UPDATE `world_stats_guilds` AS `wsg` SET `wsg`.`members` = (SELECT COUNT(`id`) FROM `world_stats_characters` AS `wsc` WHERE `wsc`.`guild_id` = `wsg`.`id`)';
					$this->db->query($query);
					return true;
				}

			}

			catch(Exception $e) {
				print_pre('Exception: ' . $e->getMessage());
				return false;
			}

		}

		public function get_guild_id($faction_id = 0, $guild_name = '') {
			$guild_id = 0;
			if (empty($guild_name)) {
				return $guild_id;
			}
			$query = 'SELECT `id` FROM `world_stats_guilds` WHERE `name` = "' . $this->db->escape($guild_name) . '" LIMIT 1';
			$this->db->query($query);
			$data = $this->db->fetch();
			if ($data) {
				return $data['id'];
			}
			$query = 'INSERT INTO `world_stats_guilds` SET `faction_id` = ' . (int)$faction_id . ', `name` = "' . $this->db->escape($guild_name) . '"';
			$this->db->query($query);
			$guild_id = $this->db->insert_id();
			return $guild_id;
		}

		public function get_guilds() {
			$guilds = [];
			$this->db->query('select * from `world_stats_guilds` order by `members` desc');
			while (false != ($guild = $this->db->fetch())) $guilds[$guild['id']] = $guild;
			return $guilds;
		}

		public static function generate_pages($all_pages, $elements_per_page, $range = 2, $selected_page = 1) {

			$last_page = ceil($all_pages/$elements_per_page);
			if ($selected_page < 1) {
				$selected_page = 1;
			}
			if ($last_page < $selected_page) {
				$selected_page = $last_page;
			}
			$pages_array = array(
				'data' => array(),
				'pages' => array()
			);

			if (5 < $last_page) {

				// calculating range
				$start_range = ($selected_page - floor($range/2));
				$end_range = ($selected_page + floor($range/2));
				if ($start_range <= 0) {
					$end_range += (abs($start_range) + 1);
					$start_range = 1;
				}
				if ($end_range > $last_page) {
					$start_range -= ($end_range - $last_page);
					$EndRange = $last_page;
				}
				$range_array = range($start_range, $end_range);
				// calculating range

				for ($counter = 1; $counter <= $last_page; $counter++) {
					if ($range_array[0] > 2 && $counter == $range_array[0]) {
						$pages_array['pages'][$counter]['index'] = '...';
					}
					elseif (isset($range_array[$range]) && ($range_array[$range] < $last_page && $counter == $range_array[$range])) {
						$pages_array['pages'][$counter]['index'] = '...';
					}
					else {
						if (($counter == 1) || $counter == $last_page || in_array($counter, $range_array)) {
							if ($counter == $selected_page) {
								if (1 < $counter) {
									$pages_array['data']['first']['title'] = 'First';
									$pages_array['data']['first']['index'] = 1;
									$pages_array['data']['previous']['title'] = 'Previous';
									$pages_array['data']['previous']['index'] = ($counter - 1);
								}
								$pages_array['pages'][$counter]['selected'] = 1;
								if ($counter <= ($last_page - 1)) {
									$pages_array['data']['last']['title'] = 'Last';
									$pages_array['data']['last']['index'] = $last_page;
									$pages_array['data']['next']['title'] = 'Next';
									$pages_array['data']['next']['index'] = ($counter + 1);
								}
							}
							$pages_array['pages'][$counter]['title'] = $counter;
							$pages_array['pages'][$counter]['index'] = $counter;
						}
					}
				}

			}
			else {
				for ($counter = 1; $counter <= $last_page; $counter++) {
					if ($counter == $selected_page) {
						if (1 < $counter) {
							$pages_array['data']['previous']['title'] = 'Previous';
							$pages_array['data']['previous']['index'] = ($counter - 1);
						}
						$pages_array['pages'][$counter]['selected'] = 1;
						if ($counter <= ($last_page - 1)) {
							$pages_array['data']['next']['title'] = 'Next';
							$pages_array['data']['next']['index'] = ($counter + 1);
						}
					}
					$pages_array['pages'][$counter]['title'] = $counter;
					$pages_array['pages'][$counter]['index'] = $counter;
				}
			}
			if ($last_page < 100) {
				unset($pages_array['data']['previous']);
				unset($pages_array['data']['next']);
				unset($pages_array['data']['first']);
				unset($pages_array['data']['last']);
			}

			return $pages_array;

		}

		public function get_roster($options = []) {

			$roster = [
				'all_rows'		=> 0,
				'rows_per_page'	=> (isset($options['limit']) ? (int)$options['limit'] : 50),
				'range'			=> 6,
				'num_pages'		=> 0,
				'page'			=> (isset($options['page']) ? (int)$options['page'] : 1),
				'data'			=> [],
				'pagination'	=> [],
			];

			$faction_id = (isset($options['faction_id']) && $options['faction_id'] ? (int)$options['faction_id'] : 0);
			$guild_id = (isset($options['guild_id']) && $options['guild_id'] ? (int)$options['guild_id'] : 0);
			$race_id = (isset($options['race_id']) && $options['race_id'] ? (int)$options['race_id'] : 0);
			$class_id = (isset($options['class_id']) && $options['class_id'] ? (int)$options['class_id'] : 0);

			$query = '
				SELECT
					COUNT(`id`) AS `rows`
				FROM `world_stats_characters` AS `wsc`
				WHERE 1
					' . ($faction_id ? ' AND `wsc`.`faction_id` = ' . $faction_id . ' ' : '') . '
					' . ($guild_id ? ' AND `wsc`.`guild_id` = ' . (-1 == $guild_id ? '""' : $guild_id) . ' ' : '') . '
					' . ($race_id ? ' AND `wsc`.`race_id` = ' . $race_id . ' ' : '') . '
					' . ($class_id ? ' AND `wsc`.`class_id` = ' . $class_id . ' ' : '') . '
			';
			$all_results = (int)$this->db->fetch_field($query, 'rows');

			if ($all_results) {

				$roster['all_rows']		= $all_results;
				$roster['num_pages']	= ceil($all_results / $roster['rows_per_page']);
				$page					= max($roster['page'], 1);
				$page					= min($page, $roster['num_pages']);
				$roster['page']			= $page;

				$roster['pagination']	= $this->generate_pages($roster['all_rows'], $roster['rows_per_page'], $roster['range'], $roster['page']);

				$query = '
					SELECT
						*
					FROM `world_stats_characters` AS `wsc`
					WHERE 1
						' . ($faction_id ? ' AND `wsc`.`faction_id` = ' . $faction_id . ' ' : '') . '
						' . ($guild_id ? ' AND `wsc`.`guild_id` = ' . (-1 == $guild_id ? '""' : $guild_id) . ' ' : '') . '
						' . ($race_id ? ' AND `wsc`.`race_id` = ' . $race_id . ' ' : '') . '
						' . ($class_id ? ' AND `wsc`.`class_id` = ' . $class_id . ' ' : '') . '
					ORDER BY `name` ASC
					LIMIT ' . ($roster['rows_per_page'] * ($roster['page'] - 1)) . ', ' . $roster['rows_per_page'] . '
				';
				$this->db->query($query);
				while (false != ($row = $this->db->fetch())) $roster['data'][$row['id']] = $row;

			}

			return $roster;
		}

		private function hex2rgba($color, $opacity = false) {

			$default = 'rgb(0, 0, 0);';

			//Return default if no color provided
			if (empty($color)) return $default;

			//Sanitize $color if "#" is provided
		        if ($color[0] == '#' ) {
		        	$color = substr( $color, 1 );
		        }

		        //Check if color has 6 or 3 characters and get values
		        if (strlen($color) == 6) {
		                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		        } elseif ( strlen( $color ) == 3 ) {
		                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		        } else {
		                return $default;
		        }

		        //Convert hexadec to rgb
		        $rgb =  array_map('hexdec', $hex);

		        //Check if opacity is set(rgba or rgb)
		        if($opacity){
		        	if(abs($opacity) > 1)
		        		$opacity = 1.0;
		        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		        } else {
		        	$output = 'rgb('.implode(",",$rgb).')';
		        }

		        //Return rgb(a) color string
		        return $output;
		}

		public function generate_ajax_data($options = []) {

			$ajax = [
				'factions' => [
					'labels'	=> [],
					'colors'	=> [],
					'data'		=> [],
					'ids'		=> []
				],
			];

			$wow = new model_public_wow();

			$faction	= (isset($options['faction'])	&& $options['faction']	? (int)$options['faction']	: false);
			$race		= (isset($options['race'])		&& $options['race']		? (int)$options['race']		: false);
			$class		= (isset($options['class'])		&& $options['class']	? (int)$options['class']	: false);

			$query = '
				SELECT
					`wsc`.`faction_id`, `f`.`name` AS `name`, `f`.`color`, COUNT(`wsc`.`id`) as `characters`
				FROM `world_stats_characters` AS `wsc`
				LEFT JOIN `factions` AS `f` ON `f`.`id` = `wsc`.`faction_id`
				WHERE 1
					#' . ($faction ? 'AND `wsc`.`faction_id` = ' . $faction . ' ' : '') . '
					' . ($race ? 'AND `wsc`.`race_id` = ' . $race . ' ' : '') . '
					' . ($class ? 'AND `wsc`.`class_id` = ' . $class . ' ' : '') . '
				GROUP BY `wsc`.`faction_id`
			';
			$this->db->query($query);
			while (false != ($fd = $this->db->fetch())) {
				$ajax['factions']['labels'][]	= $fd['name'];
				$ajax['factions']['colors'][]	= $this->hex2rgba($fd['color'], 0.6);
				$ajax['factions']['data'][]		= $fd['characters'];
				$ajax['factions']['ids'][]		= $fd['faction_id'];
			}

			$query = '
				SELECT
					`wsc`.`race_id`, `r`.`name` AS `name`, `r`.`color`, COUNT(`wsc`.`id`) as `characters`
				FROM `world_stats_characters` AS `wsc`
				LEFT JOIN `races` AS `r` ON `r`.`id` = `wsc`.`race_id`
				WHERE 1
					' . ($faction ? 'AND `wsc`.`faction_id` = ' . $faction . ' ' : '') . '
					#' . ($race ? 'AND `wsc`.`race_id` = ' . $race . ' ' : '') . '
					' . ($class ? 'AND `wsc`.`class_id` = ' . $class . ' ' : '') . '
				GROUP BY `wsc`.`race_id`
			';
			$this->db->query($query);
			while (false != ($rd = $this->db->fetch())) {
				$ajax['races']['labels'][]	= $rd['name'];
				$ajax['races']['colors'][]	= $this->hex2rgba($rd['color'], 0.6);
				$ajax['races']['data'][]	= $rd['characters'];
				$ajax['races']['ids'][]		= $rd['race_id'];
			}

			$query = '
				SELECT
					`wsc`.`class_id`, `c`.`name` AS `name`, `c`.`color`, COUNT(`wsc`.`id`) as `characters`
				FROM `world_stats_characters` AS `wsc`
				LEFT JOIN `classes` AS `c` ON `c`.`id` = `wsc`.`class_id`
				WHERE 1
					' . ($faction ? 'AND `wsc`.`faction_id` = ' . $faction . ' ' : '') . '
					' . ($race ? 'AND `wsc`.`race_id` = ' . $race . ' ' : '') . '
					#' . ($class ? 'AND `wsc`.`class_id` = ' . $class . ' ' : '') . '
				GROUP BY `wsc`.`class_id`
			';
			$this->db->query($query);
			while (false != ($cd = $this->db->fetch())) {
				$ajax['classes']['labels'][]	= $cd['name'];
				$ajax['classes']['colors'][]	= $this->hex2rgba($cd['color'], 0.6);
				$ajax['classes']['data'][]		= $cd['characters'];
				$ajax['classes']['ids'][]		= $cd['class_id'];
			}

			return $ajax;

		}

	}
