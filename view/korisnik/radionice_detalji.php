<div class="row content">
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-8 text-center">
                <h3><?= $radionica["naziv"] ?></h3>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-5 card">
                
            </div>
            <div class="col-4">
                <div id="mapa">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-10">
                <?= $radionica["opis_dugi"] ?>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-6 text-center">
                <form method="get" action="routes.php">
                    <input type="hidden" id="kontroler" name="kontroler" value="korisnik">
                    <input type="hidden" id="akcija" name="akcija" value="prijavi_radionicu">
                    <input class="btn j-btn j-orange" type="submit" value="prijavi se">
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    var map = L.map('mapa').setView([<?= $xcor ?>, <?= $ycor ?>], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
</script>