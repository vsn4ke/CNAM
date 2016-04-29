<?php

function category($param, $page = 1){
    $cat = getCategory($param);
    if($cat['name'] == '')
        throw new Exception('Categorie invalide');
    
    $_SESSION['current'] = generateURL('viewCat', $cat['slug']);
    $postList = getPostList($param, $page);

    $posts = null;
    for($i = 0; $i< count($postList); $i++){
        if(isset($postList['data'][$i])){
            $posts[$i] = getPost($postList['data'][$i]['id']);
            $posts[$i]['categories'] = getCategories($postList['data'][$i]['id']);
        }
    }
    generate("Category", array('category' => $cat, 'postList' => $postList, 'posts' => $posts));
}

function index(){category(1);}

function adminFunction($name, $params = array()){

    if(!isAdmin()){
        throw new Exception("Vous n'avez pas le droit d'utiliser les commandes d'admin.");
    }
    switch($name) {
        case 'deleteComment' :
            deleteComment($params[0]);
            $_SESSION['flash_class'] = 'success';
            $_SESSION['flash'] = 'Commentaire supprimé avec succés.';
            getPost($params[1]);
            break;

        case 'editComment' :
            editComment($params[0], $params[2], $_SESSION['id']);
            $_SESSION['flash_class'] = 'success';
            $_SESSION['flash'] = "Commentaire éditer avec succés";
            getPost($params[1]);
            break;

        case 'managePost' :
            $categories = getCategories();
            $posts = getPosts();
            generate('PostAdmin', array('posts' => $posts, 'categories' => $categories, 'admin' => true), true);
            break;

        case 'manageUser' :
            break;

        case 'backup' :
            backupTables($params);
            $_SESSION['flash_class'] = 'success';
            $_SESSION['flash'] = 'Backup réalisé avec succès';
            index();
            break;

        case 'editCategory' :
            if (editCategory($params[0], $params[1]))
                echo 'ok';
            break;

        case 'addCategory' :
            if (addCategory($params[0]))
                echo 'ok';
            break;

        case 'deleteCategory' :
            if (deleteCategory($params[0]))
                echo 'ok';
            break;

        default:
            throw new Exception('Catégorie invalide.');
    }
}

function post($param, $page = 1){
    $post = getPost($param);
    if($post['name'] == '')
        throw new Exception('Post non valide');

    $post['categories'] = getCategories($post['id']);
    
    $_SESSION['current'] = generateURL('post', $post['slug']);
    $comments = getComments($post['id'], $page);
    generate('post', array('post' => $post, 'comments' => $comments));
}

function comment($userId, $content, $postId){
    addComment($userId, $content, $postId);
    getPost($postId);
}


function loginPage($userName = null, $userPassword = null){

    if(is_null($userName)){
        generate('login');
    }else{
        login($userName, $userPassword);
        $_SESSION['flash_class'] = 'success';
        $_SESSION['flash'] = "Successfully Login";
        index();
    }
}

function registerPage($userName = null, $userPassword = null, $userPasswordConfirmation = null){
    if(is_null($userName)){
        generate("Register");
    }else{
        if($userPassword == $userPasswordConfirmation){
            register($userName, $userPassword);
            $_SESSION['flash_class'] = 'success';
            $_SESSION['flash'] = "Successfully Register";
            index();
        }else
            throw new Exception("The password and the confirmation don't match.");
    }
}

function logoutPage(){
    logout();
    $_SESSION['flash_class'] = 'success';
    $_SESSION['flash'] = "Successfully Logout";
    index();
}

function isAdmin(){
    return (isset($_SESSION['user_right']) && $_SESSION['user_right'] == 50) ? true : false;
}