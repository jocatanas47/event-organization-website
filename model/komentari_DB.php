<?php

class KomentariDB {
    
    public static function get_komentare($idR) {
        $db = Baza::getInstanca();
        $upit = "SELECT *" 
                . " FROM komentari"
                . " WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->execute();
        $komentari = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $komentari;
    }
    public static function get_komentare_korisnika($idK) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM komentari"
                . " WHERE idKor=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->execute();
        $komentari = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $komentari;
    }
    
    public static function dodaj_komentar($idK, $idR, $komentar) {
        $db = Baza::getInstanca();
        $datum = $vreme = date('Y-m-d H:i:s', time());
        $upit = "INSERT INTO komentari"
                . " (idKor, idR, komentar, datum)"
                . " VALUES (:idK, :idR, :komentar, :datum)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->bindValue(":komentar", $komentar);
        $iskaz->bindValue(":datum", $datum);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function promeni_komentar($idKom, $komentar) {
        $db = Baza::getInstanca();
        $upit = "UPDATE komentari"
                . " SET komentar=:komentar"
                . " WHERE idKom=:idKom";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idKom", $idKom);
        $iskaz->bindValue(":komentar", $komentar);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function izbrisi_komentar($idKom) {
        $db = Baza::getInstanca();
        $upit = "DELETE FROM komentari WHERE idKom=:idKom";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idKom", $idKom);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function get_broj_komentara_radionice($idR) {
        $db = Baza::getInstanca();
        $upit = "SELECT COUNT(idKom)" 
                . " FROM komentari"
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