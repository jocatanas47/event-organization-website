<div class="row">
    <div class="col-12 content">
        <?php foreach ($radionice as $radionica): ?>
        <div class="row">
            <div class="col-6">
                <div class="row">
                    <div class="col-8">
                        <img src=<?php echo SlikeDB::get_sliku($radionica["idS"])["putanja"]; ?>>
                    </div>
                    <div class="col-4">
                        <div class="row">
                            <div class="col-12">
                                <?= $radionica["naziv"]?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <?= $radionica["datum"]?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <?= $radionica["mesto"]?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?= $radionica["opis_dugi"]?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>