<?php

include("model/baza.php");
include("model/administratori_DB.php");
include("model/korisnici_DB.php");
include("model/slike_DB.php");
include("model/prijave_DB.php");
include("model/radionice_DB.php");

class Administrator {

    public static function prijava($greska = NULL) {
        include("view/header.php");
        include("view/administrator/prijava.php");
        include("view/footer.php");
    }

    public static function prijavi_se() {
        $kor_ime = filter_input(INPUT_GET, "kor_ime", FILTER_SANITIZE_STRING);
        $lozinka = filter_input(INPUT_GET, "lozinka", FILTER_SANITIZE_STRING);

        $administrator = AdministratoriDB::get_administratora_po_kor_ime($kor_ime);
        if (!$administrator) {
            $greska = "Greška: Pogrešno korisničko ime ili lozinka";
            Administrator::prijava($greska);
            return;
        }
        if ($administrator["lozinka"] != $lozinka) {
            $greska = "Greška: Pogrešno korisničko ime ili lozinka";
            Administrator::prijava($greska);
            return;
        }
        $_SESSION["administrator"] = $administrator["idA"];
        header("Location: routes.php?kontroler=administrator&akcija=korisnici");
    }

    public static function promena_lozinke($greska = NULL) {
        include("view/administrator/header_administrator.php");
        include("view/administrator/promena_lozinke.php");
        include("view/footer.php");
    }

    public static function promeni_lozinku() {
        $stara_lozinka = filter_input(INPUT_GET, "stara_lozinka", FILTER_SANITIZE_STRING);
        $nova_lozinka = filter_input(INPUT_GET, "nova_lozinka", FILTER_SANITIZE_STRING);
        $potvrda = filter_input(INPUT_GET, "potvrda", FILTER_SANITIZE_STRING);

        $idA = $_SESSION["administrator"];
        $administrator = AdministratoriDB::get_administratora_po_idA($idA);

        $greska = "Greška: Greška pri promeni lozinke, proverite unesene podatke";
        if ($stara_lozinka != $administrator["lozinka"]) {
            Administrator::promena_lozinke($greska);
            return;
        }
        if ($nova_lozinka != $potvrda) {
            Administrator::promena_lozinke($greska);
            return;
        }
        if (!preg_match("/^[a-zA-Z]/", $nova_lozinka) || !preg_match("/[A-Z]/", $nova_lozinka) || !preg_match("/\d/", $nova_lozinka) || !preg_match("/[^a-zA-Z\d]/", $nova_lozinka)) {
            $greska = "Greška: Lozinka mora da sadrži minimalno 8 a maksimalno 16 karaktera; mora da sarži bar jedno veliko slovo cifru i specijalni karakter; mora da kreće slovom<br>";
            Administrator::promena_lozinke($greska);
            return;
        }

        $tmp = AdministratoriDB::promeni_lozinku($idA, $nova_lozinka);
        if (!$tmp) {
            $greska = "Greška: Greška pri promeni lozinke";
            Administrator::promena_lozinke($greska);
            return;
        }

        header("Location: routes.php?kontroler=administrator&akcija=izloguj_se");
    }

    public static function korisnici($greska = NULL) {
        $korisnici = KorisniciDB::get_sve_korisnike();
        include("view/administrator/header_administrator.php");
        include("view/administrator/korisnici.php");
        include("view/footer.php");
    }

