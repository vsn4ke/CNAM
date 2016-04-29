<h1><?= $category['name'] ?></h1>
<?php foreach($postList['data'] as $post): ?>
    <?php $id = $post[0]-1;?>
    <article class="post">
        <h2><a href="<?= generateURL('post', $post['slug'])?>"><?= sanitize($post['name']) ?></a></h2>
        <div class="meta">
            <p>Posté le <span class="time"><?= sanitize($posts[$id]['date']) ?></span> par
                <span class="fn"><?= sanitize($posts[$id]['username']) ?></span> dans
                <?php for($i = 0; $i < count($posts[$id]['categories']); $i++) : ?>
                    <a href="<?= generateURL('viewCat', $posts[$id]['categories'][$i]['slug'])?>" class="cat"><?= sanitize($posts[$id]['categories'][$i]['name']) ?></a>
                    <?php if($i < count($posts[$id]['categories'])-1){echo ', ';}?>
                <?php endfor; ?>
                 avec <a href="<?= generateURL('post', $post['slug'])?>#comments" class="comments-link"><?= $posts[$id]['Com_Number']?> commentaire(s)</a>.
            </p>
        </div>
        <div class="entry"><?= truncate($posts[$id]['content']) ?></div>
        <footer><a href="<?= generateURL('post', $post['slug'])?>">Continuer la lecture...</a></footer>
    </article>
<?php endforeach; ?>
<?= $postList['links'] ?>
