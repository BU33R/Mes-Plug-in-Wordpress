<?php

require_once 'database.php';

class Data extends Database
{


    // Mettre le shortcode dans la base de données

    public function setShortcode($town){

        $insert = $this->connect()->prepare("INSERT INTO bubuu_shortcode (shortcode) VALUES (:shortcode)");
        $shortcode ='[meteo ville="'.$town.'"]';
        $insert->bindParam(":shortcode", $shortcode);
        $insert->execute();
        
    }

    // Pour l'afficher dans le champ et ainsi l'utilisateur peut le copier après le rafraîchissement de la page

    public function getShortcode(){

        $select = $this->connect()->prepare("SELECT shortcode FROM bubuu_shortcode");
        $select->execute();
        $shortcode = $select->fetch();
        return $shortcode;

    }



}



