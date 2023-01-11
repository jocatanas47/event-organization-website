<?php

class SlikeDB {
    public static function dodaj_sliku($putanja) {
        $db = Baza::getInstanca();
        $upit = "INSERT INTO slike"
                . " (putanja, datum)"
                . " VALUES (:putanja, :datum)";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":putanja", $putanja);
        $iskaz->bindValue(":datum", date('Y-m-d H:i:s'));
        $tmp = $iskaz->execute();
        $iskaz->closeCursor();
        return $tmp;
    }
    public static function get_sliku($idS) {
        $db = Baza::getInstanca();
        $upit = "SELECT * FROM slike WHERE idS=:idS";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idS", $idS);
        $iskaz->execute();
        $slika = $iskaz->fetch();
        $iskaz->closeCursor();
        return $slika;
    }
    public static function izbrisi_sliku($idS) {
        $db = Baza::getInstanca();
        $upit = "DELETE FROM slike WHERE idS=:idS";
        $iskaz = $db->prepare($upit);
        $iskaz->bindValue(":idS", $idS);
        $iskaz->execute();
        $slika = $iskaz->fetch();
        $iskaz->closeCursor();
        return $slika;
    }
}

?>