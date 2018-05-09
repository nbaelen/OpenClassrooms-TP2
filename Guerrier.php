<?php

class Guerrier extends Personnage {

        /* Méthodes d'instances */

    /* Redéfinition de la méthode recevoirDegats pour prendre en compte l'atout */
    public function recevoirDegats($pDegats) {
        $this->calculerAtout();
        $this->degats += $pDegats - $this->atout;
        if ($this->degats >= 100) {
            return self::PERSONNAGE_TUE;
        } else {
            return self::PERSONNAGE_BLESSE;
        }
    }

}