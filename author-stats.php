<?php
/*
Plugin Name: Author Stats
Plugin URI: http://yourdomain.com/
Description: Displays various metrics for a selected author
Version: 1.3
Author: Don Kukral
Author URI: http://yourdomain.com
License: GPL
*/
#ini_set('display_errors', 0); 

define( 'AUTHOR_STATS_VERSION' , '1.0' );
define( 'AUTHOR_STATS_ROOT' , dirname(__FILE__) );
define( 'AUTHOR_STATS_URL' , plugins_url(plugin_basename(dirname(__FILE__)).'/') );
define( 'AUTHOR_STATS_PAGE', 'index.php?page=author-stats');

include_once(AUTHOR_STATS_ROOT . '/php/stats.php');

add_action('admin_menu', 'author_stats_menu');
add_action('admin_enqueue_scripts', 'author_stats_admin_scripts');
add_action( 'admin_print_styles', 'author_stats_admin_styles' );

function author_stats_menu() {
    $author_stats = new AuthorStats();
	$page = add_dashboard_page('Author Stats', 'Author Stats', 'add_users', 'author-stats', array($author_stats, 'view'));

}

function author_stats_admin_scripts() {
	if ((array_key_exists('page', $_GET)) && ($_GET['page'] == 'author-stats')) {
	    wp_enqueue_script("ui", AUTHOR_STATS_URL . "js/jquery-ui-1.10.4.custom.min.js", array('jquery'), '1.0');	
	} else {
	    wp_deregister_script("ui");
	}
    
}

function author_stats_admin_styles() {
    if ((array_key_exists('page', $_GET)) && ($_GET['page'] == 'author-stats')) {
	    wp_enqueue_style( 'author-stats-css', AUTHOR_STATS_URL . 'css/jquery-ui-1.10.4.custom.min.css', false );
    } else {
        wp_deregister_style("author-stats-css");
    }
}

?>