    public static function izbrisi_korisnika() {
        $idK = filter_input(INPUT_GET, "idK", FILTER_SANITIZE_STRING);
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);
        $idS = $korisnik["idS"];
        if ($idS != NULL) {
            $slika = SlikeDB::get_sliku($idS);
            $putanja = $slika["putanja"];
            $tmp1 = unlink($putanja);
            $putanja_foldera = preg_replace("/profilna$", "", $putanja);
            $tmp2 = rmdir($putanja_foldera);
            $tmp3 = SlikeDB::izbrisi_sliku($idS);
            if (!$tmp1 || !$tmp2 || !$tmp3) {
                $greska = "Greška: Greška pri brisanju slike";
                Administrator::korisnici($greska);
                return;
            }
        }
        $tmp = KorisniciDB::izbrisi_korisnika($idK);
        if (!$tmp) {
            $greska = "Greška: Greška pri brisanju korisnika";
            Administrator::korisnici($greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=korisnici");
    }

    public static function odobri_korisnika() {
        $idK = filter_input(INPUT_GET, "idK", FILTER_SANITIZE_STRING);
        $tmp = KorisniciDB::odobri_korisnika($idK);
        if (!$tmp) {
            $greska = "Greška: Greška pri odobrenju korisnika";
            Administrator::korisnici($greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=korisnici");
    }

    public static function odbij_korisnika() {
        $idK = filter_input(INPUT_GET, "idK", FILTER_SANITIZE_STRING);
        $tmp = KorisniciDB::odbij_korisnika($idK);
        if (!$tmp) {
            $greska = "Greška: Greška pri odbijanju korisnika";
            Administrator::korisnici($greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=korisnici");
    }

    public static function korisnik_detalji($idK = NULL, $greska = NULL) {
        if ($idK == NULL) {
            $idK = filter_input(INPUT_GET, "idK", FILTER_SANITIZE_STRING);
        }
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);
        if ($korisnik["tip"] == 1) {
            $organizator = KorisniciDB::get_organizatora($idK);
        }
        if ($korisnik["idS"] != NULL) {
            $idS = $korisnik["idS"];
            $profilna = SlikeDB::get_sliku($idS)["putanja"];
        }

        include("view/administrator/header_administrator.php");
        include("view/administrator/korisnik_detalji.php");
        include("view/footer.php");
    }

    public static function promeni_profilnu() {
        $idK = filter_input(INPUT_POST, "idK", FILTER_SANITIZE_STRING);
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);

        if ($_FILES["slika"]["error"] != 0) {
            $greska = "Greška: Nije prosleđen fajl";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }
        $flag = getimagesize($_FILES["slika"]["tmp_name"]);
        if (!$flag) {
            $greska = "Greška: Prosleđeni fajl nije slika";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }
        // kada dohvatimo tip slike vraca IMAGETYPE_COUNT iz nekog razloga
        // TODO: popraviti to
        /* $a = getimagesize($_FILES["slika"]["tmp_name"]);
          $image_type = $a[2];

          if(!in_array($image_type , array(IMAGETYPE_PNG, IMAGETYPE_JPEG))) {
          $greska = "Greška: Slika nije u odgovarajućem formatu (PNG ili JPG)";
          Korisnik::profil($greska);
          return;
          } */
        list($width, $height) = getimagesize($_FILES["slika"]["tmp_name"]);
        if (!($width >= 100 && $width <= 300 && $height >= 100 && $height <= 300)) {
            $greska = "Greška: Slika nije zadovoljavajućih dimenzija (100x100px do 300x300px)";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }
        $slika = $_FILES["slika"]["tmp_name"];
        if (!is_uploaded_file($slika)) {
            $greska = "Greška: Greška pri menjanju profilne slike";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }

        if ($korisnik["idS"] != NULL) {
            $idS = $korisnik["idS"];
            $slika = SlikeDB::get_sliku($idS);
            $putanja = $slika["putanja"];
            unlink($putanja);
            SlikeDB::izbrisi_sliku($idS);
        }

        $slika = $_FILES["slika"]["tmp_name"];
        $putanja = "db_files/korisnici/" . $idK;
        if (!is_dir($putanja)) {
            mkdir($putanja);
        }
        $putanja .= "/profilna";
        $tmp = move_uploaded_file($slika, $putanja);
        if (!$tmp) {
            $greska = "Greška: Greška pri menjanju profilne slike";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }
        SlikeDB::dodaj_sliku($putanja);
        KorisniciDB::dodaj_sliku($idK);
        header("Location: routes.php?kontroler=administrator&akcija=korisnik_detalji&idK=" . $idK);
    }

    public static function azuriraj_podatke() {
        $idK = filter_input(INPUT_GET, "idK", FILTER_SANITIZE_STRING);
        $ime = filter_input(INPUT_GET, "ime", FILTER_SANITIZE_STRING);
        $prezime = filter_input(INPUT_GET, "prezime", FILTER_SANITIZE_STRING);
        $kor_ime = filter_input(INPUT_GET, "kor_ime", FILTER_SANITIZE_STRING);
        $telefon = filter_input(INPUT_GET, "telefon", FILTER_SANITIZE_STRING);
        $mejl = filter_input(INPUT_GET, "mejl", FILTER_SANITIZE_STRING);

        $korisnik = KorisniciDB::get_korisnika_po_kor_ime($kor_ime);
        if ($korisnik && $korisnik["idK"] != $idK) {
            $greska = "Greška: Korisničko ime je zauzeto";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }
        $korisnik = KorisniciDB::get_korisnika_po_mejl($mejl);
        if ($korisnik && $korisnik["idK"] != $idK) {
            $greska = "Greška: Već postoji nalog registrovan na toj e-mail adresi";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }
        $tmp = KorisniciDB::azuriraj_korisnika($idK, $ime, $prezime, $kor_ime, $telefon, $mejl);
        if (!$tmp) {
            $greska = "Greška: Neuspešno ažuriranje podataka";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=korisnik_detalji&idK=" . $idK);
    }

    public static function azuriraj_podatke_firme() {
        $idK = filter_input(INPUT_GET, "idK", FILTER_SANITIZE_STRING);
        $naziv = filter_input(INPUT_GET, "naziv", FILTER_SANITIZE_STRING);
        $maticni_broj = filter_input(INPUT_GET, "maticni_broj", FILTER_VALIDATE_INT);
        $drzava = filter_input(INPUT_GET, "drzava", FILTER_SANITIZE_STRING);
        $grad = filter_input(INPUT_GET, "grad", FILTER_SANITIZE_STRING);
        $postanski_broj = filter_input(INPUT_GET, "postanski_broj", FILTER_VALIDATE_INT);
        $ulica = filter_input(INPUT_GET, "ulica", FILTER_SANITIZE_STRING);
        $adresa_broj = filter_input(INPUT_GET, "adresa_broj", FILTER_SANITIZE_STRING);
        if (!$naziv) {
            KorisniciDB::dodaj_test("balasdio");
        }

        $tmp = KorisniciDB::azuriraj_firmu($idK, $naziv, $maticni_broj, $drzava, $grad, $postanski_broj, $ulica, $adresa_broj);
        if (!$tmp) {
            $greska = "Greška: Neuspešno ažuriranje podataka firme";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=korisnik_detalji&idK=" . $idK);
    }

    public static function promeni_lozinku_korisniku() {
        $idK = filter_input(INPUT_GET, "idK", FILTER_SANITIZE_STRING);
        $nova_lozinka = filter_input(INPUT_GET, "nova_lozinka", FILTER_SANITIZE_STRING);
        $potvrda = filter_input(INPUT_GET, "potvrda", FILTER_SANITIZE_STRING);

        $korisnik = KorisniciDB::get_korisnika_po_kor_ime($kor_ime);

        if (!preg_match("/^[a-zA-Z]/", $nova_lozinka) || !preg_match("/[A-Z]/", $nova_lozinka) || !preg_match("/\d/", $nova_lozinka) || !preg_match("/[^a-zA-Z\d]/", $nova_lozinka)) {
            $greska = "Greška: Lozinka mora da sadrži minimalno 8 a maksimalno 16 karaktera; mora da sarži bar jedno veliko slovo cifru i specijalni karakter; mora da kreće slovom<br>";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }

        if ($nova_lozinka != $potvrda) {
            $greska = "Greška: Greška pri promeni lozinke, proverite unesene podatke";
            Administrator::korisnik_detalji($idK, $greska);
            return;
        }

        $tmp = KorisniciDB::promeni_lozinku($idK, $nova_lozinka);
        if (!$tmp) {
            Korisnik::profil($greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=korisnik_detalji&idK=" . $idK);
    }

    public static function dodavanje_korisnika($greska = NULL) {
        include("view/administrator/header_administrator.php");
        include("view/administrator/dodavanje_korisnika.php");
        include("view/footer.php");
    }

    public static function dodaj_korisnika() {
        $tip = filter_input(INPUT_POST, "korisnik", FILTER_SANITIZE_STRING);

        $ime = filter_input(INPUT_POST, "ime", FILTER_SANITIZE_STRING);
        $prezime = filter_input(INPUT_POST, "prezime", FILTER_SANITIZE_STRING);
        $kor_ime = filter_input(INPUT_POST, "kor_ime", FILTER_SANITIZE_STRING);
        $lozinka = filter_input(INPUT_POST, "lozinka", FILTER_SANITIZE_STRING);
        $potvrda = filter_input(INPUT_POST, "potvrda", FILTER_SANITIZE_STRING);
        $telefon = filter_input(INPUT_POST, "telefon", FILTER_SANITIZE_STRING);
        $mejl = filter_input(INPUT_POST, "mejl", FILTER_SANITIZE_STRING);
        $slika = $_FILES["slika"];

        if ($tip == "organizator") {
            $naziv = filter_input(INPUT_POST, "naziv", FILTER_SANITIZE_STRING);
            $maticni_broj = filter_input(INPUT_POST, "maticni_broj", FILTER_VALIDATE_INT);
            $drzava = filter_input(INPUT_POST, "drzava", FILTER_SANITIZE_STRING);
            $grad = filter_input(INPUT_POST, "grad", FILTER_SANITIZE_STRING);
            $postanski_broj = filter_input(INPUT_POST, "postanski_broj", FILTER_VALIDATE_INT);
            $ulica = filter_input(INPUT_POST, "ulica", FILTER_SANITIZE_STRING);
            $adresa_broj = filter_input(INPUT_POST, "adresa_broj", FILTER_SANITIZE_STRING);
        }

        if ($ime == "" || $prezime == "" || $kor_ime == "" || $lozinka == "" || $potvrda == "" || $telefon == "" || $mejl == "") {
            $greska = "Greška: Sva polja označena zvezdicom su obavezna";
            Administrator::dodavanje_korisnika($greska);
            return;
        }
        if (!preg_match("/^[a-zA-Z]/", $lozinka) || !preg_match("/[A-Z]/", $lozinka) || !preg_match("/\d/", $lozinka) || !preg_match("/[^a-zA-Z\d]/", $lozinka)) {
            $greska = "Greška: Lozinka mora da sadrži minimalno 8 a maksimalno 16 karaktera; mora da sarži bar jedno veliko slovo cifru i specijalni karakter; mora da kreće slovom<br>";
            Administrator::dodavanje_korisnika($greska);
            return;
        }
        if ($lozinka != $potvrda) {
            $greska = "Greška: Potvrda lozinke mora biti ista kao lozinka";
            Administrator::dodavanje_korisnika($greska);
            return;
        }
        if (KorisniciDB::get_korisnika_po_kor_ime($kor_ime)) {
            $greska = "Greška: Korisničko ime je zauzeto<br>";
            Administrator::dodavanje_korisnika($greska);
            return;
        }
        if (KorisniciDB::get_korisnika_po_mejl($mejl)) {
            $greska = "Greška: Već postoji nalog registrovan na toj e-mail adresi<br>";
            Administrator::dodavanje_korisnika($greska);
            return;
        }
        if ($slika["error"] == 0) {
            $flag = getimagesize($slika["tmp_name"]);
            if (!$flag) {
                $greska = "Greška: Poslati fajl nije slika";
                Administrator::dodavanje_korisnika($greska);
                return;
            }
            list($width, $height) = getimagesize($slika["tmp_name"]);
            if (!($width >= 100 && $width <= 300 && $height >= 100 && $height <= 300)) {
                $greska = "Greška: Slika nije zadovoljavajućih dimenzija (100x100px do 300x300px)";
                Administrator::dodavanje_korisnika($greska);
                return;
            }
        }

        switch ($tip) {
            case "ucesnik":
                KorisniciDB::dodaj_ucesnika($ime, $prezime, $kor_ime, $lozinka, $telefon, $mejl);
                break;
            case "organizator":
                KorisniciDB::dodaj_organizatora($ime, $prezime, $kor_ime,
                        $lozinka, $telefon, $mejl, $naziv, $maticni_broj,
                        $drzava, $grad, $postanski_broj, $ulica, $adresa_broj);
                break;
            default:
                $greska = "Greška: Greška pri registraciji";
                Administrator::dodavanje_korisnika($greska);
                return;
        }

        if ($slika["error"] == 0) {
            $idK = KorisniciDB::get_korisnika_po_kor_ime($kor_ime)["idK"];
            $putanja = "db_files/korisnici/" . $idK;
            if (!is_dir($putanja)) {
                mkdir($putanja);
            }
            $putanja .= "/profilna";
            $tmp = move_uploaded_file($slika["tmp_name"], $putanja);
            if (!$tmp) {
                $greska = "Greška: Slike nije uspešno prebačena";
                Administrator::dodavanje_korisnika($greska);
                return;
            }
            SlikeDB::dodaj_sliku($putanja);
            KorisniciDB::dodaj_sliku($idK);
        }
        header("Location: routes.php?kontroler=administrator&akcija=dodavanje_korisnika");
    }

    public static function radionice($greska=NULL) {
        $odobrene_radionice = RadioniceDB::get_sve_odobrene_aktuelne_radionice();
        $neodobrene_radionice = RadioniceDB::get_sve_neodobrene_aktuelne_radionice();
        include("view/administrator/header_administrator.php");
        include("view/administrator/radionice.php");
        include("view/footer.php");
    }
    public static function odobri_radionicu() {
        
    }
    public static function izbrisi_radionicu() {
        
    }
    public static function odobri_ucesnika_u_organizatora() {
        
    }
    public static function radionica_detalji($greska=NULL) {
        $idR = filter_input(INPUT_GET, "idR", FILTER_VALIDATE_INT);
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);
        include("view/administrator/header_administrator.php");
        include("view/administrator/radionica_detalji.php");
        include("view/footer.php");
    }
}

?>