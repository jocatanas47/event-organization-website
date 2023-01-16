<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require("PHPMailer/Exception.php");
require("PHPMailer/PHPMailer.php");
require("PHPMailer/SMTP.php");

include("model/baza.php");

include("model/korisnici_DB.php");
include("model/prijave_DB.php");
include("model/radionice_DB.php");
include("model/slike_DB.php");

class Organizator {
    // TODO staviti ogranicenje svuda na datetime input da moze da bude samo u buducnosti

    public static function promena_lozinke($greska = NULL) {
        include("view/organizator/header_organizator.php");
        include("view/organizator/promena_lozinke.php");
        include("view/footer.php");
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

        if (!preg_match("/^[a-zA-Z]/", $nova_lozinka) || !preg_match("/[A-Z]/", $nova_lozinka) || !preg_match("/\d/", $nova_lozinka) || !preg_match("/[^a-zA-Z\d]/", $nova_lozinka)) {
            $greska = "Greška: Lozinka mora da sadrži minimalno 8 a maksimalno 16 karaktera; mora da sarži bar jedno veliko slovo cifru i specijalni karakter; mora da kreće slovom<br>";
            Organizator::promena_lozinke($greska);
            return;
        }

        $greska = "Greška: Greška pri promeni lozinke, proverite unesene podatke";
        if ($stara_lozinka != $lozinka) {
            Organizator::promena_lozinke($greska);
            return;
        }
        if ($nova_lozinka != $potvrda) {
            Organizator::promena_lozinke($greska);
            return;
        }

        $tmp = KorisniciDB::promeni_lozinku($idK, $nova_lozinka);
        if (!$tmp) {
            Organizator::promena_lozinke($greska);
            return;
        }
        KorisniciDB::dodaj_test($nova_lozinka);

        header("Location: routes.php?kontroler=gost&akcija=izloguj_se");
    }

    public static function radionice($radionice = NULL) {
        $idO = $_SESSION["korisnik"];
        if ($radionice == NULL) {
            $radionice = RadioniceDB::get_sve_radionice();
        }
        $mesta = RadioniceDB::get_mesta();
        include("view/organizator/header_organizator.php");
        include("view/organizator/radionice.php");
        include("view/footer.php");
    }

    public static function filtriraj_radionice() {
        $mesto = filter_input(INPUT_GET, "mesto", FILTER_SANITIZE_STRING);
        $naziv = filter_input(INPUT_GET, "naziv", FILTER_SANITIZE_STRING);
        if ($mesto == "izaberite mesto" && $naziv == "") {
            Organizator::radionice();
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
        Organizator::radionice($radionice);
    }

    public static function uredjivanje_radionice($idR = NULL, $greska = NULL) {
        if ($idR == NULL) {
            $idR = filter_input(INPUT_GET, "idR", FILTER_SANITIZE_STRING);
        }
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);
        $prijave = PrijaveDB::get_sve_neodobrene_prijave($idR);
        include("view/organizator/header_organizator.php");
        include("view/organizator/uredjivanje_radionice.php");
        include("view/footer.php");
    }
    public static function azuriraj_podatke_radionica() {
        $naziv = filter_input(INPUT_GET, "naziv", FILTER_SANITIZE_STRING);
        $datum = date("Y-m-d H:i:s", strtotime($_GET["datum"]));
        $mesto = filter_input(INPUT_GET, "mesto", FILTER_SANITIZE_STRING);
        $x_kor = filter_input(INPUT_GET, "x_kor", FILTER_VALIDATE_FLOAT);
        $y_kor = filter_input(INPUT_GET, "y_kor", FILTER_VALIDATE_FLOAT);
        $opis_kratki = filter_input(INPUT_GET, "opis_kratki", FILTER_SANITIZE_STRING);
        $opis_dugi = filter_input(INPUT_GET, "opis_dugi", FILTER_SANITIZE_STRING);
        $max_broj_posetilaca = filter_input(INPUT_GET, "max_broj_posetilaca", FILTER_VALIDATE_INT);
        $idR = filter_input(INPUT_GET, "idR", FILTER_VALIDATE_INT);

        $tmp = RadioniceDB::azuriraj_radionicu($idR, $naziv, $datum, $mesto, $x_kor, $y_kor, $opis_kratki, $opis_dugi, $max_broj_posetilaca);
        if (!$tmp) {
            $greska = "Greška: Greška pri ažuriranju radionice";
            Organizator::uredjivanje_radionice($idR, $greska);
            return;
        }
        header("Location: routes.php?kontroler=organizator&akcija=uredjivanje_radionice&idR=" . $idR);
    }
    public static function azuriraj_glavnu_sliku() {
        // TODO: Nakon dodavanja komentara ne radi menjanje profilne??????
        // - do kesiranja je - ne znam kako to da popravim
        $idR = filter_input(INPUT_POST, "idR", FILTER_VALIDATE_INT);
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);

