<div class="row content">
    <div class="col-12">
        <div class="row">
            <div class="col-12 text-center j-greska">
                <?= $greska ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form action="routes.php" method="get">
                    <input type="hidden" name="akcija" id="akcija" value="prijavi_se">
                    <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                    <div class="row justify-content-center">
                        <div class="col-6">
                            <br>
                            <table class="table table-hover">
                                <tr>
                                    <td>korisničko ime:</td>
                                    <td><input type="text" name="kor_ime" required></td>
                                </tr>
                                <tr>
                                    <td>lozinka:</td>
                                    <td><input type="password" name="lozinka" required></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-4 text-center">
                            <input type="submit" class="btn j-orange" value="prijavi se">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>