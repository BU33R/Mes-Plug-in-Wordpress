<?php

// Création de nouveau menu / Ajout de lien de notre plugin dans le menu latéral

add_action('admin_menu', 'lienDeMenus');

function lienDeMenus() {
    
    add_menu_page(
        'ACS Weather - Admin', //Titre de la page
        'ACS Weather', //Lien devant être affiché dans la barre latérale
        'manage_options', //Obligatoire pour que ca fonctionne
        'acs_weather_admin', //Le Slug
        'acs_weather_admin_page'//Le callBack
        );

}

function acs_weather_admin_page(){
    require_once("admin/acs-weather-admin.php");
}

/**
 * Si inexistante, on créée la table SQL "shortcode" et "communes" après l'activation du thème
*/
function initialisation() {

    global $wpdb;
    $servername = $wpdb->dbhost;
    $username = $wpdb->dbuser;
    $password = $wpdb->dbpassword;
    $dbname = $wpdb->dbname;
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $request = $conn->prepare(
        "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."communes (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code INT(6) NOT NULL,
        nom VARCHAR(30) NOT NULL)"
    );

    $request->execute();


    //Hydratation des communes

    $supprimer = $conn->prepare('Delete from '.$wpdb->prefix.'communes');
    $supprimer->execute();
    $curl = curl_init("https://geo.api.gouv.fr/communes");
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     $communes = curl_exec($curl);
     $communes = json_decode($communes, true);
     foreach ($communes as $commune) {
        $cp = implode(",", $commune['codesPostaux']);
        $ajouter = $conn->prepare('INSERT INTO '.$wpdb->prefix.'communes (code, nom) VALUES (:code, :nom)');
        $ajouter->bindParam(':code', $cp);
        $ajouter->bindParam(':nom', $commune['nom']);
        $ajouter->execute();
        $ajouter->debugDumpParams();
    }
    curl_close($curl);





    // Création d'un tableau contenu toutes les caracteristiques necessaires à la création d'un contenu

    $page_array = array(
        'post_title' => 'Météo',
        'post_id' => '',
        'post_content' => '',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => get_current_user_id(),
    
    );

    wp_insert_post($page_array);
}

// Le Hook qui permet de lancer une fonction au moment ou l'on active le plugin 

register_activation_hook(__FILE__, 'initialisation');


// Pour supprimer la page crée par le plugin a son activation

function desactivation() {

    $ondoitsupprimerquoi = get_page_by_title('coucou tlm');
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


   wp_enqueue_style('Monplug', plugins_url('css/mon-css.css', __FILE__));

}

add_action('admin_enqueue_scripts', 'cardStyles');










?>