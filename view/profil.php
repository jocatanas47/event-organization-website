<div class="row content">
    <br>
    <div class="row justify-content-center">
        <br>
        <div class="col-4 text-center">
            <input type="radio" class="btn btn-check" name="profil" id="podaci" checked onclick="togglePodRadAk(0)">
            <label class="btn j-orange" for="podaci">podaci</label>
            
            <input type="radio" class="btn btn-check" name="profil" id="radionice" onclick="togglePodRadAk(1)">
            <label class="btn j-orange" for="radionice">radionice</label>
            
            <input type="radio" class="btn btn-check" name="profil" id="akcije" onclick="togglePodRadAk(2)">
            <label class="btn j-orange" for="akcije">akcije</label>
        </div>
    </div>
    
    <div class="col-12" id="podaci_div">
        <div class=" row justify-content-center j-greska">
            <?= $greska?>
        </div>
        <form name="form1" action="routes.php" method="get">
            <input type="hidden" name="akcija" id="akcija" value="azuriraj_podatke">
            <input type="hidden" name="kontroler" id="kontroler" value="korisnik">
            <div class="row justify-content-center">
                <div class="col-6">
                    <?php if ($profilna): ?>
                    <div>
                        <img src=<?= $profilna ?>>
                    </div>
                    <?php endif ?>
                    <br>
                    <table class="table table-hover">
                        <tr>
                            <td>ime:</td>
                            <td><input type="text" name="ime" value=<?= $korisnik["ime"] ?> required></td>
                        </tr>
                        <tr>
                            <td>prezime:</td>
                            <td><input type="text" name="prezime" value=<?= $korisnik["prezime"] ?> required></td>
                        </tr>
                        <tr>
                            <td>korisničko ime:</td>
                            <td><input type="text" name="kor_ime" value=<?= $korisnik["kor_ime"] ?> required></td>
                        </tr>
                        <tr>
                            <td>kontakt telefon:</td>
                            <td><input type="text" name="telefon" value=<?= $korisnik["telefon"] ?> required></td>
                        </tr>
                        <tr>
                            <td>e-mail adresa:</td>
                            <td><input type="text" name="mejl" value=<?= $korisnik["mejl"] ?> required></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-4 text-center">
                    <input type="submit" class="btn j-orange" value="ažuriraj podatke">
                </div>
            </div>
        </form>
        <form>
            <input type="hidden" name="akcija" id="akcija" value="promeni_lozinku">
            <input type="hidden" name="kontroler" id="kontroler" value="korisnik">
            <div class="row justify-content-center">
                <div class="col-6">
                    <table class="table table-hover">
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
            <div class="row justify-content-center">
                <div class="col-4 text-center">
                    <input type="submit" class="btn j-orange" value="promeni lozinku">
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-12" >
        <div id="radionice_div" style="display:none">
            aa
        </div>
    </div>
        
    <div class="col-12">
        <div id="akcije_div" style="display:none">
            bb
        </div>
    </div>
</div>