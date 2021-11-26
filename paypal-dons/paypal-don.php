<?Php

/*

Plugin Name: Pay-Buhr
Plugin URI: 
Description: Plugin pour les dons paypal 
Version: 1.0
Author: BU3R.
Author URI: https://github.com/BU33R
License: GPLv2

*/

// patch URL
// un Patch URL qui nous servira à appeler nos fichiers dans le répertoire du plugin.

define('PAYDON_BASENAME', plugin_basename(__FILE__));

define('PAYDON_DIR_URL', plugins_url('', PAYDON_BASENAME));


// On va créer une fonction php qu’ont va appeler paypal_don_load_scripts() qui va contenir tous les javascripts qu’ont aura besoin pour notre plugin.

function paypal_don_load_scripts() {

	wp_register_script( 'paypal-dons', PAYDON_DIR_URL. '/js/paypal-dons.js' , dirname(__FILE__) );
	wp_enqueue_script('paypal-dons');
	wp_enqueue_script('jquery');
	wp_enqueue_script('media-upload');
 	wp_enqueue_script('thickbox');
	
}

add_action('admin_enqueue_scripts', 'paypal_don_load_scripts');



function paypal_don_admin_styles() {  

 	// pour importer une feuille css

	// wp_register_style('mon-css', PAYDON_DIR_URL. '/css/mon-css.css' , dirname(__FILE__) );
	// wp_enqueue_style('mon-css');
 	wp_enqueue_style('thickbox');
}

add_action('admin_print_styles', 'paypal_don_admin_styles');



// Ajout du shortcode [don] 
add_shortcode('don','donate'); 

function donate() {
    $donate_options = get_option('donate_plugin_options');
 
    // Bouton image par défaut
    $url = 'http://www.paypal.com/fr_FR/i/btn/btn_donate_SM.gif';
 
    // Autre choix pour le bouton don
    switch ($donate_options['button']) {
 
        case 'small':
            $url = 'http://www.paypal.com/fr_FR/i/btn/btn_donate_SM.gif';
            break;
        case 'medium':
            $url = 'http://www.paypal.com/fr_FR/i/btn/btn_donate_LG.gif';
            break;
        case 'large':
            $url = 'http://www.paypal.com/fr_FR/i/btn/btn_donateCC_LG.gif';
            break; 
		case 'custom':
            $url = $donate_options['paypal_custom_button'];
            break;	 
 
    }
 
    return '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <div class="paypal-donations">
                    <input type="hidden" name="cmd" value="_donations">
                    <input type="hidden" name="business" value="'.$donate_options['paypal_user_id'].'">
                    <input type="hidden" name="rm" value="0">
                    <input type="hidden" name="currency_code" value="'.$donate_options['currency'].'">
                    <input type="image" src="'.$url.'" name="submit" alt="PayPal - The safer, easier way to pay online.">
                    <img alt="" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
                </div>
            </form>';
}

// Vous devez maintenant ajouter une fonction vide, qui est nécessaire pour assurer que les autres fonctions du plugin fonctionnent correctement.

function donate_plugin_cb() {
 
    // Rappel option
 
}

// Ensuite, vous allez ajouter deux fonctions qui vont générer les champs de saisie dans le panneau d’administration du plugin. Une pour votre adresse email PayPal et une pour le bouton personnalisé.

// Génère le formulaire de champ de saisie des paramètres [EMAIL]

function paypal_user_id_html() {
    $donate_options = get_option('donate_plugin_options');
    echo "<input name='donate_plugin_options[paypal_user_id]' type='email' value='{$donate_options['paypal_user_id']}'/>";
	
}

function paypal_custom_button_html() {
    $donate_options = get_option('donate_plugin_options');
    echo "<input  name='donate_plugin_options[paypal_custom_button]' type='text' value='{$donate_options['paypal_custom_button']}'/>";
	echo '<input  class="paypal-button-upload-button button" type="button" value="Télécharger une image" />';
	
}



// Génère le formulaire de champ de saisie des paramètres [RADIO]
function paypal_donation_button_html() {
    $donate_options = get_option('donate_plugin_options');
    ?>
<p>
    <label>
        <input type='radio' name='donate_plugin_options[button]' value='small'
            <?php if($donate_options['button'] == 'small') { echo 'checked'; }  ?>>
        <img src='https://www.paypal.com/fr_FR/i/btn/btn_donate_SM.gif' alt='small'
            style='vertical-align: middle;margin-left: 15px;'>
    </label>
</p>

<p>
    <label>
        <input type='radio' name='donate_plugin_options[button]' value='medium'
            <?php if($donate_options['button'] == 'medium') { echo 'checked'; } ?>>
        <img src='https://www.paypal.com/fr_FR/i/btn/btn_donate_LG.gif' alt='medium'
            style='vertical-align: middle;margin-left: 15px;'>
    </label>
</p>

<p>
    <label>
        <input type='radio' name='donate_plugin_options[button]' value='large'
            <?php if($donate_options['button'] == 'large') { echo 'checked'; } ?>>
        <img src='https://www.paypal.com/fr_FR/i/btn/btn_donateCC_LG.gif' alt='large'
            style='vertical-align: middle;margin-left: 15px;'></br>
    </label>
</p>

<p>
    <label>
        <input type='radio' name='donate_plugin_options[button]' value='custom'
            <?php if($donate_options['button'] == 'custom') { echo 'checked'; } ?>>
        <img src='<?php echo $donate_options['paypal_custom_button'];?>'
            style='vertical-align: middle;margin-left: 15px;'> Personnalisé</br>
    </label>
</p>

<?php
}

