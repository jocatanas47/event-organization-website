<?php

class KorisniciDB {

    public static function get_sve_korisnike() {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM korisnici";
        $iskaz = $db->prepare($upit);
        $iskaz->execute();
        $korisnici = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $korisnici;
    }
    public static function get_korisnike_kojima_se_svidja_radionica($idR) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM korisnici JOIN svidjanja"
                . " ON korisnici.idK=svidjanja.idK"
                . " WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->execute();
        $korisnici = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $korisnici;
    }
    public static function get_korisnike_koji_su_prijavljeni_na_radionicu($idR) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM korisnici JOIN prijave"
                . " ON korisnici.idK=prijave.idK"
                . " WHERE idR=:idR";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idR", $idR);
        $iskaz->execute();
        $korisnici = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $korisnici;
    }

    public static function get_korisnika_po_idK($idK) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM korisnici WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->execute();
        $korisnik = $iskaz->fetch();
        $iskaz->closeCursor();
        return $korisnik;
    }
    public static function get_korisnika_po_kor_ime($kor_ime) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM korisnici WHERE kor_ime=:kor_ime";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":kor_ime", $kor_ime);
        $iskaz->execute();
        $korisnik = $iskaz->fetch();
        $iskaz->closeCursor();
        return $korisnik;
    }
    public static function get_korisnika_po_mejl($mejl) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM korisnici WHERE mejl=:mejl";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":mejl", $mejl);
        $iskaz->execute();
        $korisnik = $iskaz->fetch();
        $iskaz->closeCursor();
        return $korisnik;
    }
    
    public static function get_organizatora($idK) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM organizatori WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->execute();
        $korisnik = $iskaz->fetch();
        $iskaz->closeCursor();
        return $korisnik;
    }

    public static function dodaj_korisnika($ime, $prezime, $kor_ime, $lozinka, $telefon, $mejl, $tip) {
        $db = Baza::getInstanca();
        if ($tip) {
            $tip = 1;
        } else {
            $tip = 0;
        }
        $upit = "INSERT INTO korisnici"
                . " (ime, prezime, kor_ime, lozinka, telefon, mejl, tip, status)"
                . " VALUES (:ime, :prezime, :kor_ime, :lozinka, :telefon, :mejl,"
                . " :tip, 0)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":ime", $ime);
        $iskaz->bindValue(":prezime", $prezime);
        $iskaz->bindValue(":kor_ime", $kor_ime);
        $iskaz->bindValue(":lozinka", $lozinka);
        $iskaz->bindValue(":telefon", $telefon);
        $iskaz->bindValue(":mejl", $mejl);
        $iskaz->bindValue(":tip", $tip);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function dodaj_ucesnika($ime, $prezime, $kor_ime, $lozinka, $telefon, $mejl) {
        return KorisniciDB::dodaj_korisnika($ime, $prezime, $kor_ime, $lozinka, $telefon, $mejl, False);
    }
    public static function dodaj_organizatora($ime, $prezime, $kor_ime, $lozinka, $telefon, $mejl, 
            $naziv, $maticni_broj, $drzava, $grad, $postanski_broj, $ulica, $adresa_broj) {
        $db = Baza::getInstanca();
        $tmp = KorisniciDB::dodaj_korisnika($ime, $prezime, $kor_ime, $lozinka, $telefon, $mejl, True);
        if (!$tmp) {
            return false;
        }
        $korisnik = KorisniciDB::get_korisnika_po_kor_ime($kor_ime);
        $upit = "INSERT INTO organizatori"
                . " (idK, naziv, maticni_broj, drzava, grad, postanski_broj,"
                . " ulica, adresa_broj)"
                . " VALUES (:idK, :naziv, :maticni_broj, :drzava, :grad, 
                    :postanski_broj, :ulica, :adresa_broj)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $korisnik["idK"]);
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->bindValue(":maticni_broj", $maticni_broj);
        $iskaz->bindValue(":drzava", $drzava);
        $iskaz->bindValue(":grad", $grad);
        $iskaz->bindValue(":postanski_broj", $postanski_broj);
        $iskaz->bindValue(":ulica", $ulica);
        $iskaz->bindValue(":adresa_broj", $adresa_broj);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function dodaj_sliku($idK) {
        $db = Baza::getInstanca();
        $upit = "UPDATE korisnici"
                . " SET idS=LAST_INSERT_ID()"
                . " WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function dodaj_privremenu_lozinku($mejl, $privremena_lozinka) {
        $db = Baza::getInstanca();
        $vreme = date('Y-m-d H:i:s', time() + 30*60);
        $upit = "UPDATE korisnici"
                . " SET lozinka_privremena=:privremena_lozinka, lozinka_trajanje=:vreme, lozinka_promenjena=1"
                . " WHERE mejl = :mejl";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":privremena_lozinka", $privremena_lozinka);
        $iskaz->bindValue(":vreme", $vreme);
        $iskaz->bindValue(":mejl", $mejl);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function promeni_lozinku($idK, $nova_lozinka) {
        $db = Baza::getInstanca();
        $upit = "UPDATE korisnici"
                . " SET lozinka=:nova_lozinka, lozinka_promenjena=0"
                . " WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":nova_lozinka", $nova_lozinka);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function azuriraj_korisnika($idK, $ime, $prezime, $kor_ime, $telefon, $mejl) {
        $db = Baza::getInstanca();
        $upit = "UPDATE korisnici"
                . " SET ime=:ime, prezime=:prezime, kor_ime=:kor_ime, telefon=:telefon, mejl=:mejl"
                . " WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":ime", $ime);
        $iskaz->bindValue(":prezime", $prezime);
        $iskaz->bindValue(":kor_ime", $kor_ime);
        $iskaz->bindValue(":telefon", $telefon);
        $iskaz->bindValue(":mejl", $mejl);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function azuriraj_firmu($idK, $naziv, $maticni_broj, $drzava,
            $grad, $postanski_broj, $ulica, $adresa_broj) {
        $db = Baza::getInstanca();
        $upit = "UPDATE organizatori"
                . " SET naziv=:naziv, maticni_broj=:maticni_broj, drzava=:drzava,"
                . " grad=:grad, postanski_broj=:postanski_broj, ulica=:ulica, adresa_broj=:adresa_broj"
                . " WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":naziv", $naziv);
        $iskaz->bindValue(":maticni_broj", $maticni_broj);
        $iskaz->bindValue(":drzava", $drzava);
        $iskaz->bindValue(":grad", $grad);
        $iskaz->bindValue(":postanski_broj", $postanski_broj);
        $iskaz->bindValue(":ulica", $ulica);
        $iskaz->bindValue(":adresa_broj", $adresa_broj);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function odobri_korisnika($idK) {
        $db = Baza::getInstanca();
        $upit = "UPDATE korisnici SET status=1 WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function odbij_korisnika($idK) {
        $db = Baza::getInstanca();
        $upit = "UPDATE korisnici SET status=2 WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function izbrisi_korisnika($idK) {
        $db = Baza::getInstanca();
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);
        $tip = $korisnik["tip"];
        $upit = "DELETE FROM korisnici WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        if (!$tmp) {
            return false;
        }
        
        $upit = "DELETE FROM prijave WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        if (!$tmp) {
            return false;
        }
        $upit = "DELETE FROM svidjanja WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        if (!$tmp) {
            return false;
        }
        $upit = "DELETE FROM komentari WHERE idKor=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        if (!$tmp) {
            return false;
        }
        
        if ($tip == 0) {
            return true;
        }
        $upit = "DELETE FROM organizatori WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        if (!$tmp) {
            return false;
        }
        return true;
    }
    
    
    public static function korisnik_predlozio_radionicu($idK) {
        $db = Baza::getInstanca();
        $upit = "SELECT 1"
                . " FROM korisnici"
                . " WHERE (idK=:idK AND hoce_org=1)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $iskaz->execute();
        $tmp = $iskaz->fetch();
        $iskaz->closeCursor();
        if (!$tmp) {
            return false;
        } else {
            return true;
        }
    }
    public static function korisnik_hoce_org($idK) {
        $db = Baza::getInstanca();
        $upit = "UPDATE korisnici"
                . " SET hoce_org=1"
                . " WHERE idK=:idK";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idK", $idK);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
    public static function dodaj_test($str="default") {
        $db = Baza::getInstanca();
        $upit = "INSERT INTO test_baza"
                . " (test)"
                . " VALUES (:str)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":str", $str);
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    
}
?>