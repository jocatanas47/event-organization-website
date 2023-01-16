<div class="row content">
    <div class="col-12">
        <div class="row">
            <div class="col-12 justify-content-center j-greska">
                <?php $greska ?>
            </div>
        </div>
        <br>
        <div class="row justify-content-center">
            <div class="col-12 justify-content-center">
                <a class="link-secondary card-link" href="routes.php?kontroler=administrator&akcija=dodavanje_korisnika">dodaj korisnika</a>
            </div>
        </div>
        <br>
        <div class="row justify-content-center">
            <div class="col-10 text-center">
                <table class="table table-hover">
                    <tr>
                        <th>
                            korisničko ime
                        </th>
                        <th>
                            tip
                        </th>
                        <th>
                            status
                        </th>
                        <th>
                            akcije
                        </th>
                    </tr>
                    <?php foreach ($korisnici as $korisnik): ?>
                        <?php
                        if ($korisnik["tip"] == 0) {
                            $tip = "učesnik";
                        } else {
                            $tip = "organizator";
                        }
                        switch ($korisnik["status"]) {
                            case 0:
                                $status = "neodobren";
                                break;
                            case 1:
                                $status = "odobren";
                                break;
                            case 2:
                                $status = "odbijen";
                                break;
                            default:
                                $status = "greška";
                                break;
                        }
                        ?>
                        <tr>
                            <td>
                                <a class="link-secondary" href="routes.php?kontroler=administrator&akcija=korisnik_detalji&idK=<?= $korisnik["idK"] ?>">
                                    <?= $korisnik["kor_ime"] ?>
                                </a>
                            </td>
                            <td>
                                <?= $tip ?>
                            </td>
                            <td>
                                <?= $status ?>
                            </td>
                            <td>
                                <form action="routes.php" method="post">
                                    <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                                    <input type="hidden" name="akcija" id="akcija" value="izbrisi_korisnika">
                                    <input type="hidden" name="idK" id="idK" value="<?= $korisnik["idK"] ?>">
                                    <input type="submit" class="btn j-orange" value="izbriši">
                                </form>
                                <?php if ($korisnik["status"] == 0): ?>
                                    <form action="routes.php" method="post">
                                        <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                                        <input type="hidden" name="akcija" id="akcija" value="odobri_korisnika">
                                        <input type="hidden" name="idK" id="idK" value="<?= $korisnik["idK"] ?>">
                                        <input type="submit" class="btn j-orange" value="odobri">
                                    </form>
                                    <form action="routes.php" method="post">
                                        <input type="hidden" name="kontroler" id="kontroler" value="administrator">
                                        <input type="hidden" name="akcija" id="akcija" value="odbij_korisnika">
                                        <input type="hidden" name="idK" id="idK" value="<?= $korisnik["idK"] ?>">
                                        <input type="submit" class="btn j-orange" value="odbij">
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>