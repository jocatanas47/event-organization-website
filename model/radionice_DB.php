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