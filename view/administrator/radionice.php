<div class="row content">
    <div class="col-12">
        <div class="row">
            <div class="col-12 justify-content-center j-greska">
                <?php $greska ?>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 justify-content-center">
                <a class="link-secondary card-link" href="routes.php?kontroler=administrator&akcija=dodavanje_radionice">dodaj radionicu</a>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-8 col-md-4 text-center">
                <input type="radio" class="btn btn-check" name="radionica" id="prihvacene" checked onclick="togglePriPre(0)">
                <label class="btn j-orange" for="prihvacene">prihvaćene</label>

                <input type="radio" class="btn-check" name="radionica" id="predlozene" value="organizator" onclick="togglePriPre(1)">
                <label class="btn j-orange" for="predlozene">predložene</label>
            </div>
        </div>
        <br>
        <div class="row justify-content-center" id="prihvacene_div">
            <div class="col-10 text-center">
                <table class="table table-hover">
                    <tr>
                        <th>
                            naziv
                        </th>
                        <th>
                            datum
                        </th>
                        <th>
                            organizator
                        </th>
                        <th>
                            akcije
                        </th>
                    </tr>
                    <?php foreach ($odobrene_radionice as $radionica): ?>
                        <?php
                        $korisnik = KorisniciDB::get_korisnika_po_idK($radionica["idO"]);
                        $idK = $radionica["idO"];
                        ?>
                        <tr>
                            <td>
                                <a class="link-secondary" href="routes.php?kontroler=administrator&akcija=radionica_detalji&idR=<?= $radionica["idR"] ?>">
                                    <?= $radionica["naziv"] ?>
                                </a>
                            </td>
                            <td>
                                <?= $radionica["datum"] ?>
                            </td>
                            <td>
                                <?= KorisniciDB::get_korisnika_po_idK($idK)["kor_ime"] ?>
                            </td>
                            <td>
                                <form action="routes.php" method="get">
                                    <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                                    <input type="hidden" name="akcija" id="akcija" value="izbrisi_radionicu">
                                    <input type="hidden" name="idR" id="idR" value="<?= $radionica["idR"] ?>">
                                    <input type="submit" class="btn j-orange" value="izbriši">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <div class="row justify-content-center" id="predlozene_div" style="display:none">
            <div class="col-10 text-center">
                <table class="table table-hover">
                    <tr>
                        <th>
                            naziv
                        </th>
                        <th>
                            datum
                        </th>
                        <th>
                            predlagač
                        </th>
                        <th>
                            tip
                        </th>
                        <th>
                            akcije
                        </th>
                    </tr>
                    <?php foreach ($neodobrene_radionice as $radionica): ?>
                        <?php
                        $korisnik = KorisniciDB::get_korisnika_po_idK($radionica["idO"]);
                        ?>
                        <tr>
                            <td>
                                <a class="link-secondary" href="routes.php?kontroler=administrator&akcija=radionica_detalji&idR=<?= $radionica["idR"] ?>">
                                    <?= $radionica["naziv"] ?>
                                </a>
                            </td>
                            <td>
                                <?= $radionica["datum"] ?>
                            </td>
                            <td>
                                <?= $korisnik["kor_ime"] ?>
                            </td>
                            <td>
                                <?php
                                if ($korisnik["tip"] == 0) {
                                    echo "učesnik";
                                } else {
                                    echo "organizator";
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($korisnik["tip"] == 0): ?>
                                    <?php if (!PrijaveDB::korisnik_prijavljen_na_radionicu($korisnik["idK"])): ?>
                                        <form action="routes.php" method="get">
                                            <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                                            <input type="hidden" name="akcija" id="akcija" value="odobri_ucesnika_u_organizatora">
                                            <input type="hidden" name="idK" id="idK" value="<?= $korisnik["idK"] ?>">
                                            <input type="hidden" name="idR" id="idR" value="<?= $radionica["idR"] ?>">
                                            <input type="submit" class="btn j-orange" value="prihvati radionicu/organizatora">
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($korisnik["tip"] == 1): ?>
                                    <form action="routes.php" method="get">
                                        <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                                        <input type="hidden" name="akcija" id="akcija" value="odobri_radionicu">
                                        <input type="hidden" name="idR" id="idR" value="<?= $radionica["idR"] ?>">
                                        <input type="submit" class="btn j-orange" value="prihvati">
                                    </form>
                                <?php endif ?>
                                <form action="routes.php" method="get">
                                    <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                                    <input type="hidden" name="akcija" id="akcija" value="izbrisi_radionicu">
                                    <input type="hidden" name="idR" id="idR" value="<?= $radionica["idR"] ?>">
                                    <input type="submit" class="btn j-orange" value="izbriši">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>