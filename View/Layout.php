<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="en"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->

<head>
    <title><?= $title ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" type="image/x-icon" href="<?= generateViewURL('favicon.ico', 'img') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= generateViewURL('favicon.png', 'img') ?>">
    <link rel="stylesheet" href="<?= generateURL('View/Style/default.css') ?>" />
</head>
<body>
<div class="container">
    <header id="navtop"> <a href="<?= generateURL('index')?>" class="logo fleft"> <img src="<?= generateViewURL('logo.png', 'img')?>" alt=""> </a>

    </header>
    <div class="blog-page main grid-wrap">
        <header class="grid col-full">
            <p id="flash" class="<?= $flash['class'] == '' ? 'hidden' : $flash['class']?>">
                <?= $flash['message'] ?>
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
                            <li><a href="<?= generateURL('admin', 'category')?>">Administration</a></li>
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
                        <li><a id="Login" href="#">Login</a></li>
                    </ul>
                    <ul>
                        <li><a id="Register" href="#">Register</a></li>
                    </ul>
                <?php endif; ?>
            </nav>
            <hr class="clear">
        </header>
        <div id="modal">
            <div id="LoginModal" class="modal">
                <span id="closeLogin" class="close">x</span>
                <div class="modal-content">
                    <h3>Login : </h3>
                    <form action="<?= generateURL('login') ?>" method="post" id="loginForm">
                        <p><label for="usr">Username : </label><input id="usr" type="text" name="userName"></p>
                        <p><label for="pwd">Password : </label><input id="pwd" type="password" name="userPassword"></p>
                        <p><input type="submit"  id="submitForm" value="Login"></p>
                    </form>
                </div>
            </div>
            <div id="RegisterModal" class="modal">
                <span id="closeRegister" class="close">x</span>
                <div class="modal-content">
                    <h3>Register : </h3>
                    <form action="<?= generateURL('register') ?>" method="post" id="registerForm">
                        <p><label for="usr">Username : </label><input id="usr" type="text" name="userName"></p>
                        <p><label for="pwd">Password : </label><input id="pwd" type="password" name="userPassword"></p>
                        <p><label for="pwdConfirmation">Confirm password : </label><input id="pwdConfirmation" type="password" name="userPasswordConfirmation"></p>
                        <p><input type="submit"  id="submitForm" value="Register"></p>
                    </form>
                </div>
            </div>
        </div>
        <section class="grid col-three-quarters mq2-col-two-thirds mq3-col-full">
            <?= $content ?>
        </section>
        <aside class="grid col-one-quarter mq2-col-one-third mq3-col-full blog-sidebar">
            <?php if(isAdmin()):?>
            <div class="widget">
                <ul>
                    <li><a href="<?= generateURL('admin', 'category')?>">Gestion des catégories</a></li>
                </ul>
                <ul>
                    <li><a href="<?= generateURL('admin', 'post')?>">Gestion des posts</a></li>
                </ul>
                <ul>
                    <li><a href="<?= generateURL('admin', 'user')?>">Gestion des membres</a></li>
                </ul>
                <ul>
                    <li><a href="<?= generateURL('admin', 'database')?>">Sauvegarde base de données</a></li>
                </ul>
            </div>
            <?php endif; ?>
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
                    <li><a href="<?=generateURL('contact')?>">Contact</a></li>
                </ul>
            </nav>
        </footer>
    </div>
</div>
<?php if(!isset($_SESSION['user_name'])):?>
<script>
    var loginModal = document.getElementById('LoginModal');
    var registerModal = document.getElementById('RegisterModal');
    var postLoginModal = document.getElementById('PostLoginModal');

    if(postLoginModal !== null){
        postLoginModal.onclick = function(e) {
            e.preventDefault();
            display(loginModal);
        };
    }

    document.getElementById("Login").onclick = function(e) {
        e.preventDefault();
        display(loginModal);
    };

    document.getElementById("Register").onclick = function(e) {
        e.preventDefault();
        display(registerModal);
    };

    document.getElementById('closeLogin').onclick = close(loginModal);
    document.getElementById('closeRegister').onclick = close(registerModal);

    window.onclick = function(event) {
        if (event.target == loginModal) {
            close(loginModal);
        } else if(event.target == registerModal){
            close(registerModal)
        }
    };

    function close(block){
        block.style.display = "none";
    }

    function display(block){
        block.style.display = "block";
    }
</script>
<?php endif;?>

</body>
</html>