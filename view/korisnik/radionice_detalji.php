<div class="row content">
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-8 text-center j-greska">
                <?= $greska ?>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-8 text-center">
                <h3><?= $radionica["naziv"] ?></h3>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-10 col-md-5 card">
                <?php if ($galerija != NULL): ?>
                    <div class="row justify-content-center">
                        <?php foreach (glob($galerija["putanja"] . "/*") as $slika): ?>
                            <div class="col-8 col-md-4">
                                <img class="img-fluid" src=<?= $slika ?>>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <br>
                <?php endif; ?>
                <div>
                    <?= $radionica["opis_dugi"] ?>
                </div>

            </div>
            <div class="col-8 col-md-4">
                <div id="mapa">
                </div>
            </div>
        </div>
        <br>
        <?php if (PrijaveDB::get_broj_prijavljenih_na_radionicu($idR) < $radionica["max_broj_posetilaca"]): ?>
            <div class="row justify-content-center">
                <div class="col-6 text-center">
                    <form method="get" action="routes.php">
                        <input type="hidden" id="kontroler" name="kontroler" value="korisnik">
                        <input type="hidden" id="akcija" name="akcija" value="prijavi_radionicu">
                        <input type="hidden" id="idR" name="idR" value="<?= $radionica["idR"] ?>">
                        <input class="btn j-btn j-orange" type="submit" value="prijavi se">
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-10 col-md-6 card">
                <div class="row text-center">
                    <div class="col-2 card j-pink">
                        <div class="row">
                            <div class="col-6">
                                <a href="routes.php?kontroler=korisnik&akcija=svidjanja&idR=<?= $radionica["idR"] ?>"><img src="resources/heart.svg"></a><?= $broj_svidjanja ?>
                            </div>
                            <div class="col-6">
                                <img src="resources/chat.svg"><?= $broj_komentara ?>
                            </div>

                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <?php if (RadioniceDB::korisnik_bio_na_radionici($idK, $idR)): ?>
                            <form>
                                <input type="hidden" id="kontroler" name="kontroler" value="korisnik">
                                <input type="hidden" id="akcija" name="akcija" value="lajkuj_radionicu">
                                <input type="hidden" name="idR" id="idR" value="<?= $idR ?>">
                                <div class="col-12">
                                    <input type="submit" class="btn j-pink" value="sviđa mi se">
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php foreach ($komentari as $komentar): ?>
                    <?php
                    $korisnik = KorisniciDB::get_korisnika_po_idK($komentar["idKor"]);
                    $profilna;
                    if ($korisnik["idS"] != NULL):
                        $profilna = SlikeDB::get_sliku($korisnik["idS"])["putanja"];
                    else:
                        $profilna = "resources/avatar_default.png";
                    endif;
                    ?>
                    <div class="row">
                        <div class="col-3">
                        </div>
                        <div class="col-8">
                            <?= $korisnik["kor_ime"] ?> <?= $komentar["datum"] ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <img class="img-fluid img-thumbnail" src="<?= $profilna ?>">
                        </div>
                        <div class="col-8 card j-gray">
                            <?= $komentar["komentar"] ?>
                        </div>
                    </div>
                    <br>
                <?php endforeach; ?>

                <form name="form1" method="get" action="routes.php">
                    <input type="hidden" id="kontroler" name="kontroler" value="korisnik">
                    <input type="hidden" id="akcija" name="akcija" value="komentarisi_radionicu">
                    <input type="hidden" id="idR" name="idR" value="<?= $idR ?>">
                    <div class="row border-top p-2">
                        <div class="col-3">
                            komentar:
                        </div>
                        <div class="col-6">
                            <input type="text" id="komentar" name="komentar" required>
                        </div>
                        <div class="col-3">
                            <input class="btn j-orange" type="submit" value="pošalji">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
    </div>
</div>
<script>
    var map = L.map('mapa').setView([<?= $radionica["y_kor"] ?>, <?= $radionica["x_kor"] ?>], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var marker = new L.Marker([<?= $radionica["y_kor"] ?>, <?= $radionica["x_kor"] ?>]);
    marker.addTo(map);
</script>