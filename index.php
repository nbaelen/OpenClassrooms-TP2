<?php

//Fonction d'autoload
function autoload($classname) {
    require $classname.'.php';
}

spl_autoload_register('autoload');


//Gestion de la session
session_start();

if (isset($_SESSION['perso'])){
    $perso = $_SESSION['perso'];
}

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header ('Location: .');
    exit();
}


//Création du PDO et du PersonnageManager
include('database.php');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$manager = new PersonnageManager($db);


//Création d'un nouveau personnage
if (isset($_POST['creer'], $_POST['nom']) &&  $_POST['nom'] != "") {
    if ($manager->exists($_POST['nom'])) {
        $message = 'Ce nom de personnage est déjà utilisé !';
    } else {
        $class = ucfirst($_POST['type']);
        $perso = new $class(['nom' => $_POST['nom']]);
        $manager->addPersonnage($perso);
    }
}


//Utilisation d'un nouveau personnage
if (isset($_POST['utiliser'], $_POST['nom']) &&  $_POST['nom'] != "") {
    if ($manager->exists($_POST['nom'])) {
        $perso = $manager->get($_POST['nom']);
    } else {
        $message = 'Ce personnage n\'existe pas !';
    }
}


//Frapper un personnage
if (isset($_GET['frapper'])) {
    if ($manager->exists((int) $_GET['frapper'])) {
        $persoCible = $manager->get($_GET['frapper']);

        switch($perso->frapper($persoCible,5)) {
            case Personnage::CEST_MOI :
                $message = 'Mais pourquoi voulez vous me frapper ?';
                break;
            case Personnage::PERSONNAGE_BLESSE :
                $message = 'Vous venez de frapper le personnage !';
                $manager->update($persoCible);
                break;
            case Personnage::PERSONNAGE_TUE :
                $message = 'Le personnage a été tué !';
                $manager->deletePersonnage($persoCible);
                break;
        }
    } else {
        $message = 'Le personnage que vous voulez frapper n\'existe pas';
    }
}

//Ensorceler un personnage
if (isset($_GET['ensorceler'])) {
    if ($manager->exists((int) $_GET['ensorceler'])) {
        $persoCible = $manager->get((int) $_GET['ensorceler']);

        switch ($perso->lancerSort($persoCible)) {
            case Personnage::CEST_MOI :
                $message = 'Mais pourquoi voulez vous me frapper ?';
                break;
            case Personnage::PERSONNAGE_ENSORCELE :
                $message = 'Vous venez d\'ensorceler le personnage !';
                $manager->update($persoCible);
                break;
            case Personnage::PAS_DE_MAGIE :
                $message = 'Vous n\'avez pas assez de magie pour ensorceler le personnage !';
                break;
        }
    } else {
        $message = 'Le personnage que vous voulez ensorceler n\'existe pas';
    }
}

?>


    <!DOCTYPE html>
    <html>
    <head>
        <title>Mini jeu de combat</title>
        <meta charset="utf-8">
    </head>

    <body>
    <p>Nombres de personnages créés : <?= $manager->count() ?></p>
    <? if (isset($message)) { echo "<p>$message</p>"; } ?>
    <? if (!isset($perso)) { ?>
        <form action="index.php" method="post">
            <p>
                <label for="nom">Nom : </label>
                <input type="text" name="nom" id="nom" maxlength="15" required/>
                <input type="submit" name="utiliser" value="Utiliser ce personnage"><br/>
                <label for="type">Type : </label>
                <select name="type" id="type">
                    <option value="magicien">Magicien</option>
                    <option value="guerrier">Guerrier</option>
                    <option value="brute">Brute</option>
                </select>
                <input type="submit" name="creer" value="Créer ce personnage">

            </p>
        </form>
        <?
    } else {
        ?>
        <p><a href="?deconnexion=1">Deconnexion</a></p>
        <form>
            <fieldset>
                <legend>Mes informations</legend>
                <p>
                    Nom : <?= htmlspecialchars($perso->getNom()) ?><br/>
                    Type : <?= htmlspecialchars(ucfirst($perso->getType())); ?><br/>
                    Dégats : <?= $perso->getDegats() ?><br/>
                    Atout : <?= $perso->getAtout() ?>
                </p>
            </fieldset>
            <fieldset>
                <legend>Qui frapper ?</legend>
                <p>
                    <?
                        if ($perso->getTimeEndormi() > time()) {
                            echo 'Votre personnage est endormi pour '.$perso->reveil().' et ne peut pas frapper d\'autres personnages !';
                        } else {
                            $list = $manager->getList($perso->getNom());

                            if (empty($list)) {
                                echo 'Aucun personnage à frapper';
                            } else {
                                foreach ($list as $listValue) {
                                    echo '<a href="?frapper=' . $listValue->getId() . '">' . htmlspecialchars($listValue->getNom()) . '</a>
                                   (type : ' . $listValue->getType() . ', dégâts : ' . $listValue->getDegats().')';
                                if ($perso->getType() == "magicien") {
                                    echo ' | <a href="?ensorceler=' . $listValue->getId() . '">Lancer un sort';
                                }
                            echo '<br/>';
                                }
                            }
                        }
                    ?>
                </p>
            </fieldset>
        </form>
        <?
    }
    ?>

    </body>
    </html>

<?php
if (isset($perso)) {
    $_SESSION['perso'] = $perso;
}
?>