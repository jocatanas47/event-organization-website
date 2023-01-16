<div class="row content">
    <div class="col-12">
        <form name="form1" action="routes.php" method="get">
            <input type="hidden" name="akcija" id="akcija" value="resetuj_lozinku">
            <input type="hidden" name="kontroler" id="kontroler" value="gost">
            <div class="row justify-content-center">
                <div class="col-10 col-md-6">
                    <br>
                    <table class="table table-hover">
                        <tr>
                            <td>e-mail adresa:</td>
                            <td><input type="text" name="mejl" required></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-8 col-md-4 text-center">
                    <input type="submit" class="btn j-orange" value="pošalji">
                </div>
            </div>
        </form>
    </div>
</div>