        if ($_FILES["slika"]["error"] != 0) {
            $greska = "Greška: Nije prosleđen fajl";
            Organizator::uredjivanje_radionice($idR, $greska);
            return;
        }
        $flag = getimagesize($_FILES["slika"]["tmp_name"]);
        if (!$flag) {
            $greska = "Greška: Prosleđeni fajl nije slika";
            Organizator::uredjivanje_radionice($idR, $greska);
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
        $slika = $_FILES["slika"]["tmp_name"];
        if (!is_uploaded_file($slika)) {
            $greska = "Greška: Greška pri menjanju glavne slike";
            Organizator::uredjivanje_radionice($idR, $greska);
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
            Organizator::uredjivanje_radionice($idR, $greska);
            return;
        }

        SlikeDB::dodaj_sliku($putanja);
        $db = Baza::getInstanca();
        $idS = $db->lastInsertId();
        RadioniceDB::dodaj_sliku($idR, $idS);

        header("Location: routes.php?kontroler=organizator&akcija=uredjivanje_radionice&idR=" . $idR);
    }
    public static function azuriraj_galeriju() {
        $idR = filter_input(INPUT_POST, "idR", FILTER_VALIDATE_INT);
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);

        if ($_FILES["galerija"]["error"][0] != 0) {
            $greska = "Greška: Nije prosleđen fajl";
            Organizator::uredjivanje_radionice($idR, $greska);
            return;
        }
        $galerija = $_FILES["galerija"];
        if (count($galerija["tmp_name"]) > 5) {
            $greska = "Greska: Galerija može da sadrži maksimalno 5 slika";
            Organizator::uredjivanje_radionice($idR, $greska);
            return;
        }
        foreach ($_FILES["galerija"]["tmp_name"] as $slika) {
            $flag = getimagesize($slika);
            if (!$flag) {
                $greska = "Greška: Prosleđeni fajl nije slika";
                Organizator::uredjivanje_radionice($idR, $greska);
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
            if (!is_uploaded_file($slika)) {
                $greska = "Greška: Greška pri menjanju galerije";
                Organizator::uredjivanje_radionice($idR, $greska);
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
            Organizator::uredjivanje_radionice($idR, $greska);
            return;
        }

        header("Location: routes.php?kontroler=organizator&akcija=uredjivanje_radionice&idR=" . $idR);
    }
    public static function prihvati_korisnika() {
        $idR = filter_input(INPUT_GET, "idR", FILTER_SANITIZE_STRING);
        $idK = filter_input(INPUT_GET, "idK", FILTER_SANITIZE_STRING);
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);
        $broj_prijavljenih = PrijaveDB::get_broj_prijavljenih_na_radionicu($idR);
        if ($broj_prijavljenih >= $radionica["max_broj_posetilaca"]) {
            $greska = "Greška: Radionica je puna";
            Organizator::uredjivanje_radionice($idR, $greska);
            return;
        }
        $tmp = PrijaveDB::odobri_prijavu($idK, $idR);
        if (!$tmp) {
            $greska = "Greška: Greška prilikom obrade zahteva";
            Organizator::uredjivanje_radionice($idR, $greska);
            return;
        }
        header("Location: routes.php?kontroler=organizator&akcija=uredjivanje_radionice&idR=" . $idR);
    }
    public static function otkazi_radionicu() {
        $idR = filter_input(INPUT_GET, "idR", FILTER_SANITIZE_STRING);
        $korisnici = KorisniciDB::get_korisnike_koji_su_prijavljeni_na_radionicu($idR);
        
        $radionica = RadioniceDB::get_radionicu_po_idR($idR);
        $tmp = RadioniceDB::otkazi_radionicu($idR);
        if (!$tmp) {
            $greska = "Greška: Greška pri otkazivanju radionice";
            uredjivanje_radionice($idR, $greska);
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
        foreach ($korisnici as $korisnik) {
            $mejl = $korisnik["mejl"];
            $mail->addAddress($mejl);
        }
        $mail->isHTML(true);
        $mail->Subject = "Otkazivanje radionice";
        $mail->Body = "Radionica ".$radionica["naziv"]." zakazana za ".$radionica["datum"]." se otkazuje.";
        $mail->send();
        header("Location: routes.php?kontroler=organizator&akcija=radionice");
    }

    public static function dodavanje_radionice($greska = NULL, $idR = NULL) {
        $idO = $_SESSION["korisnik"];
        $radionice = RadioniceDB::get_sve_radionice_organizatora($idO);
        if ($idR == NULL) {
            $radionica = ["naziv" => "", "mesto" => "", "x_kor" => "", "y_kor" => "",
                "opis_kratki" => "", "opis_dugi" => "", "max_broj_posetilaca" => ""];
        } else {
            $radionica = RadioniceDB::get_radionicu_po_idR($idR);
        }
        include("view/organizator/header_organizator.php");
        include("view/organizator/dodavanje_radionice.php");
        include("view/footer.php");
    }
    public static function izaberi_sablon() {
        $idR = filter_input(INPUT_GET, "sablon", FILTER_VALIDATE_INT);
        if ($idR == -1) {
            header("Location: routes.php?kontroler=organizator&akcija=dodavanje_radionice");
            return;
        }
        Organizator::dodavanje_radionice("", $idR);
    }

    public static function dodaj_radionicu() {
        // TODO: kako se povecava maksimalni upload
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
            Organizator::dodavanje_radionice($greska);
            return;
        }
        $flag = getimagesize($glavna_slika["tmp_name"]);
        if (!$flag) {
            $greska = "Greška: Prosleđeni fajl nije slika";
            Organizator::dodavanje_radionice($greska);
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
            Organizator::dodavanje_radionice($greska);
            return;
        }

        if ($galerija_slika["error"][0] == 0) {
            if (count($galerija_slika["tmp_name"]) > 5) {
                $greska = "Greska: Galerija može da sadrži maksimalno 5 slika";
                Organizator::dodavanje_radionice($greska);
                return;
            }
            foreach ($galerija_slika["tmp_name"] as $slika) {
                $flag = getimagesize($slika);
                if (!$flag) {
                    $greska = "Greška: Prosleđeni fajl nije slika";
                    Organizator::dodavanje_radionice($greska);
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
                    Organizator::dodavanje_radionice($greska);
                    return;
                }
            }
        }

        $glavna_slika = $glavna_slika["tmp_name"];
        $tmp = RadioniceDB::dodaj_radionicu($naziv, $datum, $mesto, $x_kor, $y_kor,
                        $opis_kratki, $opis_dugi, $max_broj_posetilaca, $idO);
        if (!$tmp) {
            $greska = "Greška: Greška pri dodavanju radionice";
            Organizator::dodavanje_radionice($greska);
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

        header("Location: routes.php?kontroler=organizator&akcija=dodavanje_radionice");
    }

}

?>