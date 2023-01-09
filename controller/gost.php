<?php

include("model/baza.php");
include("model/korisnici_DB.php");
include("model/slike_DB.php");
include("model/radionice_DB.php");

class Gost {
    public static function radionice($radionice=NULL) {
        if ($radionice == NULL) {
            $radionice = Radionice_DB::get_sve_radionice();
        }
        $mesta = Radionice_DB::get_mesta();
        include("view/gost/header_pocetna.php");
        include("view/gost/radionice.php");
        include("view/footer.php");
    }
    public static function filtriraj_radionice() {
        $mesto = filter_input(INPUT_GET, "mesto", FILTER_SANITIZE_STRING);
        $naziv = filter_input(INPUT_GET, "naziv", FILTER_SANITIZE_STRING);
        if ($mesto == "izaberite mesto" && $naziv == "") {
            Gost::radionice();
        }
        if ($mesto != "izaberite mesto" && $naziv == "") {
            $radionice = Radionice_DB::get_radionice_po_mesto($mesto);
            Gost::radionice($radionice);
        }
        if ($mesto == "izaberite mesto" && $naziv != "") {
            $radionice = Radionice_DB::get_radionice_po_naziv($naziv);
            Gost::radionice($radionice);
        }
        if ($mesto != "izaberite mesto" && $naziv != "") {
            $radionice = Radionice_DB::get_radionice_po_mesto_i_naziv($mesto, $naziv);
            Gost::radionice($radionice);
        }
        
    }
    public static function prijava($greska=NULL) {
        include("view/gost/header_pocetna.php");
        include("view/gost/prijava.php");
        include("view/footer.php");
    }
    public static function zaboravljena_lozinka() {
        // TODO: dodati greske
        include("view/gost/header_pocetna.php");
        include("view/gost/zaboravljena_lozinka.php");
        include("view/footer.php");
    }
    public static function registracija() {
        // TODO: dodati greske
        include("view/gost/header_pocetna.php");
        include("view/gost/registracija.php");
        include("view/footer.php");
    }
    
    public static function prijavi_se() {
        $kor_ime = filter_input(INPUT_GET, "kor_ime", FILTER_SANITIZE_STRING);
        $lozinka = filter_input(INPUT_GET, "lozinka", FILTER_SANITIZE_STRING);
        
        $korisnik = KorisniciDB::get_korisnika_po_kor_ime($kor_ime);
        if (!$korisnik) {
            $greska = "Pogreško korisničko ime ili lozinka";
            Gost::prijava($greska);
            return;
        }
        
        if (!$korisnik["lozinka_promenjena"]) {
            if ($korisnik["lozinka"] != $lozinka) {
                $greska = "Pogreško korisničko ime ili lozinka";
                Gost::prijava($greska);
                return;
            }
        } else {
            if (strtotime($korisnik["lozinka_trajanje"]) < time()) {
                $greska = "Privremena lozinka je istekla";
                Gost::prijava($greska);
                return;
            }
            if ($korisnik["lozinka_privremena"] != $lozinka) {
                $greska = "Pogreško korisničko ime ili lozinka";
                Gost::prijava($greska);
                return;
            }
        }
        $_SESSION["korisnik"] = $korisnik["idK"];
        if ($korisnik["tip"] == 0) {
            header("Location: routes.php?akcija=profil&kontroler=korisnik");
        } else {
            header("Location: routes.php?akcija=dodavanje_radionice&kontroler=organizator");
        }
        
    }
    
