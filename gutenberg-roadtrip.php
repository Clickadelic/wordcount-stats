<?php

/*
Plugin Name: The Gutenberg Roadtrip
Plugin URI:  https://github.com/Clickadelic/gutenberg-roadtrip
Description: A plugin to experiment with Gutenberg
Version:     0.0.2
Author:      Clickadelic
Author URI:  https://www.tobias-hopp.de/wordpress/plugins/the-white-label
License:     GPL2
License URI: GPL2
Text Domain: gutenberg-roadtrip
Domain Path: /languages
*/

if(!defined('ABSPATH')) {
	exit('NaNa nAnA NaNa nAnA NaNa nAnA Batman!');
}

class WordCountAndTimePlugin {

	public function __construct(){
		add_action('admin_menu', [$this, 'adminPage']);
		add_action('admin_init', [$this, 'settings']);
	}

	public function settings(){

		add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');
		
		add_settings_field('wcp_location', __('Display Location', 'gutenberg-roadtrip'), [$this, 'locationHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_location', ['sanitize_callback' => 'sanitize_text_field', 'default' => '0']);

		add_settings_field('wcp_headline', __('Headline Text', 'gutenberg-roadtrip'), [$this, 'headlineHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_headline', ['sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics']);
		
		add_settings_field('wcp_wordcount', __('Word count', 'gutenberg-roadtrip'), [$this, 'wordcountHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_wordcount', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);

		add_settings_field('wcp_charactercount', __('Character count', 'gutenberg-roadtrip'), [$this, 'charactercountHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_charactercount', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);

		add_settings_field('wcp_readingtime', __('Reading time', 'gutenberg-roadtrip'), [$this, 'readingtimeHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_readingtime', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);

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

	public function adminPage(){
		add_options_page(
			__('Word Count Settings', 'gutenberg-roadtrip'),
			'Word Count',
			'manage_options',
			'word-count-settings-page',
			[$this, 'ourHTML']
		);
	}

	public function ourHTML(){ ?>
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