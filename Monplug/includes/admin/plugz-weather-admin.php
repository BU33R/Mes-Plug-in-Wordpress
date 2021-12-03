
<?php 

require_once __DIR__.'/../models/data.php';

require_once __DIR__.'/../controllers/weatherController.php';

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<link rel="stylesheet" href="../css/mon-css.css"/>

</br>

<div class="titlePage"> <h1 class="cheat2-0">    <?php echo esc_html( get_admin_page_title() ); ?>   </h1>   </div>

<hr />

</br>

<body>
    <section class="container-fluid textCenter">
    <div id="api-key" class="d-flex flex-column px-5 py-4 mb-4">
                <h2 class="cheat2-0">Paramètres de l'API </h2>
                <form action="" method="post" class="d-flex flex-column">
                    <label for="apikey">Votre clé d'API</label>
                    <input type="text" name="apikey" id="apikey" value="<?= $myApiKey["option_value"]  ?>">
                    <input type="submit" name="<?php echo !empty($myApiKey)?'update-apikey':'register-apikey'; ?>"
                        id="api-register" class="btn btn-primary mt-3"
                        value="<?php echo !empty($myApiKey)?'Modifier votre clé':'Enregistrer votre clé'; ?>">



                    <input type="hidden" value="<?php echo $_POST["apikey"] ?>">
                </form>

            </div>
    </section>
</body>




























<!-- 
<div class="jsx-1 try-container">

    <h3 class="jsx-1">
        Recherche par code postal
    </h3>

    <div class="jsx-2 form" data-form-type="other">

        <input type="text" placeholder="Code postal" class="jsx-2" value="39000" data-form-type="address,zip">

        <button type="submit" class="button large primary" data-form-type="action">
            Chercher
        </button>

    </div>

    </img>

</div> -->