<?php
define('BASE_PATH', realpath(dirname(__FILE__)));

require_once('controller.php');
require_once('model.php');

session_start();

$param = isset($_GET['param']) ? explode('-', $_GET['param']) : '';

try{
    switch(isset($_GET['action'])?$_GET['action']:''){

        case 'login':
            if(isset($_POST['submited'])){
                $userName = getParams($_POST, 'userName');
                $userPassword = getParams($_POST, 'userPassword');
                loginPage($userName,$userPassword);
            }else{
                loginPage();
            }
            break;
        case 'register':
            if(isset($_POST['submited'])){
                $userName = getParams($_POST, 'userName');
                $userPassword = getParams($_POST, 'userPassword');
                $userPasswordConfirmation = getParams($_POST, 'userPasswordConfirmation');
                registerPage($userName, $userPassword, $userPasswordConfirmation);
            }else{
                registerPage();
            }
            break;
        case 'logout':
            logoutPage();
            break;
        case 'viewCat':
            category($param[0]);
            break;
        case 'post':
            $page = isset($param[1]) ? intval($param[1]) : 1;
            post($param[0], $page);
            break;
        case 'comment':
            if(isset($_POST['CSRFToken']) && $_POST['CSRFToken']  == $_SESSION['CSRF']){
                $content = getParams($_POST, 'message');
                $postId = getParams($_POST, 'id');
                $userId = $_SESSION['id'];
                comment($userId, $content, $postId);
            }else
                throw new Exception("Vous n'avez pas le droit de commenter.");
            break;
        case 'deleteComment' :
            adminFunction('deleteComment', array($param[0], $param[1]));
            break;
        case 'editComment' :
            if(isset($_POST['CSRFToken']) && $_POST['CSRFToken']  == $_SESSION['CSRF']) {
                $content = getParams($_POST, 'message');
                adminFunction('editComment', array($param[0], $param[1], $content));
            }else{
                throw new Exception("Vous n'avez pas le droit d'éditer les commentaires.");
            }
            break;
        case 'admin' :
            switch($param[0]){
                case 'post':
                    adminFunction('managePost');
                    break;
                case 'user':
                    adminFunction('manageUser');
                    break;
                case 'category' :
                    adminFunction('manageCategory');
                    break;
                case 'database' :
                    adminFunction('manageDatabase');
                    break;
                case 'backup':
                    adminFunction('backup', array('*'));
                    break;
                case 'purgeUser':
                    if(isset($_POST['CSRFToken']) && $_POST['CSRFToken']  == $_SESSION['CSRF'])
                        adminFunction('purgeUser');
                    else
                        throw new Exception("Vous n'avez pas le droit d'éditer les commentaires.");
                    break;
                default:
                    throw new Exception("Paramètre invalide");
            }
            break;
        case 'ajax':
            switch($param[0]){
                case 'deleteCategory' :
                    adminFunction('deleteCategory', array($param[1]));
                    break;
                case 'editCategory' :
                    $content = getParams($_POST, 'content');
                    adminFunction('editCategory', array($param[1], $content));
                    break;
                case 'addCategory' :
                    $content = getParams($_POST, 'content');
                    adminFunction('addCategory', array($content));
                    break;
                case 'deletePost' :
                    adminFunction('deletePost', array($param[1]));
                    break;
                case 'addPost' :
                    $name = getParams($_POST, 'name');
                    $content = getParams($_POST, 'content');
                    $categories = getParams($_POST, 'categories');
                    $userId = $_SESSION['id'];
                    adminFunction('addPost', array($name, $content, $userId, $categories));
                    break;
                case 'editPost' :
                    $id = $param[1];
                    $name = getParams($_POST, 'name');
                    $content = getParams($_POST, 'content');
                    $userId = $_SESSION['id'];
                    adminFunction('editPost', array($id, $name, $content, $userId));
                    break;
                case 'deleteUser' :
                    adminFunction('deleteUser', array($param[1]));
                    break;
            }
            break;
        case 'contact' :
            generate('Contact', 'Contact');
            break;

        default:
            index();
    }
}catch (Exception $e){
    $_SESSION['flash_class'] = 'warning';
    $_SESSION['flash'] = $e->getMessage();
    switch(isset($_GET['action'])?$_GET['action']:''){
        case 'login':
            loginPage();
            break;
        case 'register':
            registerPage();
            break;
        default:
            index();
    }
}

/**
 * return the value of an array index. Used to get global variable field and check their existence.
 * ex: getParams($_Post, 'id') return the id field of $_Post
 *
 * @param $tab
 * @param $name
 * @return mixed
 * @throws Exception
 */
function getParams($tab, $name){
    if(isset($tab[$name]))
        return $tab[$name];
    else
        throw new Exception("Paramètre '$name' non trouvé");
}