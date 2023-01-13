<?php

class PrijaveDB {
    
    public static function get_sve_neodobrene_prijave($idR) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM prijave WHERE (idR=:idR AND odobri=0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->execute();
        $prijave = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $prijave;
    }
    
    public static function odobri_prijavu($idK, $idR) {
        $db = Baza::getInstanca();
        $upit = "UPDATE prijave SET odobri=1 WHERE (idK=:idK AND idR=:idR)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->bindValue(":idR", $idR);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function dodaj_prijavu($idR, $idK) {
        $db = Baza::getInstanca();
        $upit = "INSERT INTO prijave"
                . " (idR, idK)"
                . " VALUES (:idR, :idK)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    
}

?>