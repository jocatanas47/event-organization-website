<?php

include("model/baza.php");
include("model/korisnici_DB.php");
include("model/slike_DB.php");
include("model/radionice_DB.php");

class Organizator {
    
    public static function radionice() {
        
    }
    public static function moje_radionice() {
        
    }
    public static function dodavanje_radionice($greska=NULL) {
        include("view/organizator/header_organizator.php");
        include("view/organizator/dodavanje_radionice.php");
        include("view/footer.php");
    }
    public static function dodaj_radionicu() {
        // TODO: Dodati provere ima ih puno
        $naziv = filter_input(INPUT_POST, "naziv", FILTER_SANITIZE_STRING);
        $datum = date("Y-m-d H:i:s", strtotime($_POST["datum"]));
        $mesto = filter_input(INPUT_POST, "mesto", FILTER_SANITIZE_STRING);
        $opis_kratki = filter_input(INPUT_POST, "opis_kratki", FILTER_SANITIZE_STRING);
        $opis_dugi = filter_input(INPUT_POST, "opis_dugi", FILTER_SANITIZE_STRING);
        $max_broj_posetilaca = filter_input(INPUT_POST, "max_broj_posetilaca", FILTER_SANITIZE_STRING);
        $idO = $_SESSION["korisnik"];
        
        $glavna_slika = $_FILES["glavna_slika"]["tmp_name"];
        $galerija_slika = $_FILES["galerija_slika"]["tmp_name"];
        
        $greska = "";
        if (count($galerija_slika) > 5) {
            $greska = "Greska: Galerija može da sadrži maksimalno 5 slika";
            Organizator::dodavanje_radionice($greska);
            return;
        }
        
        Radionice_DB::dodaj_radionicu($naziv, $datum, $mesto, $opis_kratki, $opis_dugi, $max_broj_posetilaca, $idO);
        $db = Baza::getInstanca();
        $idR = $db->lastInsertId();
        
        $putanja_slika = "db_files/radionice/glavna_slika/".$idR;
        mkdir($putanja_slika);
        move_uploaded_file($glavna_slika, $putanja_slika."/".$idR);
        SlikeDB::dodaj_sliku($putanja_slika."/".$idR);
        $idS = $db->lastInsertId();
        Radionice_DB::dodaj_sliku($idR, $idS);
        
        $putanja_galerija = "db_files/radionice/galerija/".$idR;
        mkdir($putanja_galerija);
        $i = 0;
        foreach ($galerija_slika as $slika) {
            move_uploaded_file($slika, $putanja_galerija."/".$i);
            $i++;
        }
        SlikeDB::dodaj_sliku($putanja_galerija);
        $idG = $db->lastInsertId();
        Radionice_DB::dodaj_galeriju($idR, $idG);
        
        Organizator::dodavanje_radionice();
    }
}

?>