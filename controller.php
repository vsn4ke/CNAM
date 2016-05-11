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
    $data = $postList['data'];
    $detail = null;
    for($i = 0; $i< count($data); $i++){
        if(isset($data[$i])){
            $detail[$i] = getPost($data[$i]['id']);
            $detail[$i]['categories'] = getCategories($data[$i]['id']);
        }
    }

    generate("Category", $cat['name'], array('category' => $cat, 'postList' => $postList, 'detail' => $detail));
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
        // Reach Admin page
        case 'managePost' :
            generate('AdminPost', 'Administration - Gestion des posts', array('posts' => getPosts(), 'categories' => getCategories()));
            break;

        case 'manageUser' :
            generate('AdminUser', 'Administration - Gestion des utilisateurs', array('userList' => getUserList(), 'rights' => getRights()));
            break;

        case 'manageCategory' :
            generate('AdminCategory', 'Administration - Gestion des catégories', array('categories' => getCategories()));
            break;

        case 'manageDatabase' :
            generate('AdminDatabase', 'Administration - Gestion de la base de donnée', array());
            break;

        // Category (ajax query)
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

        // Post (ajax query)
        case 'editPost' :
            if (editPost($params[0], $params[1], $params[2], $params[3]))
                echo 'ok';
            break;

        case 'addPost' :
            if (addPost($params[0], $params[1], $params[2], $params[3]))
                echo 'ok';
            break;

        case 'deletePost' :
            if (deletePost($params[0]))
                echo 'ok';
            break;

        case 'changeRight':
            if(changeRight($params[0], $params[1]))
                echo 'ok';
            break;

        // User

        case 'deleteUser' :
            if (deleteUser($params[0]))
                echo 'ok';
            break;

        case 'purgeUser' :
            if(purgeUser()){
                $_SESSION['flash_class'] = 'success';
                $_SESSION['flash'] = 'Base de donnée purgée avec succés.';
            }
            adminFunction('manageUser');
            break;

        // Comment
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


        // Database
        case 'backup' :
            backupTables($params);
            $_SESSION['flash_class'] = 'success';
            $_SESSION['flash'] = 'Backup réalisé avec succès';
            index();
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
        throw new Exception('Article non valide');

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
function loginPage($userName, $userPassword){
    try{
        login($userName, $userPassword);
    }catch (Exception $e){
        throw new Exception($e->getMessage());
    }

    $_SESSION['flash_class'] = 'success';
    $_SESSION['flash'] = "Connexion réalisé avec succès.";
    index();
}

/**
 * Route register request
 * @param null $userName
 * @param null $userPassword
 * @param null $userPasswordConfirmation
 * @throws Exception
 */
function registerPage($userName, $userPassword, $userPasswordConfirmation){
    if(count($userName) < 6)
        throw new Exception("Nom d'utilisateur trop court. Un minimum de 6 caractères est demandé.");

    if(count($userPassword) < 6)
        throw new Exception("Mot de passe trop court. Un minimum de 6 caractères est demandé.");

    if($userPassword != $userPasswordConfirmation)
        throw new Exception("Le mot de passe et la confirmation ne sont pas identique.");

    try{
        register($userName, $userPassword);
    }catch (Exception $e){
        throw new Exception($e->getMessage());
    }

    $_SESSION['flash_class'] = 'success';
    $_SESSION['flash'] = "Enregistrement réussi. Vous êtes désormais connecté.";
    index();

}

/**
 * Route logout request
 */
function logoutPage(){
    logout();
    $_SESSION['flash_class'] = 'success';
    $_SESSION['flash'] = "Déconnexion réussie.";
    index();
}

/**
 * Return true if the user is logged and admin.
 * @return bool
 */
function isAdmin(){
    return (isset($_SESSION['user_right']) && $_SESSION['user_right'] == 50) ? true : false;
}