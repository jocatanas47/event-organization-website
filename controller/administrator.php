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
        $kor_ime = filter_input(INPUT_POST, "kor_ime");
        $lozinka = filter_input(INPUT_POST, "lozinka");

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
        $stara_lozinka = filter_input(INPUT_POST, "stara_lozinka");
        $nova_lozinka = filter_input(INPUT_POST, "nova_lozinka");
        $potvrda = filter_input(INPUT_POST, "potvrda");

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
        $idK = filter_input(INPUT_POST, "idK");
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);
        $idS = $korisnik["idS"];
        if ($idS != NULL) {
            $slika = SlikeDB::get_sliku($idS);
            $putanja = $slika["putanja"];
            $tmp1 = unlink($putanja);
            $putanja_foldera = dirname($putanja);
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
        $idK = filter_input(INPUT_POST, "idK");
        $tmp = KorisniciDB::odobri_korisnika($idK);
        if (!$tmp) {
            $greska = "Greška: Greška pri odobrenju korisnika";
            Administrator::korisnici($greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=korisnici");
    }

    public static function odbij_korisnika() {
        $idK = filter_input(INPUT_POST, "idK");
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
            $idK = filter_input(INPUT_GET, "idK");
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
        $idK = filter_input(INPUT_POST, "idK");
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
        $idK = filter_input(INPUT_POST, "idK");
        $ime = filter_input(INPUT_POST, "ime");
        $prezime = filter_input(INPUT_POST, "prezime");
        $kor_ime = filter_input(INPUT_POST, "kor_ime");
        $telefon = filter_input(INPUT_POST, "telefon");
        $mejl = filter_input(INPUT_POST, "mejl");

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
        $idK = filter_input(INPUT_POST, "idK");
        $naziv = filter_input(INPUT_POST, "naziv");
        $maticni_broj = filter_input(INPUT_POST, "maticni_broj");
        $drzava = filter_input(INPUT_POST, "drzava");
        $grad = filter_input(INPUT_POST, "grad");
        $postanski_broj = filter_input(INPUT_POST, "postanski_broj");
        $ulica = filter_input(INPUT_POST, "ulica");
        $adresa_broj = filter_input(INPUT_POST, "adresa_broj");
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
        $idK = filter_input(INPUT_POST, "idK");
        $nova_lozinka = filter_input(INPUT_POST, "nova_lozinka");
        $potvrda = filter_input(INPUT_POST, "potvrda");

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
        $tip = filter_input(INPUT_POST, "korisnik");

        $ime = filter_input(INPUT_POST, "ime");
        $prezime = filter_input(INPUT_POST, "prezime");
        $kor_ime = filter_input(INPUT_POST, "kor_ime");
        $lozinka = filter_input(INPUT_POST, "lozinka");
        $potvrda = filter_input(INPUT_POST, "potvrda");
        $telefon = filter_input(INPUT_POST, "telefon");
        $mejl = filter_input(INPUT_POST, "mejl");
        $slika = $_FILES["slika"];

        if ($tip == "organizator") {
            $naziv = filter_input(INPUT_POST, "naziv");
            $maticni_broj = filter_input(INPUT_POST, "maticni_broj");
            $drzava = filter_input(INPUT_POST, "drzava");
            $grad = filter_input(INPUT_POST, "grad");
            $postanski_broj = filter_input(INPUT_POST, "postanski_broj");
            $ulica = filter_input(INPUT_POST, "ulica");
            $adresa_broj = filter_input(INPUT_POST, "adresa_broj");
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
        $idR = filter_input(INPUT_POST, "idR");
        $tmp = RadioniceDB::odobri_radionicu($idR);
        if (!$tmp) {
            $greska = "Greška: Greška pri odobrenju radionice";
            Administrator::radionice($greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=radionice");
    }
    public static function izbrisi_radionicu() {
        $idR = filter_input(INPUT_POST, "idR");
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);
        $idS = $radionica["idS"];
        $idG = $radionica["idG"];
        if ($idS != NULL) {
            $slika = SlikeDB::get_sliku($idS);
            $putanja = $slika["putanja"];
            $tmp1 = unlink($putanja);
            KorisniciDB::dodaj_test($putanja);
            $putanja_foldera = dirname($putanja);
            $tmp2 = rmdir($putanja_foldera);
            $tmp3 = SlikeDB::izbrisi_sliku($idS);
            if (!$tmp1 || !$tmp2 || !$tmp3) {
                $greska = "Greška: Greška pri brisanju slike";
                Administrator::radionice($greska);
                return;
            }
        }
        if ($idG != NULL) {
            $galerija = SlikeDB::get_sliku($idG);
            $putanja = $galerija["putanja"];
            $slike = glob($putanja . "/*");
            $tmp1 = true;
            foreach ($slike as $slika) {
                $tmp = unlink($slika);
                if (!$tmp) {
                    $tmp1 = false;
                }
            }
            $tmp2 = rmdir($putanja);
            $tmp3 = SlikeDB::izbrisi_sliku($idG);
            if (!$tmp1 || !$tmp2 || !$tmp3) {
                $greska = "Greška: Greška pri brisanju galerije";
                Administrator::radionice($greska);
                return;
            }
        }
        $tmp = RadioniceDB::izbrisi_radionicu($idR);
        if (!$tmp) {
            $greska = "Greška: Greška pri brisanju radionice";
            Administrator::radionice($greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=radionice");
    }
    public static function odobri_ucesnika_u_organizatora() {
        $idR = filter_input(INPUT_POST, "idR");
        $idK = filter_input(INPUT_POST, "idK");
        
        $tmp = KorisniciDB::odobri_ucesnika_u_organizatora($idK);
        if (!$tmp) {
            $greska = "Greška: Greška pri promeni tipa korisnika";
            Administrator::radionice($greska);
            return;
        }
        $tmp = RadioniceDB::odobri_radionicu($idR);
        if (!$tmp) {
            $greska = "Greška: Greška pri odobrenju radionice";
            Administrator::radionice($greska);
            return;
        }
        
        header("Location: routes.php?kontroler=administrator&akcija=radionice");
    }
    
    public static function radionica_detalji($idR=NULL, $greska=NULL) {
        if ($idR == NULL) {
            $idR = filter_input(INPUT_GET, "idR");
        }
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);
        include("view/administrator/header_administrator.php");
        include("view/administrator/radionica_detalji.php");
        include("view/footer.php");
    }
    public static function azuriraj_podatke_radionice() {
        $naziv = filter_input(INPUT_POST, "naziv");
        $datum = date("Y-m-d H:i:s", strtotime($_POST["datum"]));
        $mesto = filter_input(INPUT_POST, "mesto");
        $x_kor = filter_input(INPUT_POST, "x_kor");
        $y_kor = filter_input(INPUT_POST, "y_kor");
        $opis_kratki = filter_input(INPUT_POST, "opis_kratki");
        $opis_dugi = filter_input(INPUT_POST, "opis_dugi");
        $max_broj_posetilaca = filter_input(INPUT_POST, "max_broj_posetilaca");
        $idR = filter_input(INPUT_POST, "idR");

        $tmp = RadioniceDB::azuriraj_radionicu($idR, $naziv, $datum, $mesto, $x_kor, $y_kor, $opis_kratki, $opis_dugi, $max_broj_posetilaca);
        if (!$tmp) {
            $greska = "Greška: Greška pri ažuriranju radionice";
            Administrator::radionica_detalji($idR, $greska);
            return;
        }
        header("Location: routes.php?kontroler=administrator&akcija=radionica_detalji&idR=" . $idR);
    }
    public static function azuriraj_glavnu_sliku() {
        $idR = filter_input(INPUT_POST, "idR");
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);

        if ($_FILES["slika"]["error"] != 0) {
            $greska = "Greška: Nije prosleđen fajl";
            Administrator::radionica_detalji($idR, $greska);
            return;
        }
        $flag = getimagesize($_FILES["slika"]["tmp_name"]);
        if (!$flag) {
            $greska = "Greška: Prosleđeni fajl nije slika";
            Administrator::radionica_detalji($idR, $greska);
            return;
        }
        $slika = $_FILES["slika"]["tmp_name"];
        if (!is_uploaded_file($slika)) {
            $greska = "Greška: Greška pri menjanju glavne slike";
            Administrator::radionica_detalji($idR, $greska);
            return;
        }

        if ($radionica["idS"] != NULL) {
            $idS = $radionica["idS"];
            $slika = SlikeDB::get_sliku($idS);
            $putanja = $slika["putanja"];
            unlink($putanja);
            SlikeDB::izbrisi_sliku($idS);
        }
        $slika = $_FILES["slika"]["tmp_name"];
        $putanja = "db_files/radionice/glavna_slika/" . $idR;
        if (!is_dir($putanja)) {
            mkdir($putanja);
        }
        $putanja .= "/" . $idR;
        $tmp = move_uploaded_file($slika, $putanja);
        if (!$tmp) {
            $greska = "Greška: Greška pri menjanju glavne slike";
            Administrator::radionica_detalji($idR, $greska);
            return;
        }

        SlikeDB::dodaj_sliku($putanja);
        $db = Baza::getInstanca();
        $idS = $db->lastInsertId();
        RadioniceDB::dodaj_sliku($idR, $idS);

        header("Location: routes.php?kontroler=administrator&akcija=radionica_detalji&idR=" . $idR);
    }
    public static function azuriraj_galeriju() {
        $idR = filter_input(INPUT_POST, "idR");
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);

        if ($_FILES["galerija"]["error"][0] != 0) {
            $greska = "Greška: Nije prosleđen fajl";
            Administrator::radionica_detalji($idR, $greska);
            return;
        }
        $galerija = $_FILES["galerija"];
        if (count($galerija["tmp_name"]) > 5) {
            $greska = "Greska: Galerija može da sadrži maksimalno 5 slika";
            Administrator::radionica_detalji($idR, $greska);
            return;
        }
        foreach ($_FILES["galerija"]["tmp_name"] as $slika) {
            $flag = getimagesize($slika);
            if (!$flag) {
                $greska = "Greška: Prosleđeni fajl nije slika";
                Administrator::radionica_detalji($idR, $greska);
                return;
            }
            if (!is_uploaded_file($slika)) {
                $greska = "Greška: Greška pri menjanju galerije";
                Administrator::radionica_detalji($idR, $greska);
                return;
            }
        }

        if ($radionica["idG"] != NULL) {
            $idG = $radionica["idG"];
            $galerija = SlikeDB::get_sliku($idG);
            $putanja = $galerija["putanja"];
            $slike = glob($putanja . "/*");
            foreach ($slike as $slika) {
                unlink($slika);
            }
            rmdir($putanja);
            SlikeDB::izbrisi_sliku($idG);
        }
        $galerija = $_FILES["galerija"]["tmp_name"];
        $putanja = "db_files/radionice/galerija/" . $idR;
        if (!is_dir($putanja)) {
            mkdir($putanja);
        }

        $i = 0;
        foreach ($galerija as $slika) {
            move_uploaded_file($slika, $putanja . "/" . $i);
            $i++;
        }
        SlikeDB::dodaj_sliku($putanja);
        $db = Baza::getInstanca();
        $idG = $db->lastInsertId();
        $tmp = RadioniceDB::dodaj_galeriju($idR, $idG);
        if (!$tmp) {
            $greska = "Greška: Greška pri menjanju galerije";
            Administrator::radionica_detalji($idR, $greska);
            return;
        }

        header("Location: routes.php?kontroler=administrator&akcija=radionica_detalji&idR=" . $idR);
    }
    
    public static function dodavanje_radionice($greska=NULL) {
        include("view/administrator/header_administrator.php");
        include("view/administrator/dodavanje_radionice.php");
        include("view/footer.php");
    }
    public static function dodaj_radionicu() {
        $naziv = filter_input(INPUT_POST, "naziv");
        $datum = date("Y-m-d H:i:s", strtotime($_POST["datum"]));
        $mesto = filter_input(INPUT_POST, "mesto");
        $x_kor = filter_input(INPUT_POST, "x_kor");
        $y_kor = filter_input(INPUT_POST, "y_kor");
        $opis_kratki = filter_input(INPUT_POST, "opis_kratki");
        $opis_dugi = filter_input(INPUT_POST, "opis_dugi");
        $max_broj_posetilaca = filter_input(INPUT_POST, "max_broj_posetilaca");
        $idO = -1;

        $glavna_slika = $_FILES["glavna_slika"];
        $galerija_slika = $_FILES["galerija_slika"];

        if ($glavna_slika["error"] != 0) {
            $greska = "Greška: Nije prosleđen fajl sa glavnom slikom";
            Administrator::dodavanje_radionice($greska);
            return;
        }
        $flag = getimagesize($glavna_slika["tmp_name"]);
        if (!$flag) {
            $greska = "Greška: Prosleđeni fajl nije slika";
            Administrator::dodavanje_radionice($greska);
            return;
        }
        if (!is_uploaded_file($glavna_slika["tmp_name"])) {
            $greska = "Greška: Nije pronađen fajl";
            Administrator::dodavanje_radionice($greska);
            return;
        }

        if ($galerija_slika["error"][0] == 0) {
            if (count($galerija_slika["tmp_name"]) > 5) {
                $greska = "Greska: Galerija može da sadrži maksimalno 5 slika";
                Administrator::dodavanje_radionice($greska);
                return;
            }
            foreach ($galerija_slika["tmp_name"] as $slika) {
                $flag = getimagesize($slika);
                if (!$flag) {
                    $greska = "Greška: Prosleđeni fajl nije slika";
                    Administrator::dodavanje_radionice($greska);
                    return;
                }
                if (!is_uploaded_file($slika)) {
                    $greska = "Greška: Nije pronađen fajl";
                    Administrator::dodavanje_radionice($greska);
                    return;
                }
            }
        }

        $glavna_slika = $glavna_slika["tmp_name"];
        $tmp = RadioniceDB::dodaj_radionicu($naziv, $datum, $mesto, $x_kor, $y_kor,
                        $opis_kratki, $opis_dugi, $max_broj_posetilaca, $idO);
        if (!$tmp) {
            $greska = "Greška: Greška pri dodavanju radionice";
            Administrator::dodavanje_radionice($greska);
            return;
        }
        $db = Baza::getInstanca();
        $idR = $db->lastInsertId();
        $putanja_slika = "db_files/radionice/glavna_slika/" . $idR;
        if (!is_dir($putanja_slika)) {
            mkdir($putanja_slika);
        }

        move_uploaded_file($glavna_slika, $putanja_slika . "/" . $idR);
        SlikeDB::dodaj_sliku($putanja_slika . "/" . $idR);
        $idS = $db->lastInsertId();
        RadioniceDB::dodaj_sliku($idR, $idS);

        if ($galerija_slika["error"][0] == 0) {
            $galerija_slika = $galerija_slika["tmp_name"];
            $putanja_galerija = "db_files/radionice/galerija/" . $idR;
            if (!is_dir($putanja_galerija)) {
                mkdir($putanja_galerija);
            }
            $i = 0;
            foreach ($galerija_slika as $slika) {
                move_uploaded_file($slika, $putanja_galerija . "/" . $i);
                $i++;
            }
            SlikeDB::dodaj_sliku($putanja_galerija);
            $idG = $db->lastInsertId();
            RadioniceDB::dodaj_galeriju($idR, $idG);
        }

        header("Location: routes.php?kontroler=administrator&akcija=dodavanje_radionice");
    }
}

?>