<?php

/*
Plugin Name: The Gutenberg Roadtrip
Plugin URI:  https://github.com/Clickadelic/gutenberg-roadtrip
Description: A plugin to experiment with Gutenberg.
Version:     0.0.2
Author:      Tobias Hopp
Author URI:  https://www.tobias-hopp.de/wordpress/gutenberg-roadtrip
License URI: GPL2
Text Domain: gutenberg-roadtrip
Domain Path: /languages
*/

if(!defined('ABSPATH')) {
	exit('NaNa nAnA NaNa nAnA NaNa nAnA Batman!');
}

class WordCountAndTimePlugin {

	public function __construct(){
		add_action('init', [$this, 'initPluginTextdomain']);
		add_action('admin_menu', [$this, 'adminPage']);
		add_action('admin_init', [$this, 'settings']);
		add_action('the_content', [$this, 'ifWrap']);
	}

	public function initPluginTextdomain(){
		load_plugin_textdomain('gutenberg-roadtrip', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}

	public function adminPage(){
		add_options_page(
			__('Word Count Settings', 'gutenberg-roadtrip'),
			__('Word Count', 'gutenberg-roadtrip'),
			'manage_options',
			'word-count-settings-page',
			[$this, 'formOutputHTML']
		);
	}

	public function settings(){

		add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');
		
		add_settings_field('wcp_location', __('Display Location', 'gutenberg-roadtrip'), [$this, 'locationHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_location', ['sanitize_callback' => [$this, 'sanitizeLocation'], 'default' => '0']);

		add_settings_field('wcp_headline', __('Headline Text', 'gutenberg-roadtrip'), [$this, 'headlineHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_headline', ['sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics']);
		
		add_settings_field('wcp_wordcount', __('Word count', 'gutenberg-roadtrip'), [$this, 'wordcountHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_wordcount', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);

		add_settings_field('wcp_charactercount', __('Character count', 'gutenberg-roadtrip'), [$this, 'charactercountHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_charactercount', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);

		add_settings_field('wcp_readingtime', __('Reading time', 'gutenberg-roadtrip'), [$this, 'readingtimeHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_readingtime', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);

	}

	public function ifWrap($content){
		if (is_main_query() AND is_single() AND
			(
				get_option('wcp_wordcount', '1') OR
				get_option('wcp_charactercount', '1') OR
				get_option('wcp_readingtime', '1')
			)) {
			return $this->createHTML($content);
		}
		return $content;
	}

	public function createHTML($content){

		$html = '<div class="word-count-stats">';
		$html .= '<h3>'.esc_attr(get_option('wcp_headline')).'</h3>';
		$html .= '<p>';

		// get word count once because both wordcount and readtime will need it.
		if(get_option('wcp_wordcount', '1') OR get_option('wcp_readtime', '1')) {
			$wordcount = str_word_count(strip_tags($content));
		}

		if(get_option('wcp_wordcount', '1')) {
			$html .= __('This post has ', 'gutenberg-roadtrip'). $wordcount .__(' words', 'gutenberg-roadtrip');
		}
		
		if(get_option('wcp_charactercount', '1')) {
			$html .= ', ' . strlen(strip_tags($content)) . __(' characters ', 'gutenberg-roadtrip');
		}

		if(get_option('wcp_readingtime', '1')) {
			$html .= __('and will take around ', 'gutenberg-roadtrip') . round($wordcount/225) . __(' minute(s) to read.', 'gutenberg-roadtrip');
		}

		$html .= '</p>';

		if(get_option('wcp_location', '0') == '0'){
			return $html . $content;
		}
		return $content . $html;
	}

	public function locationHTML(){ ?>
		<select name="wcp_location">
			<option value="0" <?php selected(get_option('wcp_location'), "0")?>>Begin of post</option>
			<option value="1" <?php selected(get_option('wcp_location'), "1")?>>End of post</option>
		</select>
	<?php
	}
	
	public function headlineHTML(){ ?>
		<input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')); ?>" placeholder="<?php _e('Post summary', 'gutenberg-roadtrip'); ?>">
	<?php
	}

	public function wordcountHTML(){ ?>
		<input type="checkbox" name="wcp_wordcount" value="1" <?php checked(esc_attr(get_option('wcp_wordcount')), '1'); ?> />
	<?php
	}

	public function charactercountHTML(){ ?>
		<input type="checkbox" name="wcp_charactercount" value="1" <?php checked(esc_attr(get_option('wcp_charactercount')), '1'); ?> />
	<?php
	}

	public function readingtimeHTML(){ ?>
		<input type="checkbox" name="wcp_readingtime" value="1" <?php checked(esc_attr(get_option('wcp_readingtime')), '1'); ?> />
	<?php
	}

	public function sanitizeLocation($input){
		if($input != '0' AND $input !='1'){
			add_settings_error('wcp_location', 'wcp_location_error', __('Display location must be either beginning or end with values of either 0 or 1', 'gutenberg-roadtrip'));
			return get_option('wcp_location');
		}
		return $input;
	}

	public function formOutputHTML(){ ?>
		<div class="wrap">
			<h1><?php _e('Word Count Settings', 'gutenberg-roadtrip'); ?></h1>
			<form action="options.php" method="POST">
			<?php
				settings_fields('wordcountplugin');
				do_settings_sections('word-count-settings-page');
				submit_button();
			?>
			</form>
		</div>
	<?php
	}
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();
