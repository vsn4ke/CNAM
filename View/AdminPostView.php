<h3>Gestion des Posts</h3>
<ul>
    <?php foreach($posts as $post):?>
        <li id="delete-<?= $post['id']?>"><span id="name-<?= $post['id']?>"><?= $post['name']?></span>
            <small>
                <a href="#" title="Edit" id="e-<?= $post['id'] . '-' . $post['name'] ?>">Edit</a>,
                <a href="#" title="Delete" id="d-<?= $post['id'] . '-' . $post['name'] ?>">Delete</a>
            </small>
            <input type="hidden" value="<?= $post['content'] ?>" id="content-<?= $post['id']?>">
        </li>
    <?php endforeach; ?>
    <div id="newPost"></div>
    <li id="idAddPost"><a href="#" id="addPost">+ Add Post</a></li>
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

                    var form = document.createElement('form');
                    var input = document.createElement('input');
                    var textArea = document.createElement('textArea');
                    var submit = document.createElement('input');
                    var url = '<?= generateURL('ajax', 'editPost-')?>' + id;

                    form.id = 'field';
                    form.method = 'post';
                    form.action = url;

                    input.type = 'text';
                    input.name = 'name';
                    input.value = name;
                    input.required = true;
                    input.id = 'fieldName';

                    textArea.name = 'content';
                    textArea.cols = '100';
                    textArea.rows = '6';
                    textArea.value = document.getElementById('content-' +id).value;
                    textArea.required = true;

                    submit.type = 'submit';
                    submit.value = 'Editer';

                    form.appendChild(input);
                    form.appendChild(textArea);
                    form.appendChild(submit);

                    span.innerHTML = '';
                    span.appendChild(form);

                    field = document.getElementById('field');
                    field.focus();
                    field.addEventListener('submit', function (e) {
                        e.preventDefault();
                        var ajax = new XMLHttpRequest();
                        ajax.open("POST", url, true);
                        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        ajax.send(serialize(field));
                        ajax.onreadystatechange = function () {
                            if (ajax.readyState == 4 && ajax.status == 200) {
                                console.log(ajax.responseText);
                                if (ajax.responseText == "ok") {
                                    location.reload();
                                }else{
                                    var flash = document.getElementById('flash');
                                    flash.innerHTML = ajax.responseText;
                                    flash.className = 'warning';
                                }
                            }
                        };
                    })
                }


            })
        }else if( f[0] == 'd'){
            a[i].addEventListener('click', function(e){
                e.preventDefault();
                var id = e.target.id.split('-')[1];
                if(confirm("Etes-vous sur?\nLa suppression d'un post entraine la suppression de tous les commentaires associés.")){
                    var ajax = new XMLHttpRequest();
                    ajax.open("GET", "<?= generateURL('ajax', 'deletePost-')?>" + id, true);
                    ajax.send();
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState == 4 && ajax.status == 200){
                            if( ajax.responseText == "ok")
                            {
                                location.reload();
                            }else{
                                var flash = document.getElementById('flash');
                                flash.innerHTML = ajax.responseText;
                                flash.className = 'warning';
                            }

                        }
                    };
                }
            })
        }
    }

    document.getElementById('addPost').addEventListener('click', function(e){
        e.preventDefault();

        var field = document.getElementById('field');
        if(field === null){
            var span = document.createElement('span');
            var form = document.createElement('form');
            var input = document.createElement('input');
            var textArea = document.createElement('textArea');
            var submit = document.createElement('input');
            var li = document.createElement('li');
            var a = document.createElement('a');
            var small = document.createElement('small');
            var div = document.getElementById('newPost');
            var url = '<?= generateURL('ajax', 'addPost')?>';

            form.id = 'field';
            form.method = 'post';
            form.action = url;

            input.type = 'text';
            input.name = 'name';
            input.value = name;
            input.required = true;
            input.id = 'fieldName';
            input.placeholder = 'Titre du post :';

            textArea.name = 'content';
            textArea.cols = '100';
            textArea.rows = '6';
            textArea.required = true;
            textArea.placeholder = 'Corps du post';

            submit.type = 'submit';
            submit.value = 'Ajouter';

            var select = document.createElement('select');
            select.multiple = true;
            select.name = 'categories[]';
            select.size = <?= count($categories)?>;

<?php
    $i = 0;
    foreach($categories AS $category){

    echo "
            var option$i = document.createElement('option');
            option$i.value = '".$category['id']."';
            option$i.innerHTML = '".$category['name']."';
            select.appendChild(option$i);";
    $i++;
    }
?>


            form.appendChild(input);
            form.appendChild(select);
            form.appendChild(textArea);
            form.appendChild(submit);


            li.appendChild(form);

            a.title = 'Cancel';
            a.id = 'cancel';
            a.innerHTML = 'Cancel';

            small.appendChild(a);
            li.appendChild(small);
            div.appendChild(li);

            var cancel = document.getElementById('cancel');
            cancel.addEventListener('click', function(e){
                e.preventDefault();
                div.removeChild(li);
            });

            field = document.getElementById('field');
            field.firstChild.focus();
            field.addEventListener('submit', function (e) {
                e.preventDefault();
                var ajax = new XMLHttpRequest();
                ajax.open("POST", "<?= generateURL('ajax', 'addPost')?>", true);
                ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                ajax.send(serialize(field));
                ajax.onreadystatechange = function() {
                    if (ajax.readyState == 4 && ajax.status == 200) {
                        console.log(ajax.responseText);
                        if (ajax.responseText == "ok") {
                            location.reload();
                        }else{
                            var flash = document.getElementById('flash');
                            flash.innerHTML = ajax.responseText;
                            flash.className = 'warning';
                        }
                    }
                };

            });
        }
    });

    function serialize(form) {
        var field, s = [];
        if (typeof form == 'object' && form.nodeName == "FORM") {
            var len = form.elements.length;
            for (i=0; i<len; i++) {
                field = form.elements[i];
                if (field.name && !field.disabled && field.type != 'file' && field.type != 'reset' && field.type != 'submit' && field.type != 'button') {
                    if (field.type == 'select-multiple') {
                        for (j=form.elements[i].options.length-1; j>=0; j--) {
                            if(field.options[j].selected)
                                s[s.length] = encodeURIComponent(field.name) + "=" + encodeURIComponent(field.options[j].value);
                        }
                    } else if ((field.type != 'checkbox' && field.type != 'radio') || field.checked) {
                        s[s.length] = encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value);
                    }
                }
            }
        }
        return s.join('&').replace(/%20/g, '+');
    }
</script>