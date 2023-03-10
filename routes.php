<?php



$akcija = filter_input(INPUT_GET, "akcija", FILTER_SANITIZE_STRING);
if (!$akcija) {
    $akcija = filter_input(INPUT_POST, "akcija", FILTER_SANITIZE_STRING);
}
$kontroler = filter_input(INPUT_GET, "kontroler", FILTER_SANITIZE_STRING);
if (!$kontroler) {
    $kontroler = filter_input(INPUT_POST, "kontroler", FILTER_SANITIZE_STRING);
}
if (!$akcija || !$kontroler) {
    $kontroler = "gost";
    $akcija = "prijava";
}

function zovi($kontroler, $akcija) {
    require_once("controller/".$kontroler.".php");
    switch($kontroler) {
        case "gost":
            $kontroler = new Gost();
            break;
        case "korisnik":
            $kontroler = new Korisnik();
            break;
        case "organizator":
            $kontroler = new Organizator();
            break;
    }
    $kontroler::$akcija();
}

session_start();

$kontroleri = array("gost" => ["prijava", "registracija", 
                                "registracija_ucesnika", "registracija_organizatora",
                                "greska", "test", "zaboravljena_lozinka", "prijavi_se",
                                "resetuj_lozinku", "radionice", "filtriraj_radionice"],
                    "korisnik" => ["profil", "azuriraj_podatke", "promeni_lozinku", "radionice",
                                "filtriraj_radionice", "radionica_detalji", "promeni_profilnu",
                                "prijavi_radionicu", "komentarisi_radionicu", "lajkuj_radionicu",
                                "filtriraj_radionice", "svidjanja", "promeni_komentar",
                                "izbrisi_komentar", "povuci_svidjanje", "otkazi_prijavu",
                                "predlog_radionice", "predlozi_radionicu"],
                    "organizator" => ["radionice", "dodavanje_radionice",
                                "dodaj_radionicu", "izaberi_sablon", "filtriraj_radionice",
                                "uredjivanje_radionice", "prihvati_korisnika", "promena_lozinke",
                                "promeni_lozinku", "azuriraj_podatke_radionica", "azuriraj_glavnu_sliku",
                                "azuriraj_galeriju", "otkazi_radionicu"],
                    "administrator" => ["promena_lozinke", "korisnici", "radionice",
                                "prijava", "prijavi_se", "promeni_lozinku", "promeni_profilnu",
                                "azuriraj_podatke", "promeni_lozinku_korisniku", "korisnik_detalji",
                                "odobri_korisnika", "odbij_korisnika", "izbrisi_korisnika", "azuriraj_podatke_firme",
                                "dodavanje_korisnika", "dodaj_korisnika", "izbrisi_radionicu", "odobri_radionicu",
                                "odobri_ucesnika_u_organizatora", "radionica_detalji", "dodavanje_radionice", "dodaj_radionicu",
                                "azuriraj_podatke_radionice", "azuriraj_galeriju", "azuriraj_glavnu_sliku"]);


if (array_key_exists($kontroler, $kontroleri)) {
    if (in_array($akcija, $kontroleri[$kontroler])) {
        zovi($kontroler, $akcija);
    } else {
        zovi("gost", "prijava");
    }
} else {
    zovi("gost", "prijava");
}

?>