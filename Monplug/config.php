<?php

/*

Plugin name: Plug'zz
Description: Mon premiere plug-in Wordpress
Version: 0.1
Author: BU3R.
Author URI:

*/
add_action('admin_menu', 'lienDeMenus');

// Création de nouveau menu 

function lienDeMenus() {
    
    add_menu_page(

        'La page de mon plugin',
        'Mon Plugin',
        'manage_options',
        plugin_dir_path(__FILE__).  'admin/index.php'


    );

}


function cardStyles() {  

    // pour importer une feuille css

   wp_register_style('mon-css', '/css/mon-css.css', dirname(__FILE__) );
   wp_enqueue_style('mon-css');

}

add_action('admin_print_styles', 'cardStyles');





/**
 * Si inexistante, on créée la table SQL "commissions" après l'activation du thème
*/

global $wpdb;

$charset_collate = $wpdb->get_charset_collate();

$shortcode_table_name = $wpdb->prefix . 'shortcode';

$shortcode_sql = "CREATE TABLE IF NOT EXISTS $shortcode_table_name (

	id mediumint(6) NOT NULL AUTO_INCREMENT,

	shortcode varchar(30) DEFAULT NULL,
	
    PRIMARY KEY  (id)

) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

dbDelta($shortcode_sql);



?>