<?php

include("model/baza.php");

include("model/komentari_DB.php");
include("model/korisnici_DB.php");
include("model/prijave_DB.php");
include("model/radionice_DB.php");
include("model/slike_DB.php");
include("model/svidjanja_DB.php");

class Korisnik {
    
    public static function profil($greska=NULL) {
        $idK = $_SESSION["korisnik"];
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);
        $idS = $korisnik["idS"];
        if ($idS != NULL) {
            $profilna = SlikeDB::get_sliku($idS)["putanja"];
        } else {
            $profilna = False;
        }
        $radionice = RadioniceDB::get_sve_radionice_na_kojima_je_korisnik_prisustvovao($idK);
        $komentari = KomentariDB::get_komentare_korisnika($idK);
        $svidjanja = SvidjanjaDB::get_svidjanja_korisnika($idK);
        
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/profil.php");
        include("view/footer.php");
    }
    public static function promeni_profilnu() {
        // TODO: Nakon dodavanja komentara ne radi menjanje profilne??????
        // - do kesiranja je - ne znam kako to da popravim
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
            $greska = "Greška: Greška pri menjanju profilne slike";
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
        
        $slika = $_FILES["slika"]["tmp_name"];
        $putanja = "db_files/korisnici/".$idK;
        if (!is_dir($putanja)) {
            mkdir($putanja);
        }
        $putanja .= "/profilna";
        $tmp = move_uploaded_file($slika, $putanja);
        if (!$tmp) {
            $greska = "Greška: Greška pri menjanju profilne slike";
            Korisnik::profil($greska);
            return;
        }
        SlikeDB::dodaj_sliku($putanja);
        KorisniciDB::dodaj_sliku($idK);
        header("Location: routes.php?kontroler=korisnik&akcija=profil");
    }
    
    public static function azuriraj_podatke() {
        $ime = filter_input(INPUT_POST, "ime", FILTER_SANITIZE_STRING);
        $prezime = filter_input(INPUT_POST, "prezime", FILTER_SANITIZE_STRING);
        $kor_ime = filter_input(INPUT_POST, "kor_ime", FILTER_SANITIZE_STRING);
        $telefon = filter_input(INPUT_POST, "telefon", FILTER_SANITIZE_STRING);
        $mejl = filter_input(INPUT_POST, "mejl", FILTER_SANITIZE_STRING);
        
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
        if (!$tmp) {
            $greska = "Greška: Neuspešno ažuriranje podataka";
            Korisnik::profil($greska);
            return;
        }
        header("Location: routes.php?kontroler=korisnik&akcija=profil");
    }
    public static function promeni_lozinku() {
        $idK = $_SESSION["korisnik"];
        $korisnik = KorisniciDB::get_korisnika_po_idK($idK);
        
        $stara_lozinka = filter_input(INPUT_POST, "stara_lozinka", FILTER_SANITIZE_STRING);
        $nova_lozinka = filter_input(INPUT_POST, "nova_lozinka", FILTER_SANITIZE_STRING);
        $potvrda = filter_input(INPUT_POST, "potvrda", FILTER_SANITIZE_STRING);
        
        $lozinka;
        if ($korisnik["lozinka_promenjena"]) {
            $lozinka = $korisnik["lozinka_privremena"];
        } else {
            $lozinka = $korisnik["lozinka"];
        }
        
        if (!preg_match("/^[a-zA-Z]/", $nova_lozinka) || !preg_match("/[A-Z]/", $nova_lozinka)
                || !preg_match("/\d/", $nova_lozinka) || !preg_match("/[^a-zA-Z\d]/", $nova_lozinka)) {
            $greska = "Greška: Lozinka mora da sadrži minimalno 8 a maksimalno 16 karaktera; mora da sarži bar jedno veliko slovo cifru i specijalni karakter; mora da kreće slovom<br>";
            Korisnik::profil($greska);
            return;
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
        header("Location: routes.php?kontroler=gost&akcija=izloguj_se");
    }
    public static function promeni_komentar() {
        $komentar = filter_input(INPUT_POST, "komentar", FILTER_SANITIZE_STRING);
        $idKom = filter_input(INPUT_POST, "idKom", FILTER_SANITIZE_STRING);
        $tmp = KomentariDB::promeni_komentar($idKom, $komentar);
        if (!$tmp) {
            $greska = "Greška: Greška pri promeni komentara";
            Korisnik::profil($greska);
        }
        header("Location: routes.php?kontroler=korisnik&akcija=profil");
    }
    public static function izbrisi_komentar() {
        $idKom = filter_input(INPUT_POST, "idKom", FILTER_SANITIZE_STRING);
        $tmp = KomentariDB::izbrisi_komentar($idKom);
        if (!$tmp) {
            $greska = "Greška: Greška pri brisanju komentara";
            Korisnik::profil($greska);
        }
        header("Location: routes.php?kontroler=korisnik&akcija=profil");
    }
    public static function povuci_svidjanje() {
        $idS = filter_input(INPUT_POST, "idS", FILTER_SANITIZE_STRING);
        $tmp = SvidjanjaDB::povuci_svidjanje($idS);
        if (!$tmp) {
            $greska = "Greška: Greška pri povlačenju sviđanja";
            Korisnik::profil($greska);
        }
        header("Location: routes.php?kontroler=korisnik&akcija=profil");
    }
    
    public static function radionice($greska=NULL, $radionice=NULL) {
        if ($radionice == NULL) {
            $radionice = RadioniceDB::get_sve_aktuelne_radionice();
        }
        $idK = $_SESSION["korisnik"];
        $mesta = RadioniceDB::get_mesta();
        $prijavljene_radionice = RadioniceDB::get_sve_radionice_na_koje_je_korisnik_prijavljen($idK);
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/radionice.php");
        include("view/footer.php");
    }
    public static function filtriraj_radionice() {
        $mesto = filter_input(INPUT_POST, "mesto", FILTER_SANITIZE_STRING);
        $naziv = filter_input(INPUT_POST, "naziv", FILTER_SANITIZE_STRING);
        if ($mesto == "izaberite mesto" && $naziv == "") {
            Korisnik::radionice();
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
        Korisnik::radionice("", $radionice);
    }
    public static function otkazi_prijavu() {
        $idR = filter_input(INPUT_POST, "idR", FILTER_SANITIZE_STRING);
        $idK = $_SESSION["korisnik"];
        $tmp = PrijaveDB::izbrisi_prijavu($idR, $idK);
        if (!$tmp) {
            $greska = "Greška: Greška pri otkazivanju prijave";
            Korisnik::radionice($greska);
            return;
        }
        header("Location: routes.php?kontroler=korisnik&akcija=radionice");
    }
    
    public static function radionica_detalji($idR=NULL, $greska=NULL) {
        if ($idR == NULL) {
            $idR = filter_input(INPUT_GET, "idR", FILTER_SANITIZE_STRING);
        }
        $idK = $_SESSION["korisnik"];
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);
        $idG = $radionica["idG"];
        if ($idG != NULL) {
            $galerija = SlikeDB::get_sliku($idG);
        } else {
            $galerija = NULL;
        }
        $komentari = KomentariDB::get_komentare($idR);
        $broj_svidjanja = SvidjanjaDB::get_broj_lajkova_radionice($idR);
        $broj_komentara = KomentariDB::get_broj_komentara_radionice($idR);
        
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/radionice_detalji.php");
        include("view/footer.php");
    }
    public static function prijavi_radionicu() {
        $idR = filter_input(INPUT_POST, "idR", FILTER_SANITIZE_STRING);
        $idK = $_SESSION["korisnik"];
        KorisniciDB::dodaj_test("idR".$idR."-"."idK".$idK);
        $tmp = PrijaveDB::dodaj_prijavu($idR, $idK);
        if (!$tmp) {
            $greska = "Greška: Već ste prijavljeni na radionicu";
            Korisnik::radionica_detalji($idR, $greska);
            return;
        }
        header("Location: routes.php?kontroler=korisnik&akcija=radionica_detalji&idR=".$idR);
    }
    public static function svidjanja() {
        $idR = filter_input(INPUT_GET, "idR", FILTER_SANITIZE_STRING);
        $korisnici = KorisniciDB::get_korisnike_kojima_se_svidja_radionica($idR);
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/svidjanja.php");
        include("view/footer.php");
    }
    public static function lajkuj_radionicu() {
        $idR = filter_input(INPUT_POST, "idR", FILTER_SANITIZE_STRING);
        KorisniciDB::dodaj_test($idR);
        $idK = $_SESSION["korisnik"];
        SvidjanjaDB::lajkuj_radionicu($idK, $idR);
        header("Location: routes.php?kontroler=korisnik&akcija=radionica_detalji&idR=".$idR);
    }
    public static function komentarisi_radionicu() {
        $idK = $_SESSION["korisnik"];
        $komentar = filter_input(INPUT_POST, "komentar", FILTER_SANITIZE_STRING);
        $idR = filter_input(INPUT_POST, "idR", FILTER_SANITIZE_STRING);
        
        $tmp = KomentariDB::dodaj_komentar($idK, $idR, $komentar);
        if (!$tmp) {
            $greska = "Greška: Greška pri dodavanju komentara";
            Korisnik::radionica_detalji($idR, $greska);
            return;
        }
        header("Location: routes.php?kontroler=korisnik&akcija=radionica_detalji&idR=".$idR);
    }
    
    public static function predlog_radionice($greska=NULL) {
        include("view/korisnik/header_ucesnik.php");
        include("view/korisnik/predlog_radionice.php");
        include("view/footer.php");
    }
    public static function predlozi_radionicu() {
        $idK = $_SESSION["korisnik"];
        if (KorisniciDB::korisnik_predlozio_radionicu($idK)) {
            $greska = "Greška: Već ste predložili radionicu";
            Korisnik::predlog_radionice($greska);
        }
        
        $naziv = filter_input(INPUT_POST, "naziv", FILTER_SANITIZE_STRING);
        $datum = date("Y-m-d H:i:s", strtotime($_POST["datum"]));
        $mesto = filter_input(INPUT_POST, "mesto", FILTER_SANITIZE_STRING);
        $x_kor = filter_input(INPUT_POST, "x_kor", FILTER_VALIDATE_FLOAT);
        $y_kor = filter_input(INPUT_POST, "y_kor", FILTER_VALIDATE_FLOAT);
        $opis_kratki = filter_input(INPUT_POST, "opis_kratki", FILTER_SANITIZE_STRING);
        $opis_dugi = filter_input(INPUT_POST, "opis_dugi", FILTER_SANITIZE_STRING);
        $max_broj_posetilaca = filter_input(INPUT_POST, "max_broj_posetilaca", FILTER_VALIDATE_INT);
        $idO = $_SESSION["korisnik"];

        $glavna_slika = $_FILES["glavna_slika"];
        $galerija_slika = $_FILES["galerija_slika"];

        if ($glavna_slika["error"] != 0) {
            $greska = "Greška: Nije prosleđen fajl sa glavnom slikom";
            Korisnik::predlog_radionice($greska);
            return;
        }
        $flag = getimagesize($glavna_slika["tmp_name"]);
        if (!$flag) {
            $greska = "Greška: Prosleđeni fajl nije slika";
            Korisnik::predlog_radionice($greska);
            return;
        }
        // kada dohvatimo tip slike vraca IMAGETYPE_COUNT iz nekog razloga
        // TODO: popraviti to
        /* $a = getimagesize($glavna_slika["tmp_name"]);
          $image_type = $a[2];

          if(!in_array($image_type , array(IMAGETYPE_PNG, IMAGETYPE_JPEG))) {
          $greska = "Greška: Slika nije u odgovarajućem formatu (PNG ili JPG)";
          Organizator::dodavanje_radionice($greska);
          return;
          } */
        if (!is_uploaded_file($glavna_slika["tmp_name"])) {
            $greska = "Greška: Nije pronađen fajl";
            Korisnik::predlog_radionice($greska);
            return;
        }

        if ($galerija_slika["error"][0] == 0) {
            if (count($galerija_slika) > 5) {
                $greska = "Greska: Galerija može da sadrži maksimalno 5 slika";
                Korisnik::predlog_radionice($greska);
                return;
            }
            foreach ($galerija_slika["tmp_name"] as $slika) {
                $flag = getimagesize($slika);
                if (!$flag) {
                    $greska = "Greška: Prosleđeni fajl nije slika";
                    Korisnik::predlog_radionice($greska);
                    return;
                }
                // kada dohvatimo tip slike vraca IMAGETYPE_COUNT iz nekog razloga
                // TODO: popraviti to
                /* $a = getimagesize($slika["tmp_name"]);
                  $image_type = $a[2];

                  if(!in_array($image_type , array(IMAGETYPE_PNG, IMAGETYPE_JPEG))) {
                  $greska = "Greška: Slika nije u odgovarajućem formatu (PNG ili JPG)";
                  Organizator::dodavanje_radionice($greska);
                  return;
                  } */
                if (!is_uploaded_file($slika)) {
                    $greska = "Greška: Nije pronađen fajl";
                    Korisnik::predlog_radionice($greska);
                    return;
                }
            }
        }

        $glavna_slika = $glavna_slika["tmp_name"];
        $tmp = RadioniceDB::dodaj_radionicu($naziv, $datum, $mesto, $x_kor, $y_kor,
                        $opis_kratki, $opis_dugi, $max_broj_posetilaca, $idO);
        if (!$tmp) {
            $greska = "Greška: Greška pri dodavanju radionice";
            Korisnik::predlog_radionice($greska);
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
        KorisniciDB::korisnik_hoce_org($idK);
        header("Location: routes.php?kontroler=korisnik&akcija=profil");
    }
}

?>