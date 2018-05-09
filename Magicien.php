<?php

class Magicien extends Personnage {

        /* Méthodes d'instance */

    /* Permet de lancer un sort qui endort la cible */
    public function lancerSort(Personnage $pCible) {
        $temps = (2 * 6) * 3600;
        $pCible->setTimeEndormi(time() + $temps);

        echo "prout";

        return self::PERSONNAGE_ENSORCELE;
    }

}