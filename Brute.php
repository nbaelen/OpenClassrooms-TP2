<?php

class Brute extends Personnage {

        /* Méthodes d'instance */

    /* Redéfinition de la méthode frapper() pour prendre en compte l'atout */
    public function frapper(Personnage $pCible,$pDegats) {
        if ($this === $pCible) {
            return self::CEST_MOI;
        } else {
            $degats = $pDegats + $this->atout;
            return $pCible->recevoirDegats((int) $degats);
        }
    }
}