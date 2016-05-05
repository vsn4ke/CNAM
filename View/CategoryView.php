<h1><?= $category['name'] ?></h1>
<?php $id = 0;?>
<?php foreach($postList['data'] as $post): ?>
    <article class="post">
        <h2><a href="<?= generateURL('post', $post['slug'])?>"><?= sanitize($post['name']) ?></a></h2>
        <div class="meta">
            <p>Posté le <span class="time"><?= sanitize($detail[$id]['date']) ?></span> par
                <span class="fn"><?= sanitize($detail[$id]['username']) ?></span> dans
                <?php for($i = 0; $i < count($detail[$id]['categories']); $i++) : ?>
                    <a href="<?= generateURL('viewCat', $detail[$id]['categories'][$i]['slug'])?>" class="cat"><?= sanitize($detail[$id]['categories'][$i]['name']) ?></a>
                    <?php if($i < count($detail[$id]['categories'])-1){echo ', ';}?>
                <?php endfor; ?>
                 avec <a href="<?= generateURL('post', $post['slug'])?>#comments" class="comments-link"><?= $detail[$id]['Com_Number']?> commentaire(s)</a>.
            </p>
        </div>
        <div class="entry"><?= truncate($detail[$id]['content']) ?></div>
        <footer><a href="<?= generateURL('post', $post['slug'])?>">Continuer la lecture...</a></footer>
    </article>
    <?php $id++;?>
<?php endforeach; ?>
<?= $postList['links'] ?>
