<?php

class Radionice_DB {
    public static function get_sve_radionice() {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM radionice";
        $iskaz = $db->prepare($upit);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    
    public static function get_radionice_po_mesto($mesto) {
        $db = Baza::getInstanca();
        $upit = "SELECT *"
                . " FROM radionice"
                . " WHERE mesto=:mesto";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":mesto", $mesto);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    
    public static function get_radionice_po_naziv($naziv) {
        $db = Baza::getInstanca();
        $upit = "SELECT *"
                . " FROM radionice"
                . " WHERE (lower(naziv) LIKE lower(:naziv))";
        $iskaz = $db->prepare($upit);
        $naziv .= "%";
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    
    public static function get_radionice_po_mesto_i_naziv($mesto, $naziv) {
        $db = Baza::getInstanca();
        $upit = "SELECT *"
                . " FROM radionice"
                . " WHERE (lower(naziv) LIKE lower(:naziv)"
                . " AND mesto=:mesto)";
        $iskaz = $db->prepare($upit);
        $naziv .= "%";
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->bindValue(":mesto", $mesto);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    
    public static function get_mesta() {
        $db = Baza::getInstanca();
        $upit = "SELECT DISTINCT mesto"
                . " FROM radionice";
        $iskaz = $db->prepare($upit);
        $iskaz->execute();
        $mesta = $iskaz->fetchAll(PDO::FETCH_COLUMN);
        $iskaz->closeCursor();
        return $mesta;
    }
    
    public static function dodaj_radionicu($naziv, $datum, $mesto, $opis_kratki, 
            $opis_dugi, $max_broj_posetilaca, $idO) {
        $db = Baza::getInstanca();
        $upit = "INSERT INTO radionice"
                . " (naziv, datum, mesto, opis_kratki, opis_dugi,"
                . " max_broj_posetilaca, idO, odobrena)"
                . " VALUES (:naziv, :datum, :mesto, :opis_kratki, :opis_dugi,"
                . " :max_broj_posetilaca, :idO, 0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->bindValue(":datum", $datum);
        $iskaz->bindValue(":mesto", $mesto);
        $iskaz->bindValue(":opis_kratki", $opis_kratki);
        $iskaz->bindValue(":opis_dugi", $opis_dugi);
        $iskaz->bindValue(":max_broj_posetilaca", $max_broj_posetilaca);
        $iskaz->bindValue(":idO", $idO);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function dodaj_sliku($idR, $idS) {
        $db = Baza::getInstanca();
        $upit = "UPDATE radionice"
                . " SET idS=:idS"
                . " WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idS", $idS);
        $iskaz->bindValue(":idR", $idR);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function dodaj_galeriju($idR, $idG) {
        $db = Baza::getInstanca();
        $upit = "UPDATE radionice"
                . " SET idG=:idG"
                . " WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idG", $idG);
        $iskaz->bindValue(":idR", $idR);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
}

?>