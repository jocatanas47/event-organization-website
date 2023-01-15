<div class="row content">
    <div class="col-12">
        <div class="row">
            <div class="col-12justify-content-center j-greska">
                <?= $greska ?>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-5">
                
                <div class="row">
                    <div class="col-12 text-center">
                        <?php if ($profilna): ?>
                            <div>
                                <img class="img-fluid" src=<?= $profilna ?>>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <br>
                
                <div class="row">
                    <div class="col-12 text-center">
                        <form action="routes.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" id="kontroler" name="kontroler" value="administrator">
                            <input type="hidden" id="akcija" name="akcija" value="promeni_profilnu">
                            <input type="hidden" id="idK" name="idK" value="<?= $idK ?>">
                            <div class="row">
                                <div class="col-6">
                                    <input type="file" class="form-control" accept=".jpg,.png" name="slika" required>
                                </div>
                                <div class="col-6">
                                    <input type="submit" class="btn j-btn j-orange" value="promeni profilnu">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>

            </div>
            <div class="col-5 align-bottom">
                <form action="routes.php" method="get">
                    <input type="hidden" name="akcija" id="akcija" value="azuriraj_podatke">
                    <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                    <input type="hidden" id="idK" name="idK" value="<?= $idK ?>">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-hover">
                                <tr>
                                    <td>ime:</td>
                                    <td><input type="text" name="ime" value="<?= $korisnik["ime"] ?>" required></td>
                                </tr>
                                <tr>
                                    <td>prezime:</td>
                                    <td><input type="text" name="prezime" value="<?= $korisnik["prezime"] ?>" required></td>
                                </tr>
                                <tr>
                                    <td>korisničko ime:</td>
                                    <td><input type="text" name="kor_ime" value="<?= $korisnik["kor_ime"] ?>" required></td>
                                </tr>
                                <tr>
                                    <td>kontakt telefon:</td>
                                    <td><input type="text" name="telefon" value="<?= $korisnik["telefon"] ?>" required></td>
                                </tr>
                                <tr>
                                    <td>e-mail adresa:</td>
                                    <td><input type="text" name="mejl" value="<?= $korisnik["mejl"] ?>" required></td>
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
                
                <form method="get" action="routes.php">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="akcija" id="akcija" value="promeni_lozinku_korisniku">
                            <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                            <input type="hidden" id="idK" name="idK" value="<?= $idK ?>">
                            <table class="table table-hover">
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
    </div>
</div>