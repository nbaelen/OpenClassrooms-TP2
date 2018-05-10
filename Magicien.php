<?php

class Magicien extends Personnage {

        /* Méthodes d'instance */

    /* Permet de lancer un sort qui endort la cible */
    public function lancerSort(Personnage $pCible) {
        if ($this->atout == 0) {
            return self::PAS_DE_MAGIE;
        } else {
            $temps = ($this->atout * 6) * 3600;
            $pCible->setTimeEndormi(time() + $temps);

            return self::PERSONNAGE_ENSORCELE;
        }

    }

}