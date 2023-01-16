<div class="row content justify-content-center">
    <div class="col-8 col-md-4 text-center">
        <table class="table table-hover">
            <?php foreach ($korisnici as $korisnik): ?>
            <tr>
                <td>
                    <?= $korisnik["kor_ime"] ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>