// Pour terminer cette partie, nous allons générer un autre champ de saisie avec un menu déroulant, afin que vous puissiez choisir la devise dans laquelle vos dons PayPal seront traités, en ajoutant un tableau PHP.


// Génère le formulaire de champ de saisie des paramètres [DROPDOWN]
function paypal_currency_html() {
    $donate_options = get_option('donate_plugin_options');
 
    $currency = array(
                    'AUD' => 'Australian Dollars (A $)',
                    'BRL' => 'Brazilian Real',
                    'CAD' => 'Canadian Dollars (C $)',
                    'CZK' => 'Czech Koruna',
                    'DKK' => 'Danish Krone',
                    'EUR' => 'Euros (€)',
                    'HKD' => 'Hong Kong Dollar ($)',
                    'HUF' => 'Hungarian Forint',
                    'ILS' => 'Israeli New Shekel',
                    'JPY' => 'Yen (¥)',
                    'MYR' => 'Malaysian Ringgit',
                    'MXN' => 'Mexican Peso',
                    'NOK' => 'Norwegian Krone',
                    'NZD' => 'New Zealand Dollar ($)',
                    'PHP' => 'Philippine Peso',
                    'PLN' => 'Polish Zloty',
                    'GBP' => 'Pounds Sterling (£)',
                    'RUB' => 'Russian Ruble',
                    'SGD' => 'Singapore Dollar ($)',
                    'SEK' => 'Swedish Krona',
                    'CHF' => 'Swiss Franc',
                    'TWD' => 'Taiwan New Dollar',
                    'THB' => 'Thai Baht',
                    'TRY' => 'Turkish Lira',
                    'USD' => 'U.S. Dollars ($)',
                );
    ?>
<select id='currency_code' name='donate_plugin_options[currency]'>
    <?php
            foreach($currency as $code => $label) :
                if( $code == $donate_options['currency'] ) { $selected = "selected='selected'"; } else { $selected = ''; }
                echo "<option {$selected} value='{$code}'>{$label}</option>";
            endforeach; 
        ?>
</select>
<?php
}


// Tous les paramètres et la configuration des champs utilisé dans wordpress
function register_settings_and_fields() {
 
    // $option_group, $option_name, $sanitize_callback
    register_setting('donate_plugin_options','donate_plugin_options');
 
    // $id, $title, $callback, $page
    add_settings_section('donate_plugin_main_section', 'Main Settings', 'donate_plugin_cb', __FILE__);
 
    // $id, $title, $callback, $page, $section, $args
    add_settings_field('paypal_user_id', 'PayPal ID: ', 'paypal_user_id_html', __FILE__, 'donate_plugin_main_section');
	 
    // $id, $title, $callback, $page, $section, $args
    add_settings_field('button', 'Select Button: ', 'paypal_donation_button_html', __FILE__, 'donate_plugin_main_section');
	
	// $id, $title, $callback, $page, $section, $args
    add_settings_field('paypal_custom_button', 'Url de l´image: ', 'paypal_custom_button_html', __FILE__, 'donate_plugin_main_section');
 
    // $id, $title, $callback, $page, $section, $args
    add_settings_field('currency', 'Monnaie: ', 'paypal_currency_html', __FILE__, 'donate_plugin_main_section');
}
 
add_action('admin_init', 'register_settings_and_fields');

// Vous allez maintenant générer le code HTML de la page d’options principales dans WordPress, en mettant en place un div avec la classe warp , et puis en ajoutant  le formulaire et la fonction qui va importer les champs paramètres de votre plugin.

// Génére le code HTML de la page des options principales
function options_page_html() {
 
    ?>
<div class="wrap">
    <h2>Plugin Options</h2>
    <form method="post" action="options.php" enctype="multipart/form-data">
        <input type="text" name="image_location" value="" size="40" />
        <input type="button" class="paypal-button-upload-button" value="Upload Image" />

        <?php 
                // $option_group
                settings_fields( 'donate_plugin_options' );
 
                // $page 
                do_settings_sections( __FILE__ );
            ?>
        <p class="submit">
            <input type="submit" class="button-primary" name="submit" value="Save Changes">
        </p>
    </form>
</div>
<?php
}


// Menu Admin Activation

function options_init() {
 
    // page_title,  menu_title, capability, menu_slug, function

    add_options_page('Dons Paypal Options', 'Dons Paypal Options', 'administrator', __FILE__, 'options_page_html');
}

add_action('admin_menu', 'options_init');



// Activation et vérification des paramètres si ils existent.

function donate_activate() {
    $defaults = array(
                    'paypal_user_id' => get_option('admin_email'),
                    'button' => 'small',
					'paypal_custom_button' => '',
                    'currency' => 'EUR'
                );  
 
  if(get_option('donate_plugin_options')) return;
 
  add_option( 'donate_plugin_options', $defaults );
}
 
register_activation_hook( __FILE__, 'donate_activate' );



?>