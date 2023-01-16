<div class="row content">
    <div class="col-12">
        <br>
        <div class="row justify-content-center">
            <div class="col-8 text-center j-greska">
                <?= $greska ?>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12" id="podaci">
                <form name="form1" action="routes.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="akcija" id="akcija" value="predlozi_radionicu">
                    <input type="hidden" name="kontroler" id="kontroler" value="korisnik">
                    <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                    <div class="row justify-content-center">
                        <div class="col-10 col-md-6">
                            <br>
                            <table class="table table-hover">
                                <tr>
                                    <td>naziv:</td>
                                    <td><input type="text" name="naziv" required></td>
                                </tr>
                                <tr>
                                    <td>datum:</td>
                                    <td><input type="datetime-local" name="datum" required></td>
                                </tr>
                                <tr>
                                    <td>mesto:</td>
                                    <td><input type="text" name="mesto" required></td>
                                </tr>
                                <tr>
                                    <td>geografska dužina:</td>
                                    <td><input type="number" step="0.001" name="x_kor" min="-180" max="180" required></td>
                                </tr>
                                <tr>
                                    <td>geografska širina:</td>
                                    <td><input type="number" step="0.001" name="y_kor" min="-90" max="90" required></td>
                                </tr>
                                <tr>
                                    <td>kratki opis:</td>
                                    <td><input type="text" name="opis_kratki" required></td>
                                </tr>
                                <tr>
                                    <td>dugi opis:</td>
                                    <td><input type="text" name="opis_dugi" required></td>
                                </tr>
                                <tr>
                                    <td>glavna slika:</td>
                                    <td><input type="file" name="glavna_slika" accept=".jpg,.png" required></td>
                                </tr>
                                <tr>
                                    <td>galerija slika:</td>
                                    <td><input type="file" name="galerija_slika[]" accept=".jpg,.png" multiple></td>
                                </tr>
                                <tr>
                                    <td>maksimalni broj posetilaca:</td>
                                    <td><input type="number" name="max_broj_posetilaca" required></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-8 col-md-4 text-center">
                            <input type="submit" class="btn j-orange" value="predloži radionicu">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>