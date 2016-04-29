<?php $title = sanitize($post['name']); ?>
<article class="post">
    <h2><a href="<?= generateURL('post', $post['slug'])?>"><?= sanitize($post['name']) ?></a></h2>
    <div class="meta">
        <p>Posté le <span class="time"><?= sanitize($post['date']) ?></span> par
            <span class="fn"><?= sanitize($post['username']) ?></span> dans
            <?php for($i = 0; $i < count($post['categories']); $i++) : ?>
                <a href="<?= generateURL('viewCat', $post['categories'][$i]['slug'])?>" class="cat"><?= sanitize($post['categories'][$i]['name']) ?></a>
                <?php if($i < count($post['categories'])-1){echo ', ';}?>
            <?php endfor; ?>
            avec
            <a href="#comments" class="comments-link"><?= $post['Com_Number']?> commentaire(s)</a>.
        </p>
    </div>
    <div class="entry"><?= sanitize($post['content']) ?></div>
</article>
<section class="section-comment">
    <header>
        <hr>
        <h5 id="comments" class="fleft"><?= $post['Com_Number']?> Commentaire(s)</h5>
        <p class="fright"><a href="#leavecomment" class="arrow">Laisser un commentaire</a></p>
    </header>

    <ol class="comments">
        <?php foreach ($comments['data'] as $comment): ?>
        <li class="comment">
            <h6><?= sanitize($comment['username']) ?> <span class="meta">le <?= sanitize($comment['date']) ?></span></h6>
            <hr>
            <?php if(isAdmin()):?>
               <ul class="fright admin">
                   <li><a class="edit" id="editComment-<?=$comment['id']?>" href="#Comment-<?=$comment['id']?>">edit</a></li>
                   <li><a class="delete" id="deleteComment-<?=$comment['id']?>" href="<?= generateURL('deleteComment', $comment['id']. '-' . $post['slug'])?>">delete</a></li>
               </ul>
            <?php endif;?>
            <p id="Comment-<?=$comment['id']?>"><?= sanitize($comment['content']) ?></p>
            <?php if(!is_null($comment['editname'])):?>
            <p class="edited">Edité par <?= sanitize($comment['editname'])?> le <?= $comment['editdate'] ?></p>
            <?php endif;?>
        </li>
        <?php endforeach; ?>
    </ol>
    <div class="leavecomment" id="leavecomment">
        <h3>Laisser un commentaire</h3>
        <?php if(isset($_SESSION['user_name'])):?>
        <form action="<?= generateURL('comment')?>" method="post">
            <ul>
                <li>
                    <label for="message">Message:  TODO - Ajouter du BBCode</label>
                    <textarea name="message" id="message" cols="100" rows="6" required  class="required"></textarea>
                </li>
                <li>
                    <button type="submit" id="submit" class="button fright">Commenter</button>
                </li>
            </ul>
            <input type="hidden" name="id" value="<?= sanitize($post['id'])?>">
            <input type="hidden" name="CSRFToken" value="<?= $_SESSION['CSRF'] ?>">
        </form>
        <?php else: ?>
        <p><a href="<?= generateURL('login')?>">Pour commenter, vous devez être connecter.</a></p>
        <?php endif; ?>
    </div>
</section>
<?= $comments['links'] ?>
<?php if(isAdmin()):?>
<script>

    // Delete Confirm
    var deleteLinks = document.getElementsByClassName("delete");

    for(var i = 0; i < deleteLinks.length; i++){
        deleteLinks[i].addEventListener('click', confirmDelete);
    }

    function confirmDelete(e){
        e.preventDefault();
        var target = e.target || e.srcElement;
        var id = target.id;

        var commentId =  id.substr(6, id.length-1);
        var comment = document.getElementById(commentId).innerHTML;
        var message = 'Etes-vous sur de vouloir supprimer le commentaire :\n' + commentId + '\n\nAyant pour texte :\n\n' + comment;

        if(confirm(message)){
            window.location.href=target.href;
        }

    }

    // Edit
    var editLinks = document.getElementsByClassName("edit");

    for(i = 0; i < editLinks.length; i++){
        editLinks[i].addEventListener('click', editComment)
    }

    function editComment(e){
        var target = e.target || e.srcElement;
        var id = target.id;

        var commentId =  id.substr(4, id.length-1);
        var comment = document.getElementById(commentId);

        var form = document.createElement('form');
        form.method = "post";
        form.action = "<?= generateURL('editComment')?>/" + id.split("-")[1] + "-<?= $post['slug']?>";

        var ul = document.createElement('ul');
        var li1 = document.createElement('li');
        var li2 = document.createElement('li');


        var textArea = document.createElement('textarea');
        textArea.name = 'message';

        var s = comment.innerHTML;
        textArea.innerHTML = s.replace(/<br>/gi, "");


        var submit = document.createElement('input');
        submit.type = "submit";
        submit.value = "Editer";
        submit.className = "button";

        var hidden = document.createElement('input');
        hidden.type = "hidden";
        hidden.name = "CSRFToken";
        hidden.value = "<?= $_SESSION['CSRF'] ?>";

        li1.appendChild(textArea);
        li2.appendChild(submit);
        ul.appendChild(li1);
        ul.appendChild(li2);

        form.appendChild(ul);
        form.appendChild(hidden);

        comment.parentNode.replaceChild(form, comment);
    }

</script>
<?php endif;?>
