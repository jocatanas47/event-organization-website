<?php

class SvidjanjaDB {
    
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
    
}

?>