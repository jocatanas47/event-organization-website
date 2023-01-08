<?php

class Baza {
    private static $instanca = NULL;
    public static function getInstanca() {
        $dsn = "mysql:host=localhost;dbname=projekat";
        $korisnicko_ime = "root";
        $lozinka = "";
        if (!isset(self::$instanca)) {
            try {
                self::$instanca = new PDO($dsn, $korisnicko_ime, $lozinka);
            } catch (PDOException $ex) {
                $greska = "Greska pri povezivanju sa bazom: ";
                $greska .= $ex->getMessage();
                include("view/greska.php");
                exit();
            }
        }
        return self::$instanca;
    }
}

?>