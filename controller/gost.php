<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require("PHPMailer/Exception.php");
require("PHPMailer/PHPMailer.php");
require("PHPMailer/SMTP.php");

include("model/baza.php");

include("model/korisnici_DB.php");
include("model/radionice_DB.php");
include("model/slike_DB.php");
include("model/svidjanja_DB.php");

class Gost {
    
    public static function prijava($greska=NULL) {
        include("view/gost/header_pocetna.php");
        include("view/gost/prijava.php");
        include("view/footer.php");
    }
    public static function prijavi_se() {
        $kor_ime = filter_input(INPUT_POST, "kor_ime");
        $lozinka = filter_input(INPUT_POST, "lozinka");
        
        $korisnik = KorisniciDB::get_korisnika_po_kor_ime($kor_ime);
        if (!$korisnik) {
            $greska = "Greška: Pogreško korisničko ime ili lozinka";
            Gost::prijava($greska);
            return;
        }
        if ($korisnik["status"] != 1) {
            $greska = "Greška: Vaš nalog nije još prihvaćen";
            Gost::prijava($greska);
            return;
        }
        if (!$korisnik["lozinka_promenjena"]) {
            if ($korisnik["lozinka"] != $lozinka) {
                $greska = "Greška: Pogreško korisničko ime ili lozinka";
                Gost::prijava($greska);
                return;
            }
        } else {
            if (strtotime($korisnik["lozinka_trajanje"]) < time()) {
                $greska = "Greška: Privremena lozinka je istekla";
                Gost::prijava($greska);
                return;
            }
            if ($korisnik["lozinka_privremena"] != $lozinka) {
                $greska = "Greška: Pogreško korisničko ime ili lozinka";
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
    
    public static function zaboravljena_lozinka() {
        include("view/gost/header_pocetna.php");
        include("view/gost/zaboravljena_lozinka.php");
        include("view/footer.php");
    }
    public static function resetuj_lozinku() {
        $mejl = filter_input(INPUT_POST, "mejl");
        
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
        
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
         
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'radionice.projekat@gmail.com';
        $mail->Password = 'ilsimlvuihgulbxc';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('radionice.projekat@gmail.com', 'Mailer');
        $mail->addAddress($mejl);
        
        $mail->isHTML(true);
        $mail->Subject = "Nova Lozinka";
        $mail->Body = "Vaša nova lozinka je: ".$nova_lozinka;
        $mail->send();
        
        header("Location: routes.php?kontroler=gost&akcija=prijava");
    }
    
    public static function registracija() {
        include("view/gost/header_pocetna.php");
        include("view/gost/registracija.php");
        include("view/footer.php");
    }
    public static function dodaj_sliku($slika, $kor_ime) {
        $korisnik = KorisniciDB::get_korisnika_po_kor_ime($kor_ime);
        $idK = $korisnik["idK"];
        
        if ($slika["error"] != 0) {
            // nije poslata slika - ok je
            return true;
        }
        
        $flag = getimagesize($slika["tmp_name"]);
        if (!$flag) {
            return false;
        }
        list($width, $height) = getimagesize($slika["tmp_name"]);
        if (!($width >= 100 && $width <= 300 && $height >= 100 && $height <= 300)) {
            return false;
        }
        
        $slika = $slika["tmp_name"];
        if (!is_uploaded_file($slika)){
            return false;
        }
        
        $putanja = "db_files/korisnici/".$idK;
        if (!is_dir($putanja)) {
            mkdir($putanja);
        }
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
        $ime = filter_input(INPUT_POST, "ime");
        $prezime = filter_input(INPUT_POST, "prezime");
        $kor_ime = filter_input(INPUT_POST, "kor_ime");
        $lozinka = filter_input(INPUT_POST, "lozinka");
        $potvrda = filter_input(INPUT_POST, "potvrda");
        $telefon = filter_input(INPUT_POST, "telefon");
        $mejl = filter_input(INPUT_POST, "mejl");
        $ima_slika = filter_input(INPUT_POST, "ima_slika", FILTER_VALIDATE_BOOLEAN);
        
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
        if ($ima_slika) {
            $slika = $_FILES["slika"];
            if (isset($_FILES['slika'])) {
                list($width, $height) = getimagesize($_FILES["slika"]["tmp_name"]);
                if (!($width >= 100 && $width <= 300 && $height >= 100 && $height <= 300)) {
                    $greska .= "Greška: Slika nije zadovoljavajućih dimenzija (100x100px do 300x300px)";
                }
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
        if ($uspeh && $ima_slika) {
            $slika = $_FILES["slika"];
            $tmp = Gost::dodaj_sliku($slika, $kor_ime);
            if (!$tmp) {
                echo "Greška: Greška pri učitavanju slike";
            }
        }
    }
    
    public static function registracija_organizatora() {
        KorisniciDB::dodaj_test("ddd");
        $ime = filter_input(INPUT_POST, "ime");
        $prezime = filter_input(INPUT_POST, "prezime");
        $kor_ime = filter_input(INPUT_POST, "kor_ime");
        $lozinka = filter_input(INPUT_POST, "lozinka");
        $potvrda = filter_input(INPUT_POST, "potvrda");
        $telefon = filter_input(INPUT_POST, "telefon");
        $mejl = filter_input(INPUT_POST, "mejl");
        $ima_slika = filter_input(INPUT_POST, "ima_slika", FILTER_VALIDATE_BOOLEAN);
        
        $naziv = filter_input(INPUT_POST, "naziv");
        $maticni_broj = filter_input(INPUT_POST, "maticni_broj");
        $drzava = filter_input(INPUT_POST, "drzava");
        $grad = filter_input(INPUT_POST, "grad");
        $postanski_broj = filter_input(INPUT_POST, "postanski_broj");
        $ulica = filter_input(INPUT_POST, "ulica");
        $adresa_broj = filter_input(INPUT_POST, "adresa_broj");
        
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
        KorisniciDB::dodaj_test("ccc");
        if ($ima_slika) {
            if (isset($_FILES["slika"])) {
                list($width, $height) = getimagesize($_FILES["slika"]["tmp_name"]);
                if (!($width >= 100 && $width <= 300 && $height >= 100 && $height <= 300)) {
                    $greska .= "Greška: Slika nije zadovoljavajućih dimenzija (100x100px do 300x300px)";
                }
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
        if ($uspeh && $ima_slika) {
            $slika = $_FILES["slika"];
            $tmp = Gost::dodaj_sliku($slika, $kor_ime);
            if (!$tmp) {
                echo "Greška: Greška pri učitavanju slike";
            }
        }
    }
    
    public static function radionice($radionice=NULL) {
        if ($radionice == NULL) {
            $radionice = RadioniceDB::get_sve_aktuelne_radionice();
        }
        $mesta = RadioniceDB::get_mesta();
        $top_radionice = RadioniceDB::get_top_radionice();
        include("view/gost/header_pocetna.php");
        include("view/gost/radionice.php");
        include("view/footer.php");
    }
    public static function filtriraj_radionice() {
        $mesto = filter_input(INPUT_POST, "mesto");
        $naziv = filter_input(INPUT_POST, "naziv");
        if ($mesto == "izaberite mesto" && $naziv == "") {
            Gost::radionice();
            return;
        }
        if ($mesto != "izaberite mesto" && $naziv == "") {
            $radionice = RadioniceDB::get_radionice_po_mesto($mesto);
        }
        if ($mesto == "izaberite mesto" && $naziv != "") {
            $radionice = RadioniceDB::get_radionice_po_naziv($naziv);
        }
        if ($mesto != "izaberite mesto" && $naziv != "") {
            $radionice = RadioniceDB::get_radionice_po_mesto_i_naziv($mesto, $naziv);
        }
        Gost::radionice($radionice);
    }
    
    public static function izloguj_se() {
        session_destroy();
        header("Location: routes.php?kontroler=gost&akcija=prijava");
    }
    
}

?>