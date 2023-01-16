<div class="row content">
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-8 text-center j-greska">
                <?= $greska ?>
            </div>
        </div>
        <form method="post" action="routes.php">
            <div class="row justify-content-center">
                <div class="col-10 col-md-6">
                    <input type="hidden" name="akcija" id="akcija" value="promeni_lozinku">
                    <input type="hidden" name="kontroler" id="kontroler" value="organizator">

                    <table class="table table-hover text-center">
                        <tr>
                            <td>stara lozinka:</td>
                            <td><input type="password" name="stara_lozinka" required></td>
                        </tr>
                        <tr>
                            <td>nova lozinka:</td>
                            <td><input type="password" name="nova_lozinka" required></td>
                        </tr>
                        <tr>
                            <td>potvrda:</td>
                            <td><input type="password" name="potvrda" required></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <input type="submit" class="btn j-orange" value="promeni lozinku">
                </div>
            </div>
        </form>
    </div>
</div>