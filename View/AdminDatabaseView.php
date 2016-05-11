<h3>Base de données</h3>
<form action="<?= generateURL('admin', 'backup')?>" method="post">
    <button type="submit">Sauvegarde de la base de donnée</button>
</form>

<form action="<?= generateURL('admin', 'reload')?>" method="post">
    <p>Remise par défaut de la base de donnée. (uniquement pour test)</p>
    <button type="submit">Recharger la base de donnée</button>
</form>