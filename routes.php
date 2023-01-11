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
    }
    $kontroler::$akcija();
}

session_start();

$kontroleri = array("gost" => ["prijava", "registracija", 
                                "registracija_ucesnika", "registracija_organizatora",
                                "greska", "test", "zaboravljena_lozinka", "prijavi_se",
                                "resetuj_lozinku", "radionice", "filtriraj_radionice"],
                    "korisnik" => ["profil", "azuriraj_podatke", "promeni_lozinku", "radionice",
                                    "filtriraj_radionice", "radionica_detalji", "promeni_profilnu"],
                    "organizator" => ["radionice", "moje_radionice", "dodavanje_radionice",
                                "dodaj_radionicu"]);


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