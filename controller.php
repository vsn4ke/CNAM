<?php

/**
 * Gather data to display a category with their posts
 * @param $param
 * @param int $page
 * @throws Exception
 */
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
    generate("Category", $cat['name'], array('category' => $cat, 'postList' => $postList, 'posts' => $posts));
}

/**
 * Alias for category(1)
 */
function index(){category(1);}

/**
 * Manage all admin only function. A kind of router for admin's page
 * @param $name
 * @param array $params
 * @throws Exception
 */
function adminFunction($name, $params = array()){

    if(!isAdmin()){
        throw new Exception("Vous n'avez pas le droit d'utiliser les commandes d'admin.");
    }
    switch($name) {
        case 'deleteComment' :
            deleteComment($params[0]);
            $_SESSION['flash_class'] = 'success';
            $_SESSION['flash'] = 'Commentaire supprimé avec succés.';
            post($params[1]);
            break;

        case 'editComment' :
            editComment($params[0], $params[2], $_SESSION['id']);
            $_SESSION['flash_class'] = 'success';
            $_SESSION['flash'] = "Commentaire éditer avec succés";
            post($params[1]);
            break;

        case 'managePost' :
            $categories = getCategories();
            generate('PostAdmin', 'Administration - Gestion des posts', array('categories' => $categories));
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

/**
 * Gather all data to display a post and all their comments
 * @param $param
 * @param int $page
 * @throws Exception
 */
function post($param, $page = 1){
    $post = getPost($param);
    if($post['name'] == '')
        throw new Exception('Post non valide');

    $post['categories'] = getCategories($post['id']);
    
    $_SESSION['current'] = generateURL('post', $post['slug']);
    $comments = getComments($post['id'], $page);
    generate('post', $post['name'], array('post' => $post, 'comments' => $comments));
}

/**
 * Call addComment and redirect to the post page
 * @param $userId
 * @param $content
 * @param $postId
 * @throws Exception
 */
function comment($userId, $content, $postId){
    addComment($userId, $content, $postId);
    post($postId);
}


/**
 * Route login request
 * @param null $userName
 * @param null $userPassword
 * @throws Exception
 */
function loginPage($userName = null, $userPassword = null){

    if(is_null($userName)){
        generate('login', 'Enregistrement');
    }else{
        login($userName, $userPassword);
        $_SESSION['flash_class'] = 'success';
        $_SESSION['flash'] = "Successfully Login";
        index();
    }
}

/**
 * Route register request
 * @param null $userName
 * @param null $userPassword
 * @param null $userPasswordConfirmation
 * @throws Exception
 */
function registerPage($userName = null, $userPassword = null, $userPasswordConfirmation = null){
    if(is_null($userName)){
        generate("Register", 'Inscription');
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

/**
 * Route logout request
 */
function logoutPage(){
    logout();
    $_SESSION['flash_class'] = 'success';
    $_SESSION['flash'] = "Successfully Logout";
    index();
}

/**
 * Return true if the user is logged and admin.
 * @return bool
 */
function isAdmin(){
    return (isset($_SESSION['user_right']) && $_SESSION['user_right'] == 50) ? true : false;
}