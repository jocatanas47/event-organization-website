<div class="row content">
    <div class="col-12">
        <br>
        <div class="row justify-content-center">
            <br>
            <div class="col-8 col-md-4 text-center">
                <input type="radio" class="btn btn-check" name="radionice" id="prijavljene" onclick="togglePrijavljeneSve(0)">
                <label class="btn j-orange" for="prijavljene">prijavljene radionice</label>

                <input type="radio" class="btn btn-check" name="radionice" id="sve" checked onclick="togglePrijavljeneSve(1)">
                <label class="btn j-orange" for="sve">sve radionice</label>
            </div>
        </div>
        <div class="row" id="prijavljene_div" style="display:none">
            <div class="col-12">
                <table class="table">
                    <tbody>
                        <?php foreach ($prijavljene_radionice as $radionica): ?>
                            <tr>
                                <td>
                                    <div class="row justify-content-center">
                                        <div class="col-10 col-md-5">
                                            <div class="card">
                                                <img class="img-fluid" src=<?php echo SlikeDB::get_sliku($radionica["idS"])["putanja"]; ?>>
                                                <div class="card-body">
                                                    <div class="col-12 font-weight-bold text-center">
                                                        <h4><?= $radionica["naziv"] ?></h4>
                                                    </div>
                                                    <div class="col-12 fst-italic">
                                                        <?= $radionica["datum"] ?>
                                                    </div>
                                                    <div class="col-12 fw-bold">
                                                        <?= $radionica["mesto"] ?>
                                                    </div>
                                                    <div class="col-12">
                                                        <?= $radionica["opis_kratki"] ?>
                                                    </div>
                                                    <div class="col-12">
                                                        <a class="link-secondary card-link" href=<?php
                                                        $idR = $radionica['idR'];
                                                        echo "routes.php?kontroler=korisnik&akcija=radionica_detalji&idR=$idR";
                                                        ?>>detalji</a>
                                                    </div>
                                                    <?php if (RadioniceDB::vise_od_12h_do_radionice($idR)): ?>
                                                    <br>
                                                    <div class="col-12">
                                                        <form>
                                                            <input type="hidden" name="kontroler" id="kontroler" value="korisnik">
                                                            <input type="hidden" name="akcija" id="akcija" value="otkazi_prijavu">
                                                            <input type="hidden" name="idR" id="idR" value="<?= $radionica["idR"] ?>">
                                                            <input type="submit" class="btn j-orange" value="odjavi dolazak">
                                                        </form>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" id="sve_div">
            <div class="col-12">
                <br>
                <div class="row justify-content-center">
                    <div class="col-12 justify-content-center text-center">
                        <form name="forma1" method="get" action="routes.php">
                            <input type="hidden" id="akcija" name="akcija" value="filtriraj_radionice">
                            <input type="hidden" id="kontroler" name="kontroler" value="korisnik">
                            <select name="mesto" id="mesto">
                                <option value="izaberite mesto">izaberite mesto</option>
                                <?php foreach ($mesta as $mesto): ?>
                                    <option value="<?= $mesto ?>"><?= $mesto ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" id="naziv" name="naziv" placeholder="naziv">
                            <input type="submit" class="j-btn btn j-orange" value="primeni filter">
                            |
                            <input type="button" class="j-btn btn j-orange" value="sortiraj po nazivu" onclick="sortirajTabelu(1)">
                            <input type="button" class="j-btn btn j-orange" value="sortiraj po datumu" onclick="sortirajTabelu(2)">
                        </form>
                    </div>
                </div>

                <table class="table" id="tabela">
                    <tbody>
                        <?php foreach ($radionice as $radionica): ?>
                            <tr>
                                <td>
                                    <div class="row justify-content-center">
                                        <div class="col-10 col-md-5">
                                            <div class="card">
                                                <img class="img-fluid" src="<?php echo SlikeDB::get_sliku($radionica["idS"])["putanja"]; ?>">
                                                <div class="card-body">
                                                    <div class="col-12 font-weight-bold text-center">
                                                        <h4><?= $radionica["naziv"] ?></h4>
                                                    </div>
                                                    <div class="col-12 fst-italic">
                                                        <?= $radionica["datum"] ?>
                                                    </div>
                                                    <div class="col-12 fw-bold">
                                                        <?= $radionica["mesto"] ?>
                                                    </div>
                                                    <div class="col-12">
                                                        <?= $radionica["opis_kratki"] ?>
                                                    </div>
                                                    <div class="col-12">
                                                        <a class="link-secondary card-link" href="<?php
                                                        $idR = $radionica['idR'];
                                                        echo "routes.php?kontroler=korisnik&akcija=radionica_detalji&idR=$idR";
                                                        ?>">detalji</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </td>
                                <td class="j-td-hidden">
                                    <?= $radionica["naziv"] ?>
                                </td>
                                <td class="j-td-hidden">
                                    <?= $radionica["datum"] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </div>
</div>