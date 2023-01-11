<?php

include("model/baza.php");
include("model/korisnici_DB.php");
include("model/slike_DB.php");
include("model/radionice_DB.php");

class Korisnik {
    
    public static function profil($greska=NULL) {
        // TODO: Srediti ovo sve i dodati promenu slike
        $idK = $_SESSION["korisnik"];
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);
        $idS = $korisnik["idS"];
        if ($idS != NULL) {
            $profilna = SlikeDB::get_sliku($idS)["putanja"];
        } else {
            $profilna = False;
        }
        
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/profil.php");
        include("view/footer.php");
    }
    public static function azuriraj_podatke() {
        $ime = filter_input(INPUT_GET, "ime", FILTER_SANITIZE_STRING);
        $prezime = filter_input(INPUT_GET, "prezime", FILTER_SANITIZE_STRING);
        $kor_ime = filter_input(INPUT_GET, "kor_ime", FILTER_SANITIZE_STRING);
        $telefon = filter_input(INPUT_GET, "telefon", FILTER_SANITIZE_STRING);
        $mejl = filter_input(INPUT_GET, "mejl", FILTER_SANITIZE_STRING);
        
        $idK = $_SESSION["korisnik"];
        
        $korisnik = KorisniciDB::get_korisnika_po_kor_ime($kor_ime);
        if ($korisnik && $korisnik["idK"] != $idK) {
            Korisnik::profil("Greška: Korisničko ime je zauzeto");
            return;
        }
        $korisnik = KorisniciDB::get_korisnika_po_mejl($mejl);
        if ($korisnik && $korisnik["idK"] != $idK) {
            Korisnik::profil("Greška: Već postoji nalog registrovan na toj e-mail adresi");
            return;
        }
        $tmp = KorisniciDB::azuriraj_korisnika($idK, $ime, $prezime, $kor_ime, $telefon, $mejl);
        $greska = "";
        if (!$tmp) {
            $greska = "Greška: Neuspešno ažuriranje podataka";
        }
        Korisnik::profil();
    }
    public static function promeni_lozinku() {
        $idK = $_SESSION["korisnik"];
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);
        
        $stara_lozinka = filter_input(INPUT_GET, "stara_lozinka", FILTER_SANITIZE_STRING);
        $nova_lozinka = filter_input(INPUT_GET, "nova_lozinka", FILTER_SANITIZE_STRING);
        $potvrda = filter_input(INPUT_GET, "potvrda", FILTER_SANITIZE_STRING);
        
        $lozinka;
        if ($korisnik["lozinka_promenjena"]) {
            $lozinka = $korisnik["lozinka_privremena"];
        } else {
            $lozinka = $korisnik["lozinka"];
        }
        
        $greska = "Greška: Greška pri promeni lozinke, proverite unesene podatke";
        if ($stara_lozinka != $lozinka) {
            Korisnik::profil($greska);
            return;
        }
        if ($nova_lozinka != $potvrda) {
            Korisnik::profil($greska);
            return;
        }
        
        $tmp = KorisniciDB::promeni_lozinku($idK, $nova_lozinka);
        if (!$tmp) {
            Korisnik::profil($greska);
            return;
        }
        Korisnik::profil();
    }
    public static function promeni_profilnu() {
        
    }
    
    public static function radionice($radionice=NULL) {
        if ($radionice == NULL) {
            $radionice = Radionice_DB::get_sve_radionice();
        }
        $mesta = Radionice_DB::get_mesta();
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/radionice.php");
        include("view/footer.php");
    }
    public static function radionica_detalji() {
        $idR = filter_input(INPUT_GET, "idR", FILTER_SANITIZE_STRING);
        $radionica = Radionice_DB::get_radionicu_po_idR($idR);
        $xcor = 51.505;
        $ycor = -0.09;
        
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/radionice_detalji.php");
        include("view/footer.php");
    }
    
    
}

?>