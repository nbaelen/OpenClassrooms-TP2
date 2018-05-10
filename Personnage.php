<?php

/* La classe Personnage ne doit pas pouvoir être instanciée */
abstract class Personnage {

    /* Définition des variables protégées */
    protected
        $id,
        $nom,
        $degats,
        $timeEndormi,
        $type,
        $atout;

    /* Définition des constantes de classe */
    const CEST_MOI = 1;
    const PERSONNAGE_TUE = 2;
    const PERSONNAGE_BLESSE = 3;
    const PERSONNAGE_ENDORMI = 4;
    const PERSONNAGE_ENSORCELE = 5;


    /* Définition du constructeur */
    public function __construct(array $pData) {
        $this->hydrate($pData);
        $this->setType(strtolower(static::class));
    }


    /* Définition de la fonction d'hydratation */
    public function hydrate(array $pData) {
        foreach($pData as $key => $value) {

            $method = 'set' . ucfirst($key);
            if (method_exists($this,$method)) {
                $this->$method($value);
            }
        }
    }


    /* Définition des getters */
    public function getId() {
        return $this->id;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getDegats() {
        return $this->degats;
    }

    public function getTimeEndormi() {
        return (int) $this->timeEndormi;
    }

    public function getType() {
        return $this->type;
    }

    public function getAtout() {
        return $this->atout;
    }


    /* Définition des setters */
    public function setId($pId) {
        $this->id = (int) $pId;
    }

    public function setNom($pNom) {
        if ($pNom != "") {
            $this->nom = $pNom;
        }
    }

    public function setDegats($pDegats) {
        $this->degats = (int) $pDegats;
    }

    public function setTimeendormi($pTime) {
        $this->timeEndormi = (int) $pTime;
    }

    public function setType($pType) {
        if ($pType != "") {
            $this->type = $pType;
        }
    }

    public function setAtout($pAtout) {
        $this->atout = (int) $pAtout;
    }


    /* Méthodes d'instance */
    public function frapper(Personnage $pCible,$pDegats) {
        if ($this === $pCible) {
            return self::CEST_MOI;
        } else {
            return $pCible->recevoirDegats((int) $pDegats);
        }
    }

    public function recevoirDegats($pDegats) {
        $this->degats += $pDegats;
        $this->calculerAtout();
        if ($this->degats >= 100) {
            return self::PERSONNAGE_TUE;
        } else {
            return self::PERSONNAGE_BLESSE;
        }
    }

    public function calculerAtout() {
        if ($this->degats > 90) {
            $this->setAtout(0);
        } else if ($this->degats > 75) {
            $this->setAtout(1);
        } else if ($this->degats > 50) {
            $this->setAtout(2);
        } else if ($this->degats > 25) {
            $this->setAtout(3);
        } else {
            $this->setAtout(4);
        }
    }

    public function reveil() {
        $temps = $this->timeEndormi - time();

        $heures = floor($temps / 3600);
        $minutes = floor(($temps - ($heures * 3600)) / 60);
        $secondes = floor ($temps - ($heures * 3600) - ($minutes * 60));

        return $heures.'h '.$minutes.'m '.$secondes.'s';
    }
}