<?php

class PersonnageManager {

    /* Déclaration des variables */
    private $_db;


    /* Constructeur par défaut */
    public function __construct($pDb) {
        $this->setDb($pDb);
    }


    /* Définition des setters */
    public function setDb(PDO $pDb) {
        $this->_db = $pDb;
    }


        /* Méthodes d'instance */

    /* Ajoute un personnage en BDD (et l'hydrate) */
    public function addPersonnage(Personnage $pPerso) {
        $query = $this->_db->prepare('INSERT INTO personnages_v2(nom, type) VALUES (?, ?)');
        $query->execute([$pPerso->getNom(),$pPerso->getType()]);

        $pPerso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0
        ]);
    }

    /* Supprime un personnage de la BDD */
    public function deletePersonnage(Personnage $pPerso) {
        $query = $this->_db->prepare('DELETE FROM personnages_v2 WHERE id = ?');
        $query->execute([
            $pPerso->getId()
        ]);
    }

    /* Compte le nombre de personnages enregistrés en BDD */
    public function count() {
        $query = $this->_db->query('SELECT COUNT(*) as count FROM personnages_v2');
        $data = $query->fetch();

        return $data['count'];
    }

    /* Renvoie true si le personnage donné existe en BDD */
    public function exists($pPerso) {
        if (is_numeric($pPerso)) {
            $query = $this->_db->prepare('SELECT COUNT(*) as count FROM personnages_v2 WHERE id = ?');
        } else {
            $query = $this->_db->prepare('SELECT COUNT(*) as count FROM personnages_v2 WHERE nom = ?');
        }
        $query->execute([$pPerso]);
        return (bool) $query->fetchColumn();
    }

    /* Renvoie le personnage demandé */
    public function get($pPerso) {
        if (is_numeric($pPerso)) {
            $query = $this->_db->prepare('SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE id = ?');
        } else {
            $query = $this->_db->prepare('SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE nom = ?');
        }
        $query->execute([$pPerso]);
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if ($data['type'] == "guerrier") {
            return new Guerrier($data);
        } else {
            return new Magicien($data);
        }
    }


    /* Renvoie un tableau contenant tout les personnages, sauf celui envoyé en paramètre */
    public function getList($pPerso) {
        if (is_numeric($pPerso)) {
            $query = $this->_db->prepare('SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE NOT id = ? ORDER BY nom');
        } else {
            $query = $this->_db->prepare('SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE NOT nom = ? ORDER BY nom');
        }
        $query->execute([$pPerso]);

        $personnages = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            if ($data['type'] == "guerrier") {
                $personnages[] = new Guerrier($data);
            } else {
                $personnages[] = new Magicien($data);
            }
        }

        return $personnages;
    }

    /* Permet de mettre à jour les caractéristiques d'un personnage */
    public function update(Personnage $pPerso) {
        $query = $this->_db->prepare('UPDATE personnages_v2 SET degats = ?, atout = ? WHERE id = ?');
        $query->execute([
            $pPerso->getDegats(),
            $pPerso->getAtout(),
            $pPerso->getId()
        ]);
    }
}