    public static function resetuj_lozinku() {
        $mejl = filter_input(INPUT_GET, "mejl", FILTER_SANITIZE_STRING);
        
        $dobra_lozinka = false;
        $nova_lozinka = "";
        while(!$dobra_lozinka) {
            $nova_lozinka = "";
            $flag = rand(0, 1);
            if ($flag) {
                $nova_lozinka .= chr(rand(65, 90));
            } else {
                $nova_lozinka .= chr(rand(97, 122));
            }
            for ($i = 1; $i < 15; $i++) {
                $nova_lozinka .= chr(rand(33, 126));
            }
            if (preg_match("/\d/", $nova_lozinka) && 
                    preg_match("/[A-Z]/", $nova_lozinka) &&
                    preg_match("/[a-z]/", $nova_lozinka) &&
                    preg_match("/[^A-Za-z1-9\d]/", $nova_lozinka)) {
                $dobra_lozinka = true;
            }
        }
        
        $flag = KorisniciDB::dodaj_privremenu_lozinku($mejl, $nova_lozinka);
        if (!$flag) {
            $greska = "Greška: Greška pri dodeli nove lozinke";
            Gost::prijava($greska);
            return;
        }
        Gost::prijava();
    }
    
    public static function dodaj_sliku($slika, $kor_ime) {
        $korisnik = KorisniciDB::get_korisnika_po_kor_ime($kor_ime);
        $idK = $korisnik["idK"];
        $putanja = "db_files/korisnici/".$idK;
        if (!is_uploaded_file($slika)){
            return false;
        }
        $a = getimagesize($path);
        $image_type = $a[2];
        if(!in_array($image_type , array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_JPG))) {
            return false;
        }

