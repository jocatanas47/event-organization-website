<?php

class RadioniceDB {
    
    public static function get_sve_radionice() {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM radionice";
        $iskaz = $db->prepare($upit);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    public static function get_sve_aktuelne_radionice() {
        $db = Baza::getInstanca();
        $tren_vreme = date('Y-m-d H:i:s', time());
        $upit = "SELECT * FROM radionice WHERE (datum>:tren_vreme AND otkazana=0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":tren_vreme", $tren_vreme);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    public static function get_sve_radionice_na_koje_je_korisnik_prijavljen($idK) {
        $db = Baza::getInstanca();
        $tren_vreme = date('Y-m-d H:i:s', time());
        $upit = "SELECT *" 
                . " FROM radionice JOIN prijave ON radionice.idR=prijave.idR"
                . " WHERE (idK=:idK AND datum>:tren_vreme AND otkazana=0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->bindValue(":tren_vreme", $tren_vreme);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    public static function get_sve_radionice_na_kojima_je_korisnik_prisustvovao($idK) {
        $db = Baza::getInstanca();
        $tren_vreme = date('Y-m-d H:i:s', time());
        $upit = "SELECT *" 
                . " FROM radionice JOIN prijave ON radionice.idR=prijave.idR"
                . " WHERE (idK=:idK AND datum<:tren_vreme AND odobri=1 AND otkazana=0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->bindValue(":tren_vreme", $tren_vreme);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    public static function get_sve_radionice_organizatora($idO) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM radionice WHERE (idO=:idO AND otkazana=0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idO", $idO);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    public static function get_radionice_po_mesto($mesto) {
        $db = Baza::getInstanca();
        $tren_vreme = date('Y-m-d H:i:s', time());
        $upit = "SELECT *"
                . " FROM radionice"
                . " WHERE (mesto=:mesto AND datum>:tren_vreme AND otkazana=0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":mesto", $mesto);
        $iskaz->bindValue(":tren_vreme", $tren_vreme);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    public static function get_radionice_po_naziv($naziv) {
        $db = Baza::getInstanca();
        $tren_vreme = date('Y-m-d H:i:s', time());
        $upit = "SELECT *"
                . " FROM radionice"
                . " WHERE (lower(naziv) LIKE lower(:naziv) AND datum>:tren_vreme AND otkazana=0)";
        $iskaz = $db->prepare($upit);
        $naziv .= "%";
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->bindValue(":tren_vreme", $tren_vreme);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    public static function get_radionice_po_mesto_i_naziv($mesto, $naziv) {
        $db = Baza::getInstanca();
        $tren_vreme = date('Y-m-d H:i:s', time());
        $upit = "SELECT *"
                . " FROM radionice"
                . " WHERE (lower(naziv) LIKE lower(:naziv)"
                . " AND mesto=:mesto AND datum>:tren_vreme AND otkazana=0)";
        $iskaz = $db->prepare($upit);
        $naziv .= "%";
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->bindValue(":mesto", $mesto);
        $iskaz->bindValue(":tren_vreme", $tren_vreme);
        $iskaz->execute();
        $radionice = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $radionice;
    }
    public static function get_top_radionice() {
        $db = Baza::getInstanca();
        $tren_vreme = date('Y-m-d H:i:s', time());
        $upit = "SELECT *, COUNT(svidjanja.idSvidj) as lajkovi"
                . " FROM radionice"
                . " LEFT JOIN svidjanja ON radionice.idR=svidjanja.idR"
                . " WHERE (datum>:tren_vreme AND otkazana=0)"
                . " GROUP BY radionice.idR"
                . " ORDER BY lajkovi DESC";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":tren_vreme", $tren_vreme);
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
    
    public static function azuriraj_radionicu($idR, $naziv, $datum, $mesto, $x_kor, $y_kor,
            $opis_kratki, $opis_dugi, $max_broj_posetilaca) {
        $db = Baza::getInstanca();
        $upit = "UPDATE radionice"
                . " SET naziv=:naziv, datum=:datum, mesto=:mesto,"
                . " x_kor=:x_kor, y_kor=:y_kor, opis_kratki=:opis_kratki,"
                . " opis_dugi=:opis_dugi, max_broj_posetilaca=:max_broj_posetilaca "
                . " WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->bindValue(":datum", $datum);
        $iskaz->bindValue(":mesto", $mesto);
        $iskaz->bindValue(":x_kor", $x_kor);
        $iskaz->bindValue(":y_kor", $y_kor);
        $iskaz->bindValue(":opis_kratki", $opis_kratki);
        $iskaz->bindValue(":opis_dugi", $opis_dugi);
        $iskaz->bindValue(":max_broj_posetilaca", $max_broj_posetilaca);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function otkazi_radionicu($idR) {
        $db = Baza::getInstanca();
        $upit = "UPDATE radionice"
                . " SET otkazana=1"
                . " WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function korisnik_bio_na_radionici($idK, $idR) {
        $db = Baza::getInstanca();
        $tren_vreme = date('Y-m-d H:i:s', time());
        $naziv = RadioniceDB::get_radionicu_po_idR($idR)["naziv"];
        $upit = "SELECT 1"
                . " FROM radionice JOIN prijave ON radionice.idR=prijave.idR"
                . " WHERE (idK=:idK AND naziv=:naziv AND datum<:tren_vreme AND odobri=1 AND otkazana=0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->bindValue(":tren_vreme", $tren_vreme);
        $iskaz->execute();
        $tmp = $iskaz->fetch();
        $iskaz->closeCursor();
        if (!$tmp) {
            return false;
        } else {
            return true;
        }
    }
    public static function vise_od_12h_do_radionice($idR) {
        $db = Baza::getInstanca();
        $vreme = date('Y-m-d H:i:s', time() + 12*60*60);
        $upit = "SELECT 1"
                . " FROM radionice"
                . " WHERE (idR=:idR AND datum>:vreme)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->bindValue(":vreme", $vreme);
        $iskaz->execute();
        $tmp = $iskaz->fetch();
        $iskaz->closeCursor();
        if (!$tmp) {
            return false;
        } else {
            return true;
        }
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
    
}

?>