<?php

require_once __DIR__ . '/includes/models/data.php';

require_once __DIR__ . '/includes/models/database.php';

/*

Plugin name: Plug'zz
Description: Récupération et exploitation des données sur toutes les communes de France, mais aussi pour la météo.
Version: 0.1
Author: BU3R.
Author URI:
License: GPL3

*/

function initialisation(){

    function createPage(){
      $page_array = array(
        'post_title' => 'Météo',
        'post_content' => 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Reprehenderit, repellat. Expedita pariatur error eveniet consequuntur reiciendis quos ipsa id esse. Quia earum dolores porro hic sed delectus, illum accusamus vitae!',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_author'  => get_current_user_id(),
    );
    wp_insert_post($page_array);
    }
  
/**
 * Si inexistante, on créée la table SQL "shortcode" et "communes" après l'activation du thème
*/

    function createTableCode(){
      global $wpdb;
      $query ='CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'shortcode
      (
          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          shortcode VARCHAR(30)
      )';
      $wpdb->query($query);
    }
  
    function createTableCommunes(){
      global $wpdb;
      $query = 'CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'communes
      (
          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          code INT(6),
          codepostal INT(6),
          nom VARCHAR(30)
      )';
      $wpdb->query($query);
    }
  
    function curl(){
      $curl = curl_init("https://geo.api.gouv.fr/communes");
      curl_setopt_array($curl, [
              CURLOPT_CAINFO          => __DIR__ . DIRECTORY_SEPARATOR . 'cert.cer',
              CURLOPT_RETURNTRANSFER  => true,
      ]);
      $communes = curl_exec($curl);
      $communes = json_decode($communes, true);
  
      global $wpdb;
  
      $values = array();
      $place_holders = array();
      $query = 'INSERT INTO '.$wpdb->prefix.'communes ( code, codepostal, nom) VALUES ';
      foreach ($communes as $commune){
          foreach ($commune['codesPostaux'] as $codepostal){
          array_push($values, $commune['code'], $codepostal, $commune['nom']);
          $place_holders[] = "(%d, %d, %s)";
      }}
      $query .= implode( ', ', $place_holders );
      $wpdb->query( $wpdb->prepare( "$query ", $values ) );
  
      curl_close($curl);   
  }
  
    createPage();
    createTableCode();
    createTableCommunes();
    curl();
}

// Le Hook qui permet de lancer une fonction au moment ou l'on active le plugin 

register_activation_hook(__FILE__, 'initialisation');


//  Création d'un nouveau menu / Ajout de lien de notre plugin dans le menu latéral

add_action('admin_menu', 'lienDeMenus');

function lienDeMenus() {
    
    add_menu_page(

        "Plug'z Weather - Admin",       //Titre de la page
        "Plug'z Weather",               //Lien devant être affiché dans la barre latérale
        'manage_options',               //Obligatoire pour que ca fonctionne
        'plugz_weather_admin',          //Le Slug
        'plugz_weather_admin_page'      //Le callBack

    );

}

function plugz_weather_admin_page(){
    require_once("includes/admin/plugz-weather-admin.php");
}

// Pour supprimer la page crée par le plugin a son activation

function desactivation() {

    $ondoitsupprimerquoi = get_page_by_title('meteo');
    wp_delete_post($ondoitsupprimerquoi -> ID, true);

}

// Le Hook qui permet de lancer une fonction au moment ou l'on désactive le plugin 

register_deactivation_hook(__FILE__, 'desactivation');

function sup_table(){

    global $wpdb;
    $servername = $wpdb->dbhost;
    $username = $wpdb->dbuser;
    $password = $wpdb->dbpassword;
    $dbname = $wpdb->dbname;
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $request = $conn->prepare(
        "DROP TABLE ".$wpdb->prefix."communes"
    );
    $request->execute();
}

register_uninstall_hook(__FILE__, 'sup_table');

// pour importer une feuille css

function cardStyles() {  


   wp_enqueue_style('Monplug', plugins_url('includes/css/mon-css.css', __FILE__));

}

add_action('admin_enqueue_scripts', 'cardStyles');

?>