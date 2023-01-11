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
    
    public static function get_radionicu_po_idR($idR) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM radionice WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->execute();
        $radionica = $iskaz->fetch();
        $iskaz->closeCursor();
        return $radionica;
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
    
    public static function get_broj_prijavljenih_na_radionicu($idR) {
        $db = Baza::getInstanca();
        $upit = "SELECT COUNT(idP)" 
                . " FROM prijave"
                . " WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $broj = $iskaz->fetch();
        $iskaz->closeCursor();
        return $broj;
    }
    
    public static function get_sve_radionice_na_koje_je_korisnik_prijavljen($idK) {
        $db = Baza::getInstanca();
        $upit = "SELECT *" 
                . " FROM radionice JOIN prijave ON radionice.idR=prijave.idR"
                . " WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    
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
    
    public static function korisnik_bio_na_radionici($idK, $idR) {
        $db = Baza::getInstanca();
        $tren_vreme = date('Y-m-d H:i:s', time());
        $upit = "SELECT 1"
                . " FROM radionice JOIN prijave ON radionice.idR=prijave.idR"
                . " WHERE (idK=:idK AND idR=:idR AND datum<:tren_vreme)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->bindValue(":tren_vreme", $tren_vreme);
        $tmp = $iskaz->fetch();
        $iskaz->closeCursor();
        if (!$tmp) {
            return false;
        } else {
            return true;
        }
    }
    
    public static function dodaj_radionicu($naziv, $datum, $mesto, $x_kor, $y_kor, $opis_kratki, 
            $opis_dugi, $max_broj_posetilaca, $idO) {
        $db = Baza::getInstanca();
        $upit = "INSERT INTO radionice"
                . " (naziv, datum, mesto, x_kor, y_kor, opis_kratki, opis_dugi,"
                . " max_broj_posetilaca, idO, odobrena)"
                . " VALUES (:naziv, :datum, :mesto, :x_kor, :y_kor, :opis_kratki, :opis_dugi,"
                . " :max_broj_posetilaca, :idO, 0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->bindValue(":datum", $datum);
        $iskaz->bindValue(":mesto", $mesto);
        $iskaz->bindValue(":x_kor", $x_kor);
        $iskaz->bindValue(":y_kor", $y_kor);
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
}

?>