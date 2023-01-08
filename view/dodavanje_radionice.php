<div class="row content">
    <div class="col-12" id="podaci">
        <form name="form1" action="routes.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="akcija" id="akcija" value="dodaj_radionicu">
            <input type="hidden" name="kontroler" id="kontroler" value="organizator">
            <div class="row justify-content-center">
                <div class="col-6">
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
                            <td><input type="file" name="galerija_slika[]" accept=".jpg,.png" multiple required></td>
                        </tr>
                        <tr>
                            <td>maksimalni broj posetilaca:</td>
                            <td><input type="number" name="max_broj_posetilaca" required></td>
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