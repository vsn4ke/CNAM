<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->
<head>
    <title><?= $title ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" type="image/x-icon" href="<?= generateURL('View/img/favicon.ico') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= generateURL('View/img/favicon.png') ?>">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?= generateURL('View/Style/default.css') ?>" />
</head>
<body>
<div class="container">
    <header id="navtop"> <a href="<?= generateURL('index')?>" class="logo fleft"> <img src="<?= generateViewURL('logo.png', 'img')?>" alt=""> </a>

    </header>
    <div class="blog-page main grid-wrap">
        <header class="grid col-full">
            <p id="flash" class="<?= $flash['class'] == '' ? 'hidden' : $flash->class?>">
                <?= $flash->message ?>
            </p>
            <hr>
            <nav class="fleft">
                <?php foreach($catList as $cat){
                    echo '<ul><li><a href="'.generateURL('viewCat', $cat['slug']).'">'.sanitize($cat['name']).'</a></li></ul>';
                }?>
            </nav>
            <nav class="fright">
                <?php if(isset($_SESSION['user_name'])):?>
                    <?php if(isAdmin()) : ?>
                        <ul>
                            <li><a href="<?= generateURL('admin', 'post')?>">Administration</a></li>
                        </ul>
                    <?php endif;?>
                    <ul>
                        <li><?= sanitize($_SESSION['user_name'])?></li>
                    </ul>
                    <ul>
                        <li><a href="<?= generateURL('logout')?>">Logout</a></li>
                    </ul>
                <?php else: ?>
                    <ul>
                        <li><a href="<?= generateURL('login')?>">Login</a></li>
                    </ul>
                    <ul>
                        <li><a href="<?= generateURL('register')?>">Register</a></li>
                    </ul>
                <?php endif; ?>
            </nav>
            <hr class="clear">
            <?php if($admin):?>
                <nav>
                    <ul>
                        <li>Gestion des posts</li>
                    </ul>
                    <ul>
                        <li>Gestion des membres</li>
                    </ul>
                    <ul>
                        <li>Sauvegarde base de données</li>
                    </ul>
                </nav>
            <?php endif; ?>
        </header>
        <section class="grid col-three-quarters mq2-col-two-thirds mq3-col-full">
            <?= $content ?>
        </section>
        <aside class="grid col-one-quarter mq2-col-one-third mq3-col-full blog-sidebar">
            <div class="widget">
                <p>TODO : Ajouter un texte de présentation</p>
            </div>
            <div class="widget">
                <h2>Popular Posts</h2>
                <ul>
                    <?php foreach($popularPostList as $popularPost){
                        echo '<li><a href="'.generateURL('post', $popularPost['slug']).'">'. sanitize($popularPost['name']).'</a></li>';
                    }?>
                </ul>
            </div>
            <div class="widget">
                <h2>Categories</h2>
                <ul>
                    <?php foreach($catList as $cat){
                        echo '<li><a href="'.generateURL('viewCat', $cat['slug']).'">'.sanitize($cat['name']).' ('.$cat['Post_Count'].')</a></li>';
                    }?>
                </ul>
            </div>
        </aside>
    </div>
    <!--main-->
    <div class="divide-top">
        <footer class="grid-wrap">
            <ul class="grid col-one-third social">
                <li><a href="#">TODO : Ajouter les liens réseaux sociaux</a></li>
            </ul>
            <div class="up grid col-one-third "> <a href="#navtop" title="Go back up">&uarr;</a> </div>
            <nav class="grid col-one-third ">
                <ul>
                    <li><a href="#">TODO : Ajouter les liens rapides</a></li>
                </ul>
            </nav>
        </footer>
    </div>
</div>
</body>
</html>