        mkdir($putanja);
        $putanja .= "/profilna";
        $tmp = move_uploaded_file($slika, $putanja);
        if (!$tmp) {
            return false;
        }
        SlikeDB::dodaj_sliku($putanja);
        KorisniciDB::dodaj_sliku($idK);
        return true;
    }
    
    public static function registracija_ucesnika() {
        $ime = filter_input(INPUT_POST, "ime", FILTER_SANITIZE_STRING);
        $prezime = filter_input(INPUT_POST, "prezime", FILTER_SANITIZE_STRING);
        $kor_ime = filter_input(INPUT_POST, "kor_ime", FILTER_SANITIZE_STRING);
        $lozinka = filter_input(INPUT_POST, "lozinka", FILTER_SANITIZE_STRING);
        $potvrda = filter_input(INPUT_POST, "potvrda", FILTER_SANITIZE_STRING);
        $telefon = filter_input(INPUT_POST, "telefon", FILTER_SANITIZE_STRING);
        $mejl = filter_input(INPUT_POST, "mejl", FILTER_SANITIZE_STRING);
        
        $greska = "";
        
        if ($ime == "" || $prezime == "" || $kor_ime == "" || $lozinka == ""
            || $potvrda == "" || $telefon == "" || $mejl == "") {
            $greska .= "Greška: Sva polja označena zvezdicom su obavezna";
        }
        if (!preg_match("/^[a-zA-Z]/", $lozinka) || !preg_match("/[A-Z]/", $lozinka)
                || !preg_match("/\d/", $lozinka) || !preg_match("/[^a-zA-Z\d]/", $lozinka)) {
            $greska .= "Greška: Lozinka mora da sadrži minimalno 8 a maksimalno 16 karaktera; mora da sarži bar jedno veliko slovo cifru i specijalni karakter; mora da kreće slovom<br>";
        }
        if ($lozinka != $potvrda) {
            $greska .= "Greška: Potvrda lozinke mora biti ista kao lozinka";
        }
        if ($_FILES["slika"]["error"] == 0) {
            list($width, $height) = getimagesize($_FILES["slika"]["tmp_name"]);
            if (!($width >= 100 && $width <= 300 && $height >= 100 && $height <= 300)) {
                $greska .= "Greška: Potvrda lozinke mora biti ista kao lozinka";
            }
        }
        if (KorisniciDB::get_korisnika_po_kor_ime($kor_ime)) {
            $greska .= "Greška: Korisničko ime je zauzeto<br>";
        }
        if (KorisniciDB::get_korisnika_po_mejl($mejl)) {
            $greska .= "Greška: Već postoji nalog registrovan na toj e-mail adresi<br>";
        }
        echo $greska;
        
        if ($greska != "") {
            return;
        }
       
        $uspeh = KorisniciDB::dodaj_ucesnika($ime, $prezime, $kor_ime, $lozinka, $telefon, $mejl);
        if ($_FILES["slika"]["error"] == 0 && $uspeh) {
            $slika = $_FILES["slika"]["tmp_name"];
            $tmp = Gost::dodaj_sliku($slika, $kor_ime);
            if (!$tmp) {
                echo "Greška: Greška pri učitavanju slike";
            }
        }
    }
    
    public static function registracija_organizatora() {
        $ime = filter_input(INPUT_POST, "ime", FILTER_SANITIZE_STRING);
        $prezime = filter_input(INPUT_POST, "prezime", FILTER_SANITIZE_STRING);
        $kor_ime = filter_input(INPUT_POST, "kor_ime", FILTER_SANITIZE_STRING);
        $lozinka = filter_input(INPUT_POST, "lozinka", FILTER_SANITIZE_STRING);
        $telefon = filter_input(INPUT_POST, "telefon", FILTER_SANITIZE_STRING);
        $mejl = filter_input(INPUT_POST, "mejl", FILTER_SANITIZE_STRING);
        
        $naziv = filter_input(INPUT_POST, "naziv", FILTER_SANITIZE_STRING);
        $maticni_broj = filter_input(INPUT_POST, "maticni_broj", FILTER_VALIDATE_INT);
        $drzava = filter_input(INPUT_POST, "drzava", FILTER_SANITIZE_STRING);
        $grad = filter_input(INPUT_POST, "grad", FILTER_SANITIZE_STRING);
        $postanski_broj = filter_input(INPUT_POST, "postanski_broj", FILTER_VALIDATE_INT);
        $ulica = filter_input(INPUT_POST, "ulica", FILTER_SANITIZE_STRING);
        $adresa_broj = filter_input(INPUT_POST, "adresa_broj", FILTER_SANITIZE_STRING);
        
        $greska = "";
        
        if ($ime == "" || $prezime == "" || $kor_ime == "" || $lozinka == ""
            || $potvrda == "" || $telefon == "" || $mejl == "") {
            $greska .= "Greška: Sva polja označena zvezdicom su obavezna";
        }
        if (!preg_match("/^[a-zA-Z]/", $lozinka) || !preg_match("/[A-Z]/", $lozinka)
                || !preg_match("/\d/", $lozinka) || !preg_match("/[^a-zA-Z\d]/", $lozinka)) {
            $greska .= "Greška: Lozinka mora da sadrži minimalno 8 a maksimalno 16 karaktera; mora da sarži bar jedno veliko slovo cifru i specijalni karakter; mora da kreće slovom<br>";
        }
        if ($lozinka != $potvrda) {
            $greska .= "Greška: Potvrda lozinke mora biti ista kao lozinka";
        }
        if ($_FILES["slika"]["error"] == 0) {
            list($width, $height) = getimagesize($_FILES["slika"]["tmp_name"]);
            if (!($width >= 100 && $width <= 300 && $height >= 100 && $height <= 300)) {
                $greska .= "Greška: Potvrda lozinke mora biti ista kao lozinka";
            }
        }
        if (KorisniciDB::get_korisnika_po_kor_ime($kor_ime)) {
            $greska .= "Greška: Korisničko ime je zauzeto<br>";
        }
        if (KorisniciDB::get_korisnika_po_mejl($mejl)) {
            $greska .= "Greška: Već postoji nalog registrovan na toj e-mail adresi<br>";
        }
        echo $greska;
        
        if ($greska != "") {
            return;
        }
        
        $uspeh = KorisniciDB::dodaj_organizatora($ime, $prezime, $kor_ime, $lozinka, $telefon, $mejl,
                $naziv, $maticni_broj, $drzava, $grad, $postanski_broj, $ulica, $adresa_broj);
        if ($_FILES["slika"]["error"] == 0 && $uspeh) {
            $slika = $_FILES["slika"]["tmp_name"];
            $tmp = Gost::dodaj_sliku($slika, $kor_ime);
            if (!$tmp) {
                echo "Greška: Greška pri učitavanju slike";
            }
        }
    }
    
    public static function test() {
        
    }
}

?>