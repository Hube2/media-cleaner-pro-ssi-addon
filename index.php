<?php 

	/*
		Plugin Name: SSI Addon for Media Cleaner Pro
		Plugin URI: http://www.site-seeker.com/
		Description: SSI Addon for Media Cleaner Pro
		Version: 0.0.1
		Author: John A. Huebner II
		Author URI: https://github.com/Hube2/
	*/
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	new ssi_addon_for_media_cleaner_pro();
	
	class ssi_addon_for_media_cleaner_pro {
		
		public function __construct() {
			add_filter('wpmc_check_media', array($this, 'check_media'), 20, 3);
		} // end public function __construct
		
		public function check_media($in_use, $media_id, $is_broken) {
			
			if ($in_use) {
				return $in_use;
			}
			
			// dirty querries to look for media ID in meta and options
			// this should catch all ACF image fields
			// it will not catch gallery fields
			
			global $wpdb;
			
			//ob_start();
			
			$query = 'SELECT * FROM '.$wpdb->postmeta.' WHERE meta_value = "'.$media_id.'" LIMIT 1';
			$results = $wpdb->get_results($query, 'ARRAY_A');
			//print_r($results);
			if (!empty($results)) {
				$in_use = true;
			}
			
			if (!$in_use) {
				$query = 'SELECT * FROM '.$wpdb->options.' WHERE option_value = "'.$media_id.'" LIMIT 1';
				$results = $wpdb->get_results($query, 'ARRAY_A');
				//print_r($results);
				if (!empty($results)) {
					$in_use = true;
				}
			}
			if (!$in_use) {
				$query = 'SELECT * FROM '.$wpdb->termmeta.' WHERE meta_value = "'.$media_id.'" LIMIT 1';
				$results = $wpdb->get_results($query, 'ARRAY_A');
				//print_r($results);
				if (!empty($results)) {
					$in_use = true;
				}
			}
			if (!$in_use) {
				$query = 'SELECT * FROM '.$wpdb->usermeta.' WHERE meta_value = "'.$media_id.'" LIMIT 1';
				$results = $wpdb->get_results($query, 'ARRAY_A');
				//print_r($results);
				if (!empty($results)) {
					$in_use = true;
				}
			}
			
			
			
			// added for CM Gallery Fields
			//implode(",", $gallery_fields);
			if ($in_use) {
				$gallery_fields = array(
					'tile_pattern_layouts_and_designs_slider',
					'mood_sourcing_slider',
					'product_sourcing_slider',
					'gallery'
				);
				$query = 'SELECT * FROM '.$wpdb->postmeta.' 
								WHERE meta_key IN ("'.implode(",", $gallery_fields).'") AND 
								meta_value LIKE "%\"'.$media_id.'\"%" LIMIT 1';
				$results = $wpdb->get_results($query, 'ARRAY_A');
				//print_r($results);
				if (!empty($results)) {
					$in_use = true;
				}
			}
			
			
			//$this->write_to_file(ob_get_clean());
			
			return $in_use;
		} // end public function check_media
		
		
		private function write_to_file($value, $comment='') {
			// this function for testing & debuggin only
			$file = dirname(__FILE__).'/-data-'.date('Y-m-d-h-i').'.txt';
			$handle = fopen($file, 'a');
			ob_start();
			if ($comment) {
				echo $comment.":\r\n";
			}
			if (is_array($value) || is_object($value)) {
				print_r($value);
			} elseif (is_bool($value)) {
				var_dump($value);
			} else {
				echo $value;
			}
			echo "\r\n\r\n";
			fwrite($handle, ob_get_clean());
			fclose($handle);
		} // end private function write_to_file
		
	} // end class ssi_addon_for_media_cleaner_pro
	
		