<?php

class SvidjanjaDB {
    
    public static function get_svidjanja_korisnika($idK) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM svidjanja"
                . " WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->execute();
        $svidjanja = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $svidjanja;
    }
    
    public static function lajkuj_radionicu($idK, $idR) {
        $db = Baza::getInstanca();
        $upit = "INSERT INTO svidjanja"
                . " (idK, idR)"
                . " VALUES (:idK, :idR)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->bindValue(":idR", $idR);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function povuci_svidjanje($idS) {
        $db = Baza::getInstanca();
        $upit = "DELETE FROM svidjanja WHERE idS=:idS";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idS", $idS);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function get_broj_lajkova_radionice($idR) {
        $db = Baza::getInstanca();
        $upit = "SELECT COUNT(idS)" 
                . " FROM svidjanja"
                . " WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->execute();
        $broj = $iskaz->fetchColumn();
        $iskaz->closeCursor();
        return $broj;
    }
    
}

?>