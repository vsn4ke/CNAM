<h3>Gestion des utilisateurs</h3>
<ul>
    <?php foreach($userList as $user):?>
        <li id="delete-<?= $user['id']?>">
            <span id="name-<?= $user['id']?>">
                <?= sanitize($user['name'])?>
            </span>
            <select id="<?= $user['id']?>-right">
                <?php foreach($rights as $right){
                    $selected = ($user['uRight'] == $right['id']) ? 'selected' : '';
                    echo '<option '. $selected .'>' . $right['name'] . '</option>';
                }?>
            </select>
            <small>
                <a href="#" title="Delete" id="d-<?= $user['id'] . '-' . $user['name'] ?>">Supprimer</a>
            </small>
        </li>
    <?php endforeach; ?>
    <form action="<?= generateURL('admin', 'purgeUser')?>" method="post">
        <input type="hidden" name="CSRFToken" value="<?= $_SESSION['CSRF'] ?>">
        <h4>Purge de la base de donnée</h4>
        <p>
            Purger la base de données supprimera TOUS les posts et commentaires écrits par les utilisateurs supprimer. <br>
            Une sauvegarde de la base de données est réalisé avant l'opération.<br>
            Les performances du site peuvent en être affectées.
        </p>
        <ul>
            <li><button type="submit">Purger les utilisateurs</button></li>
        </ul>
    </form>
</ul>
<script>
    var a = document.getElementsByTagName('a');
    for(var i = 0; i < a.length; i++){
        if( a[i].id.split('-')[0] == 'd'){
            a[i].addEventListener('click', function(e){
                e.preventDefault();
                var id = e.target.id.split('-')[1];
                if(confirm("Etes-vous sur?")){
                    var ajax = new XMLHttpRequest();
                    ajax.open("GET", "<?= generateURL('ajax', 'deleteUser-')?>" + id, true);
                    ajax.send();
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState == 4 && ajax.status == 200){
                            console.log(ajax.responseText);
                            if( ajax.responseText == "ok")
                            {
                                document.getElementById('name-'+id).innerHTML = "User " + id + " Deleted";

                            }else if (ajax.responseText == "last_admin"){
                                var flash = document.getElementById('flash');
                                flash.className='warning';
                                flash.innerHTML = 'Impossible de supprimer le dernier administrateur.';
                            }
                        }
                    };
                }
            })
        }
    }
    var selects = document.getElementsByTagName('select');
    for( i = 0; i < selects.length; i++){
        selects[i].addEventListener('change', function(e){
            e.preventDefault();
            var userId = e.target.id.split('-')[0];
            var userRight = e.target.value;
            var ajax = new XMLHttpRequest();
            ajax.open("POST", "<?= generateURL('ajax', 'changeRight')?>", true);
            ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ajax.send('userId=' + userId + '&userRight='+ userRight);
            ajax.onreadystatechange = function(){
                if(ajax.readyState == 4 && ajax.status == 200){
                    console.log(ajax.responseText);
                    if( ajax.responseText == "ok")
                    {
                        location.reload();

                    }else if (ajax.responseText == "last_admin"){
                        var flash = document.getElementById('flash');
                        flash.className='warning';
                        flash.innerHTML = 'Impossible de modifier le dernier administrateur.';
                    }
                }
            };

        })

    }

</script>