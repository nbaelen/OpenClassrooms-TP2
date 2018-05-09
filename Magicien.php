<?php

class Magicien extends Personnage {

        /* Méthodes d'instance */

    /* Permet de lancer un sort qui endort la cible */
    public function lancerSort(Personnage $pCible) {
        $temps = ($this->atout * 6) * 3600;
        $pCible->setTimeEndormi(time() + $temps);

        return self::PERSONNAGE_ENSORCELE;
    }

}