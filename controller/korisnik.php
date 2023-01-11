<?php

include("model/baza.php");
include("model/korisnici_DB.php");
include("model/slike_DB.php");
include("model/radionice_DB.php");
include("model/prijave_DB.php");

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
    public static function promeni_profilnu() {
        // TODO: Nakon dodavanja komentara ne radi menjanje profilne??????
        $idK = $_SESSION["korisnik"];
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);
        
        if ($_FILES["slika"]["error"] != 0) {
            $greska = "Greška: Nije prosleđen fajl";
            Korisnik::Profil($greska);
            return;
        }
        $flag = getimagesize($_FILES["slika"]["tmp_name"]);
        if (!$flag) {
            $greska = "Greška: Prosleđeni fajl nije slika";
            Korisnik::Profil($greska);
            return;
        }
        // kada dohvatimo tip slike vraca IMAGETYPE_COUNT iz nekog razloga
        // TODO: popraviti to
        /*$a = getimagesize($_FILES["slika"]["tmp_name"]);
        $image_type = $a[2];
        
        if(!in_array($image_type , array(IMAGETYPE_PNG, IMAGETYPE_JPEG))) {
            $greska = "Greška: Slika nije u odgovarajućem formatu (PNG ili JPG)";
            Korisnik::profil($greska);
            return;
        }*/
        list($width, $height) = getimagesize($_FILES["slika"]["tmp_name"]);
        if (!($width >= 100 && $width <= 300 && $height >= 100 && $height <= 300)) {
            $greska = "Greška: Slika nije zadovoljavajućih dimenzija (100x100px do 300x300px)";
            Korisnik::Profil($greska);
            return;
        }
        
        $slika = $_FILES["slika"]["tmp_name"];
        if (!is_uploaded_file($slika)){
            $greska .= "Greška: Greška pri menjanju profilne slike";
            Korisnik::profil($greska);
            return;
        }
        
        if ($korisnik["idS"] != NULL) {
            $idS = $korisnik["idS"];
            $slika = SlikeDB::get_sliku($idS);
            $putanja = $slika["putanja"];
            unlink($putanja);
            SlikeDB::izbrisi_sliku($idS);
        }
        
        /*
        $slika = $_FILES["slika"]["tmp_name"];
        $putanja = "db_files/korisnici/".$idK;
        if (!is_dir($putanja)) {
            mkdir($putanja);
        }
        $putanja .= "/profilna";
        $tmp = move_uploaded_file($slika, $putanja);
        if (!$tmp) {
            $greska .= "Greška: Greška pri menjanju profilne slike";
            Korisnik::profil($greska);
            return;
        }
        SlikeDB::dodaj_sliku($putanja);
        KorisniciDB::dodaj_sliku($idK);*/
        Korisnik::profil();
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
    
    public static function radionice($radionice=NULL) {
        if ($radionice == NULL) {
            $radionice = Radionice_DB::get_sve_radionice();
        }
        $mesta = Radionice_DB::get_mesta();
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/radionice.php");
        include("view/footer.php");
    }
    public static function radionica_detalji($idR=NULL, $greska=NULL) {
        if ($idR == NULL) {
            $idR = filter_input(INPUT_GET, "idR", FILTER_SANITIZE_STRING);
        }
        $radionica = Radionice_DB::get_radionicu_po_idR($idR);
        $idG = $radionica["idG"];
        $galerija = SlikeDB::get_sliku($idG);
        $komentari = Radionice_DB::get_komentare($idR);
        
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/radionice_detalji.php");
        include("view/footer.php");
    }
    public static function prijavi_radionicu() {
        $idR = filter_input(INPUT_GET, "idR", FILTER_SANITIZE_STRING);
        $idK = $_SESSION["korisnik"];
        $tmp = PrijaveDB::dodaj_prijavu($idR, $idK);
        if (!$tmp) {
            $greska = "Greška: Neuspešna prijava na radionicu";
            radionica_detalji($idR, $greska);
            return;
        }
        redionica_detalji($idR);
    }
    public static function lajkuj_radionicu() {
        
    }
    public static function komentarisi_radionicu() {
        $idK = $_SESSION["korisnik"];
        $komentar = filter_input(INPUT_GET, "komentar", FILTER_SANITIZE_STRING);
        $idR = filter_input(INPUT_GET, "idR", FILTER_SANITIZE_STRING);
        
        $tmp = Radionice_DB::dodaj_komentar($idK, $idR, $komentar);
        if (!$tmp) {
            $greska = "Greška: Greška pri dodavanju komentara";
            Korisnik::radionica_detalji($idR, $greska);
            return;
        }
        Korisnik::radionica_detalji($idR);
    }
}

?>