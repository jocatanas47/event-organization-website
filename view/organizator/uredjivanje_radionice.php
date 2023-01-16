<div class="row content">
    <div class="col-12">
        <br>
        <div class="row justify-content-center">
            <div class="col-8 text-center j-greska">
                <?= $greska ?>
            </div>
        </div>
        <br>
        <div class="row justify-content-center">
            <div class="col-8 col-md-4 text-center">
                <input type="radio" class="btn btn-check" name="dugme" id="radionica" checked onclick="toggleRadPri(0)">
                <label class="btn j-orange" for="radionica">radionica</label>

                <input type="radio" class="btn btn-check" name="dugme" id="prijave" onclick="toggleRadPri(1)">
                <label class="btn j-orange" for="prijave">prijave</label>
            </div>
        </div>
        <br>
        <div class="row" id="radionica_div">
            <div class="col-12">
                <div class="row justify-content-center">
                    <div class="col-10 col-md-5">
                        <form name="form2" action="routes.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="akcija" id="akcija" value="azuriraj_glavnu_sliku">
                            <input type="hidden" name="kontroler" id="kontroler" value="organizator">
                            <input type="hidden" name="idR" id="idR" value="<?= $idR ?>">
                            <div class="row">
                                <div class="col-6">
                                    <input type="file" name="slika" required>
                                </div>
                                <div class="col-6">
                                    <input type="submit" class="btn j-orange" value="ažuriraj glavnu sliku">
                                </div>
                            </div>
                        </form> 
                        <form name="form3" action="routes.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="akcija" id="akcija" value="azuriraj_galeriju">
                            <input type="hidden" name="kontroler" id="kontroler" value="organizator">
                            <input type="hidden" name="idR" id="idR" value="<?= $idR ?>">
                            <div class="row">
                                <div class="col-6">
                                    <input type="file" name="galerija[]" required multiple>
                                </div>
                                <div class="col-6">
                                    <input type="submit" class="btn j-orange" value="ažuriraj galeriju">
                                </div>
                            </div>
                        </form>
                        <form name="form4" action="routes.php" method="post">
                            <input type="hidden" name="akcija" id="akcija" value="otkazi_radionicu">
                            <input type="hidden" name="kontroler" id="kontroler" value="organizator">
                            <input type="hidden" name="idR" id="idR" value="<?= $idR ?>">
                            <div class="row">
                                <div class="col-12">
                                    <input type="submit" class="btn j-orange" value="otkaži radionicu">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-10 col-md-5 align-bottom">
                        <form name="form1" action="routes.php" method="post">
                            <input type="hidden" name="akcija" id="akcija" value="azuriraj_podatke_radionica">
                            <input type="hidden" name="kontroler" id="kontroler" value="organizator">
                            <input type="hidden" name="idR" id="idR" value="<?= $idR ?>">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-hover">
                                        <tr>
                                            <td>naziv:</td>
                                            <td><input type="text" name="naziv" required value=<?= $radionica["naziv"] ?>></td>
                                        </tr>
                                        <tr>
                                            <td>datum:</td>
                                            <td><input type="datetime-local" name="datum" value=<?= date("Y-m-d\TH:i:s", strtotime($radionica["datum"])) ?> required></td>
                                        </tr>
                                        <tr>
                                            <td>mesto:</td>
                                            <td><input type="text" name="mesto" required value="<?= $radionica["mesto"] ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>geografska dužina:</td>
                                            <td><input type="number" step="0.001" name="x_kor" min="-180" max="180" required value=<?= $radionica["x_kor"] ?>></td>
                                        </tr>
                                        <tr>
                                            <td>geografska širina:</td>
                                            <td><input type="number" step="0.001" name="y_kor" min="-90" max="90" required value=<?= $radionica["y_kor"] ?>></td>
                                        </tr>
                                        <tr>
                                            <td>kratki opis:</td>
                                            <td><input type="text" name="opis_kratki" required value="<?= $radionica["opis_kratki"] ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>dugi opis:</td>
                                            <td><input type="text" name="opis_dugi" required value="<?= $radionica["opis_dugi"] ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>maksimalni broj posetilaca:</td>
                                            <td><input type="number" name="max_broj_posetilaca" required value=<?= $radionica["max_broj_posetilaca"] ?>></td>
                                        </tr>
                                    </table>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <input type="submit" class="btn j-orange" value="ažuriraj podatke">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <div class="row justify-content-center" id="prijave_div" style="display:none">
            <div class="col-8 col-md-4">
                <table class="table table-hover text-center">
                    <?php foreach ($prijave as $prijava): ?>
                        <form method="post" action="routes.php">
                            <input type="hidden" name="akcija" id="akcija" value="prihvati_korisnika">
                            <input type="hidden" name="kontroler" id="kontroler" value="organizator">
                            <input type="hidden" name="idK" id="idK" value=<?= $prijava["idK"] ?>>
                            <input type="hidden" name="idR" id="idR" value=<?= $prijava["idR"] ?>>
                            <tr>
                                <td>
                                    <?= KorisniciDB::get_korisnika_po_idK($prijava["idK"])["kor_ime"] ?>
                                </td>
                                <td>
                                    <input type="submit" class="btn j-orange" value="prihvati korisnika">
                                </td>
                            </tr>
                        </form>
                    <?php endforeach; ?>
                </table> 
            </div>

        </div>
    </div>
</div>
