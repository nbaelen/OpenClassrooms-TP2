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
if (isset($_POST['creer'], $_POST['nom']) &&  $_POST['nom'] != "" && ($_POST['type'] == "guerrier" || $_POST['type'] == "magicien")) {
    if ($manager->exists($_POST['nom'])) {
        $message = 'Ce nom de personnage est déjà utilisé !';
    } else {
        if ($_POST['type'] == "guerrier") {
            $perso = new Guerrier(['nom' => $_POST['nom']]);
        } else {
            $perso = new Magicien(['nom' => $_POST['nom']]);
        }
        $manager->addPersonnage($perso);
    }
}


//Utilisation d'un nouveau personnage
if (isset($_POST['utiliser'], $_POST['nom']) &&  $_POST['nom'] != "") {
    if ($manager->exists($_POST['nom'])) {
        $manager->checkLastLog($_POST['nom']);

        $perso = $manager->get($_POST['nom']);
    } else {
        $message = 'Ce personnage n\'existe pas !';
    }
}


//Frapper un personnage
if (isset($_GET['frapper'])) {
    if ($manager->exists((int) $_GET['frapper'])) {
        $persoCible = $manager->get($_GET['frapper']);

        switch($perso->frapper($persoCible)) {
            case Personnage::CEST_MOI :
                $message = 'Mais pourquoi voulez vous me frapper ?';
                break;
            case Personnage::PERSONNAGE_TUE :
                $message = 'Le personnage a été tué !';
                $manager->delete($persoCible);
                $manager->update($perso);
                break;
            case Personnage::PERSONNAGE_FRAPPE :
                $message = 'Vous venez de frapper le personnage !';
                $manager->update($persoCible);
                $manager->update($perso);
                break;
            case Personnage::NOMBRE_COUP_DEPASSE :
                $message = 'Vous avez dépassé le nombre de coup autorisé par jour !';
                break;
        }
    } else {
        $message = 'Le personnage que vous voulez frapper n\'existe pas';
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
    <p>Nombres de personnages créés : <?php echo $manager->count() ?></p>
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
                    Nom : <? echo htmlspecialchars($perso->getNom()) ?><br/>
                    Dégats : <? echo $perso->getDegats() ?>
                </p>
            </fieldset>
            <fieldset>
                <legend>Qui frapper ?</legend>
                <p>
                    <?
                    $list = $manager->getList($perso->getNom());

                    if (empty($list)) {
                        echo 'Aucun personnage à frapper';
                    } else {
                        foreach ($list as $listValue) {
                            echo '<a href="?frapper='.$listValue->getId().'">'.htmlspecialchars($listValue->getNom()).'</a>
                                   (dégâts : '.$listValue->getDegats().')<br />';
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