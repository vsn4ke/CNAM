<h3>Catégorie : </h3>
<ul>
    <?php foreach($categories as $category):?>
        <li id="delete-<?= $category['id']?>"><span id="name-<?= $category['id']?>"><?= $category['name']?></span>
            <small>
                <a href="#" title="Edit" id="e-<?= $category['id'] . '-' . $category['name'] ?>">Edit</a>,
                <a href="#" title="Delete" id="d-<?= $category['id'] . '-' . $category['name'] ?>">Delete</a>
            </small>
        </li>
    <?php endforeach; ?>
    <div id="newCat"></div>
    <li id="idAddCat"><a href="#" id="addCat">+ Add category</a></li>
</ul>
<script>
    var a = document.getElementsByTagName('a');

    for(var i = 0; i < a.length; i++){
        var f = a[i].id.split('-');
        if( f[0] == 'e'){
            a[i].addEventListener('click', function(e){
                e.preventDefault();
                var old = e.target;
                var id = e.target.id.split('-')[1];
                var span = document.getElementById('name-' + id);
                var name = span.innerHTML;


                var field = document.getElementById('field');
                if(field == null) {
                    var a = document.createElement('a');
                    a.title = 'Cancel';
                    a.id = 'cancel';
                    a.innerHTML = 'Cancel';
                    old.parentNode.replaceChild(a, old);

                    var cancel = document.getElementById('cancel');
                    cancel.addEventListener('click', function(e){
                        e.preventDefault();
                        span.innerHTML = name;
                        cancel.parentNode.replaceChild(old, cancel);
                    });

                    var input = document.createElement('input');
                    input.id = 'field';
                    input.type = 'text';
                    input.value = name;


                    span.innerHTML = '';
                    span.appendChild(input);

                    field = document.getElementById('field');
                    field.focus();
                    field.addEventListener('keypress', function (e) {
                        if (('which' in e ? e.which : e.keyCode) == 13) {
                            var newContent = field.value;
                            var ajax = new XMLHttpRequest();
                            ajax.open("POST", "<?= generateURL('ajax', 'editCategory-')?>" + id, true);
                            ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                            ajax.send('content=' + newContent);
                            ajax.onreadystatechange = function () {
                                if (ajax.readyState == 4 && ajax.status == 200) {
                                    console.log(ajax.responseText);
                                    if (ajax.responseText == "ok") {
                                        span.innerHTML = newContent;
                                        cancel.parentNode.replaceChild(old, cancel);
                                    }
                                }
                            };
                        }
                    })
                }


            })
        }else if( f[0] == 'd'){
            a[i].addEventListener('click', function(e){
                e.preventDefault();
                var id = e.target.id.split('-')[1];
                if(confirm("Etes-vous sur?")){
                    var ajax = new XMLHttpRequest();
                    ajax.open("GET", "<?= generateURL('ajax', 'deleteCategory-')?>" + id, true);
                    ajax.send();
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState == 4 && ajax.status == 200){
                            if( ajax.responseText == "ok")
                            {
                                var node = document.getElementById('delete-' + id);

                                node.parentNode.removeChild(node);

                            }
                        }
                    };
                }
            })
        }
    }

    document.getElementById('addCat').addEventListener('click', function(e){
        e.preventDefault();

        var field = document.getElementById('field');
        if(field === null){
            var input = document.createElement('input');
            input.id ='field';
            input.type = 'text';
            input.value = name;

            var li = document.createElement('li');
            li.appendChild(input);

            var a = document.createElement('a');
            a.title = 'Cancel';
            a.id = 'cancel';
            a.innerHTML = 'Cancel';


            var small = document.createElement('small');
            small.appendChild(a);

            li.appendChild(small);
            var div = document.getElementById('newCat');
            div.appendChild(li);

            var cancel = document.getElementById('cancel');
            cancel.addEventListener('click', function(e){
                e.preventDefault();
                div.removeChild(li);
            });

            field = document.getElementById('field');
            field.focus();
            field.addEventListener('keypress', function (e) {
                if (('which' in e ? e.which : e.keyCode) == 13) {
                    var content = field.value;
                    var ajax = new XMLHttpRequest();
                    ajax.open("POST", "<?= generateURL('ajax', 'addCategory')?>", true);
                    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    ajax.send('content=' + content);
                    ajax.onreadystatechange = function() {
                        if (ajax.readyState == 4 && ajax.status == 200) {
                            console.log(ajax.responseText);
                            if (ajax.responseText == "ok") {
                                location.reload();
                            }
                        }
                    };
                }
            });
        }
    });
</script>
<form action="<?= generateURL('admin', 'backup')?>" method="post">
    <button type="submit">Backup Database</button>
</form>

