<div class="row">
    <div class="col-12 content">
        <form name="form1" enctype="multipart/form-data">
            <br>
            <div class="row justify-content-center">
                <div class="col-8 text-center j-greska" id="greska">
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-8 col-md-4 text-center">
                    <input type="radio" class="btn btn-check" name="korisnik" id="ucesnik" onclick="toggleUceOrg(0)">
                    <label class="btn j-orange" for="ucesnik">učesnik</label>

                    <input type="radio" class="btn-check" name="korisnik" id="organizator" checked onclick="toggleUceOrg(1)">
                    <label class="btn j-orange" for="organizator">organizator</label>
                </div>
            </div>
            <br>
            <div class="row justify-content-center">
                <div class="col-10 col-md-5">
                    <table class="table table-hover">
                        <tr>
                            <td>ime:*</td>
                            <td><input type="text" name="ime" id="ime"></td>
                        </tr>
                        <tr>
                            <td>prezime:*</td>
                            <td><input type="text" name="prezime" id="prezime"></td>
                        </tr>
                        <tr>
                            <td>korisničko ime:*</td>
                            <td><input type="text" name="kor_ime" id="kor_ime"></td>
                        </tr>
                        <tr>
                            <td>lozinka:*</td>
                            <td><input type="password" name="lozinka" id="lozinka"></td>
                        </tr>
                        <tr>
                            <td>potvrda:*</td>
                            <td><input type="password" name="potvrda" id="potvrda"></td>
                        </tr>
                        <tr>
                            <td>kontakt telefon:*</td>
                            <td><input type="text" name="telefon" id="telefon"></td>
                        </tr>
                        <tr>
                            <td>e-mail adresa:*</td>
                            <td><input type="email" name="mejl" id="mejl"></td>
                        </tr>
                    </table>
                </div>
                <div class="col-10 col-md-5" id="org_opcioni">
                    <table class="table table-hover">
                        <tr>
                            <td>naziv organizacije:</td>
                            <td><input type="text" name="naziv" id="naziv"></td>
                        </tr>
                        <tr>
                            <td>matični broj organizacije:</td>
                            <td><input type="number" name="maticni_broj" id="maticni_broj"></td>
                        </tr>
                        <tr>
                            <td>država:</td>
                            <td><input type="text" name="drzava" id="drzava"></td>
                        </tr>
                        <tr>
                            <td>grad:</td>
                            <td><input type="text" name="grad" id="grad"></td>
                        </tr>
                        <tr>
                            <td>poštanski broj:</td>
                            <td><input type="number" name="postanski_broj" id="postanski_broj"></td>
                        </tr>
                        <tr>
                            <td>ulica:</td>
                            <td><input type="text" name="ulica" id="ulica"></td>
                        </tr>
                        <tr>
                            <td>broj:</td>
                            <td><input type="text" name="adresa_broj" id="adresa_broj"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-6 col-md-3 text-center">
                    <label for="slika" class="form-label">profilna slika:</label>
                    <input type="file" class="form-control" accept=".jpg,.png" name="slika" id="slika">
                </div>
            </div>
            <br>
            <div class="row justify-content-center">
                <div class="col-8 col-md-4 text-center">
                    <input type="button" class="btn j-orange" value="registruj se" onclick="registracija()">
                </div>
            </div>
        </form>
    </div>
    <br>
</div>