<?php

class AdministratoriDB {
    
    public static function get_administratora_po_kor_idA($idA) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM administratori WHERE idA=:idA";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idA", $idA);
        $iskaz->execute();
        $administrator = $iskaz->fetch();
        $iskaz->closeCursor();
        return $administrator;
    }
    
    public static function get_administratora_po_kor_ime($kor_ime) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM administratori WHERE kor_ime=:kor_ime";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":kor_ime", $kor_ime);
        $iskaz->execute();
        $administrator = $iskaz->fetch();
        $iskaz->closeCursor();
        return $administrator;
    }
    
    public static function promeni_lozinku($idA, $nova_lozinka) {
        $db = Baza::getInstanca();
        $upit = "UPDATE administratori SET lozinka=:nova_lozinka WHERE idA=:idA";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":nova_lozinka", $nova_lozinka);
        $iskaz->bindValue(":idA", $idA);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }

    
}

?>