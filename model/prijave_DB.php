<?php

class PrijaveDB {
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