<div class="row content">
    <br>
    <div class="row">
        <div class="col-12">
            <br>
            <div class="row justify-content-center">
                <div class="col-12 justify-content-center text-center">
                    <form name="forma1" method="get" action="routes.php">
                        <input type="hidden" id="akcija" name="akcija" value="filtriraj_radionice">
                        <input type="hidden" id="kontroler" name="kontroler" value="organizator">
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
                                                <?php if ($radionica["idO"] == $idO): ?>
                                                <div class="col-12">
                                                    <a class="link-secondary card-link" href=<?php 
                                                    $idR = $radionica['idR'];
                                                    echo "routes.php?kontroler=organizator&akcija=uredjivanje_radionice&idR=$idR"; ?>>uredi radionicu</a>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </td>
                            </div>
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