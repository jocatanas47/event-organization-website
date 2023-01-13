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
            <div class="col-12 justify-content-center text-center">
                <form name="forma1" method="get" action="routes.php">
                    <input type="hidden" id="akcija" name="akcija" value="izaberi_sablon">
                    <input type="hidden" id="kontroler" name="kontroler" value="organizator">
                    <select name="sablon" id="sablon">
                        <option value="-1">izaberite šablon</option>
                        <?php foreach ($radionice as $radionica1): ?>
                            <option value=<?= $radionica1["idR"] ?>><?= $radionica1["naziv"]." - ".$radionica1["datum"] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" class="j-btn btn j-orange" value="primeni šablon">
                    
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12" id="podaci">
                <form name="form1" action="routes.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="akcija" id="akcija" value="dodaj_radionicu">
                    <input type="hidden" name="kontroler" id="kontroler" value="organizator">
                    <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                    <div class="row justify-content-center">
                        <div class="col-6">
                            <br>
                            <table class="table table-hover">
                                <tr>
                                    <td>naziv:</td>
                                    <td><input type="text" name="naziv" required value="<?= $radionica["naziv"] ?>"></td>
                                </tr>
                                <tr>
                                    <td>datum:</td>
                                    <td><input type="datetime-local" name="datum" required></td>
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
                                    <td>glavna slika:</td>
                                    <td><input type="file" name="glavna_slika" accept=".jpg,.png" required></td>
                                </tr>
                                <tr>
                                    <td>galerija slika:</td>
                                    <td><input type="file" name="galerija_slika[]" accept=".jpg,.png" multiple required></td>
                                </tr>
                                <tr>
                                    <td>maksimalni broj posetilaca:</td>
                                    <td><input type="number" name="max_broj_posetilaca" required value=<?= $radionica["max_broj_posetilaca"] ?>></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-4 text-center">
                            <input type="submit" class="btn j-orange" value="dodaj radionicu">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>