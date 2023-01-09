<div class="row content">
    <div class="col-12">
        <form name="forma1" method="get" action="routes.php">
            <input type="hidden" id="akcija" name="akcija" value="filtriraj_radionice">
            <input type="hidden" id="kontroler" name="kontroler" value="korisnik">
            <select name="mesto" id="mesto">
                <option value="izaberite mesto">izaberite mesto</option>
                <?php foreach($mesta as $mesto): ?>
                <option value=<?= $mesto ?>><?= $mesto ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="naziv" name="naziv">
            <input type="submit" value="filter">
        </form>
    </div>
    <div class="col-12">
        <?php foreach ($radionice as $radionica): ?>
        <div class="row justify-content-center">
            <div class="col-5 justify-content-center j-radionica">
                <div class="row">
                    <div class="col-5" height="150px">
                        <img class="img-fluid" height="150px" src=<?php echo SlikeDB::get_sliku($radionica["idS"])["putanja"]; ?>>
                    </div>
                    <div class="col-7">
                        <div class="row" height="50px">
                            <div class="col-4">
                                Naziv:
                            </div>
                            <div class="col-8">
                                <?= $radionica["naziv"]?>
                            </div>
                        </div>
                        <div class="row" height="50px">
                            <div class="col-4">
                                Datum:
                            </div>
                            <div class="col-8">
                                <?= $radionica["datum"]?>
                            </div>
                        </div>
                        <div class="row" height="50px">
                            <div class="col-4">
                                Mesto:
                            </div>
                            <div class="col-8">
                                <?= $radionica["mesto"]?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        Opis:
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?= $radionica["opis_dugi"]?>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <?php endforeach; ?>
    </div>
</div>