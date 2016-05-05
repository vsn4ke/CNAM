<?php

/*
    -- Core
 */

/**
 * Create the object PDO if it doesn't exist and return it.
 *
 * @return PDO
 */
function getDb()
{
    global $db;
    if($db == null){
        $db = new PDO('mysql:host=localhost;dbname=project_CNAM;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    return $db;
}

/**
 * @param $sql : String Query
 * @param array || null $params : all parameters to bind in the query
 * @return PDOStatement
 */
function executeRequest($sql, $params = null)
{
    if($params == null){
        $result = getDb()->query($sql);
    }else{

        $result = getDb()->prepare($sql);
        foreach($params as $key => $param){

            if(is_int($param))
                $result->bindValue($key+1,$param, PDO::PARAM_INT);
            else
                $result->bindValue($key+1,$param);

        }
        $result->execute();
    }
    return $result;
}

/**
 * @param $text : string
 * @return string : input without all special characters
 */
function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '_', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '_');
    $text = preg_replace('~-+~', '_', $text);
    $text = strtolower($text);

    if (empty($text))
        return 'n_a';

    return $text;
}

/**
 * @param $sql : query
 * @param $page : page number
 * @param $perPage : number of items per page
 * @param array $param : query's parameters
 * @return array : links : html code for the pagination, data : query result
 */
function paginator($sql, $page, $perPage, $param = array())
{
    $limit = $perPage == 'all'? $perPage : intval($perPage);
    $total = executeRequest($sql, $param)->rowCount();

    if($limit == 'all')
        $query = $sql;
    else
        $query = $sql.' LIMIT '.(($page-1)*$limit).', '.$limit;

    $request = executeRequest($query, $param);

    if ( $limit == 'all' )
        return '';

    $last = ceil($total/$limit);

    $html = '';
    if($last>1){
        $html = '<ul class="page-numbers">';
        $class = ($page == 1) ? "hidden" : "";
        $html .= '<li><a href="'.$_SESSION['current'].'-'.($page-1).'" class="'.$class.'">&laquo;</a></li>';

        for ( $i = 1 ; $i <= $last; $i++ ) {
            $class = ($page == $i ) ? "active" : "";
            $html .= '<li><a href="'.$_SESSION['current'].'-'.$i.'" class="'.$class.'">'.$i.'</a></li>';
        }

        $class = ($page == $last) ? "hidden" : "";
        $html .= '<li class="'.$class.'"><a href="'.$_SESSION['current'].'-'.($page+1).'">&raquo;</a></li>';
        $html .= '</ul>';
    }

    $result['links'] = $html;
    $result['data'] = $request->fetchAll();

    return $result;
}

/**
 * @param array $tables : null or array('*') to full backup || array of all tables to backup
 */
function backupTables($tables = array())
{
    if($tables[0] == null || $tables[0] == '*'){
        $tables = null;
        $t = executeRequest('SHOW TABLES')->fetchAll();
        for($i = 0; $i < count($t); $i++){
            $tables[] = $t[$i][0];
        }
    }

    $return = '';
    foreach($tables AS $table){
        $result = executeRequest('SELECT * FROM ' . $table);
        $num_fields = $result->columnCount();
        $return .= 'DROP TABLE ' . $table . ';' ;

        $row = executeRequest('SHOW CREATE TABLE '. $table)->fetch();
        $return .= "\n\n" . $row[1] . ";\n\n";

        $rows = $result->fetchAll();


        foreach($rows as $r){
            $return .= 'INSERT INTO '.$table.' VALUES(';
            for($j = 0 ; $j < $num_fields; $j++){
                $r[$j]=preg_replace("/\n/","\\n",$r[$j]);
                $r[$j]=preg_replace("/\r/","",$r[$j]);
                if(isset($r[$j])){$return .= '"' . $r[$j] . '"' ; } else { $return .= '""'; }
                if($j < ($num_fields -1)) { $return .= ','; }
            }
            $return .= ");\n";
        }
        $return .= "\n\n\n";
    }

    $handle = fopen('SQL/db-backup-'.time().'-'.(md5(implode(',', $tables).time())).'.sql', 'w+');
    fwrite($handle, $return);
    fclose($handle);
}

/*
 *  -- Category
 */

/**
 * Get all data related to Categories
 * @param null $id : specify an Post_ID to get data related to categories for a specific Post_ID
 * @return array
 */
function getCategories($id = null)
{
    $sql = 'SELECT
                ca.Cat_ID AS id,
                ca.Cat_Name AS name,
                ca.Cat_Slug AS slug,
                count(lcp.Cat_ID) AS Post_Count
            FROM tCategory AS ca
            LEFT JOIN linkCatPost AS lcp
                ON lcp.Cat_ID = ca.Cat_ID
            LEFT JOIN tPost AS p
                ON p.Post_ID = lcp.Post_ID
            :where
            GROUP BY ca.Cat_ID';

    if(!is_null($id)){
        $sql = preg_replace('/:where/',' WHERE p.Post_ID = ? ', $sql);
        $res = executeRequest($sql, array($id));
    }else{
        $sql = preg_replace('/:where/','', $sql);
        $res = executeRequest($sql);
    }

    return $res->fetchAll();
}

/**
 * Get a specific category data
 * @param $category
 * @return mixed
 */
function getCategory($category)
{
    $sql = 'SELECT
                ca.Cat_Name AS name,
                ca.Cat_Slug AS slug
            FROM tCategory AS ca
            WHERE
                ca.Cat_ID = ? OR ca.Cat_Slug = ?';

    return executeRequest($sql, array($category, $category))->fetch();
}

/**
 * Count the numbers of categories
 * @return mixed
 */
function getCategoriesCount()
{
    $sql = 'SELECT COUNT(*) AS count FROM tCategory';
    return executeRequest($sql)->fetch();
}

/**
 * Delete specific category and all links. Used with AJAX
 * @param $id
 * @return bool
 */
function deleteCategory($id)
{
    backupTables(array('tCategory', 'linkCatPost'));

    $sql = 'DELETE FROM tCategory WHERE Cat_ID = ?;
            DELETE FROM linkCatPost WHERE Cat_ID = ?';

    try{
        executeRequest($sql, array($id, $id));
        return true;
    }catch (Exception $e){
        return false;
    }
}

/**
 * Edit specific category. Used with AJAX
 * @param $id
 * @param $name
 * @return bool
 */
function editCategory($id, $name)
{
    $sql = 'UPDATE tCategory SET Cat_Name = ?, Cat_Slug = ? WHERE Cat_ID = ?';

    try{
        executeRequest($sql, array($name, slugify($name), $id));
        return true;
    }catch (Exception $e){
        return false;
    }
}

/**
 * Add a new category. Used with AJAX
 * @param $name
 * @return bool
 */
function addCategory($name)
{
    $sql = 'INSERT INTO tCategory VALUES("", ?, ?)';  // id, slug, name

    try{
        executeRequest($sql, array(slugify($name), $name));
        return true;
    }catch (Exception $e){
        return false;
    }
}

/*
 *  -- Comment
 */

/**
 * Get all comments for a specific Post_ID and a selected page
 * @param $postId
 * @param int $page
 * @return array paginator
 */
function getComments($postId, $page = 1)
{
    $sql = 'SELECT
                co.Com_ID AS id,
                co.Com_Content AS content,
                co.Com_Date as date,
                co.Com_Date_Edit AS editdate,
                u.User_Name AS username,
                eu.User_Name AS editname
            FROM tComment AS co
            JOIN tUser AS u
                ON u.User_ID = co.User_ID
            LEFT JOIN tUser AS eu
                ON eu.User_ID = co.User_ID_Edit
            WHERE co.Post_ID = ?
            ORDER BY co.Com_ID DESC';

    return paginator($sql, $page, 10, array($postId));
}

/**
 * Add a comment in the database
 * @param $userId
 * @param $content
 * @param $postId
 */
function addComment($userId, $content, $postId)
{
    $sql = 'INSERT INTO tComment(Com_Content, Com_Date, Post_ID, User_ID) VALUES (?, ?, ?, ?)';
    $date = date(DATE_W3C);
    executeRequest($sql, array($content, $date, $postId, $userId));
}

/**
 * Delete a specific comment
 * @param $id
 */
function deleteComment($id)
{
    $sql = 'DELETE FROM tComment WHERE Com_ID = ?';
    executeRequest($sql, array($id));
}

/**
 * Edit a specific comment. The userID is the editor's id.
 * @param $id
 * @param $content
 * @param $userID
 */
function editComment($id, $content, $userID)
{
    $sql = 'UPDATE tComment SET Com_Content = ?, User_ID_Edit = ?, Com_Date_Edit = NOW() WHERE Com_ID = ?';
    executeRequest($sql, array($content, $userID, $id));
}

/*
 *  -- Post
 */

/**
 * Get the list  of all post for a specific Cat_ID or Cat_Slug (param) and for a given page number.
 * @param $param
 * @param int $page
 * @return array paginator
 */
function getPostList($param, $page=1)
{

    $sql = 'SELECT
                  p.Post_ID AS id,
                  p.Post_Name AS name,
                  p.Post_Slug AS slug
            FROM tPost AS p
            JOIN linkCatPost AS lcp
                ON lcp.Post_ID = p.Post_ID
            JOIN tCategory AS ca
                ON ca.Cat_ID = lcp.Cat_ID
            WHERE ca.Cat_ID = ? OR ca.Cat_Slug = ?
            GROUP BY p.Post_ID';

    return paginator($sql, $page, 8, array($param, $param));
}

/**
 * Get all data for a specific Post_ID or Post_Slug (param)
 * @param $param
 * @return mixed
 */
function getPost($param)
{
    $sql = 'SELECT
                p.Post_Name AS name,
                p.Post_Content AS content,
                p.Post_Date AS date,
                p.Post_ID as id,
                p.Post_Slug AS slug,
                u.User_Name AS username,
                count(co.Com_ID) AS Com_Number
            FROM tPost AS p
            JOIN tUser AS u
                ON p.User_ID = u.User_ID
            LEFT JOIN tComment AS co
                ON co.Post_ID = p.Post_ID
            WHERE p.Post_ID = ? OR p.Post_Slug = ?';
    return executeRequest($sql, array($param, $param))->fetch();
}

function getPosts()
{
    $sql = 'SELECT
                p.Post_Name AS name,
                p.Post_Content AS content,
                p.Post_Date AS date,
                p.Post_ID as id,
                p.Post_Slug AS slug,
                u.User_Name AS username,
                count(co.Com_ID) AS Com_Number
            FROM tPost AS p
            JOIN tUser AS u
                ON p.User_ID = u.User_ID
            LEFT JOIN tComment AS co
                ON co.Post_ID = p.Post_ID
            GROUP BY p.Post_ID';

    return executeRequest($sql)->fetchAll();
}

/**
 * Return up to 5 posts data order by comment numbers
 * @return PDOStatement
 */
function popularPostList()
{
    $sql = 'SELECT
                p.Post_Name AS name,
                p.Post_Slug AS slug,
                count(co.Com_ID) AS Com_Number
            FROM tPost AS p
            JOIN tComment AS co
                ON co.Post_ID = p.Post_ID
            GROUP BY co.Post_ID
            ORDER BY Com_Number DESC LIMIT 0, 5';

    return executeRequest($sql);
}

/**
 * @param $name
 * @param $content
 * @param $userID
 * @param $categories
 * @return bool
 */
function addPost($name, $content, $userID, $categories){


    try{
        $sql = 'INSERT INTO tPost
                VALUES("", ?, ?, NOW(), ?, ?, "", "");';  // id, name, content, date, slug, user_ID, User_ID_Edit, Post_Date_Edit

        executeRequest($sql,array($name, $content, slugify($name), $userID));
        $id = getDb()->lastInsertId();
        $sql = '';

        $params = array();
        foreach($categories as $category){
            $sql .= 'INSERT INTO linkCatPost VALUES(?, ?);';
            $params[] = intval($category);
            $params[] = $id;
        }

        executeRequest($sql, $params);
        return true;
    }catch (Exception $e){
        return false;
    }
}

/**
 * @param $id
 * @return bool
 */
function deletePost($id){
    backupTables(array('tPost', 'linkCatPost', 'tComment'));

    $sql = 'DELETE FROM tPost WHERE Post_ID = ?;
            DELETE FROM linkCatPost WHERE Post_ID = ?;
            DELETE FROM tComment WHERE Post_ID = ?';

    try{
        executeRequest($sql, array($id, $id, $id));
        return true;
    }catch (Exception $e){
        return false;
    }
}

/**
 * @param $id
 * @param $name
 * @param $content
 * @param $userID
 * @return bool
 */
function editPost($id, $name, $content, $userID){
    $sql = 'UPDATE tPost
            SET  Post_Name = ?, Post_Content = ?, Post_Slug = ?, User_ID_Edit = ?, Post_Date_Edit = NOW()
            WHERE Post_ID = ?';

    try{
        executeRequest($sql, array($name, $content, slugify($name), $userID,  $id));
        return true;
    }catch (Exception $e){
        return false;
    }
}

/*
 * -- User
 */

/**
 * Add a new user in the database. If succeed, the new user is logged in.
 * @param $userName
 * @param $userPassword
 * @throws Exception
 */
function register($userName, $userPassword)
{
    try{
        $newPassword = password_hash($userPassword, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO tUser(User_Name, User_Hash, User_Right) VALUES(?, ?, ?)';
        $right = 1;
        executeRequest($sql, array($userName, $newPassword, $right));
        $id = getDb()->lastInsertId();
        // si tout ce passe bien on peut log l'utilisateur
        setSession($userName, $right, $id);
    }catch (Exception $e){
        throw new Exception("Utilisateur déjà enregistré.");
    }
}

/**
 * Logged in a user
 * @param $userName
 * @param $userPassword
 * @throws Exception
 */
function login($userName, $userPassword)
{
    $sql = 'SELECT User_Hash AS hash, User_ID AS id, User_Right AS uRight FROM tUser WHERE User_Name = ?';
    $result = executeRequest($sql, array($userName));
    $user = $result->fetch(PDO::FETCH_ASSOC);

    if($result->rowCount() >0){
        if(password_verify($userPassword, $user['hash'])){
            setSession($userName,$user['uRight'], $user['id']);
        }else
            throw new Exception("Le mot de passe ne coincide pas avec le nom d'utilisateur.");
    }else
        throw new Exception("Nom d'utilisateur incorrect.");
}

/**
 * Destroy all user session related variables.
 * @return bool
 */
function logout()
{
    session_destroy();
    unset($_SESSION['user_name']);
    unset($_SESSION['user_right']);
    unset($_SESSION['CSRF']);
    unset($_SESSION['current']);
    unset($_SESSION['id']);
    return true;
}

/**
 * Small function used in login / register to create user session variables
 * @param $userName
 * @param $right
 * @param $id
 */
function setSession($userName, $right, $id){
    $_SESSION['user_name'] = $userName;
    $_SESSION['user_right'] = $right;
    $_SESSION['CSRF'] = md5(uniqid(rand(), true));
    $_SESSION['id'] = $id;
}


/*
 * -- View
 */

/**
 * Gather all data and create an html code using the layout and the page required (action)
 * @param $action
 * @param string $title
 * @param array $data
 * @throws Exception
 */
function generate($action, $title = '', $data = array())
{
    $file = 'View/' . $action . 'View.php';
    $content = generateFile($file, $data);
    $flash = getFlashMessage();
    $catList = getCategories();
    $popularPostList = popularPostList();
    $view = generateFile(
        'View/Layout.php',
        array(
            'title' => $title,
            'content' => $content,
            'flash' => $flash,
            'catList' => $catList,
            'popularPostList' => $popularPostList
        )
    );

    echo $view;
}

/**
 * Extract all variables from $data and inject them in the view file required. Then send back the file (string).
 * @param $file
 * @param $data
 * @return string
 * @throws Exception
 */
function generateFile($file, $data)
{
    if(file_exists($file)){
        extract($data);
        ob_start();
        require $file;
        return ob_get_clean();
    }else{
        throw new Exception("File '$file' not found.");
    }

}

/**
 * Clean all inputed string and return it.
 * @param $value
 * @return string
 */
function sanitize($value)
{
    return nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false));
}

/**
 * Create a valid URL to use. Edit the pattern to adapt it.
 * @param $action
 * @param null $param
 * @return string
 */
function generateURL($action, $param = null)
{
    $url =   '/CNAM/' . sanitize($action);
    if(!is_null($param)){
        $url .=  '/' . sanitize($param);
    }

    return $url;
}

/**
 * Create a valid View URL used for css for exemple.
 * @param $action
 * @param null $param
 * @return string
 */
function generateViewURL($action, $param = null)
{
    $url = '/CNAM/View/';
    if(!is_null($param)){
        $url .= $param . '/';
    }
    $url .= $action;
    return $url;
}

/**
 * Get the flash session, empty it and return an array to use on the next page
 * @return array
 */
function getFlashMessage()
{

    $flash['message'] = isset($_SESSION['flash']) && $_SESSION['flash'] != '' ? $_SESSION['flash'] : '';
    $flash['class'] = isset($_SESSION['flash_class']) && $_SESSION['flash_class'] != '' ? $_SESSION['flash_class'] : '';
    $_SESSION['flash'] = '';
    $_SESSION['flash_class'] = '';
    return $flash;
}

/**
 * Truncate and sanitize a string, length can be adjusted.
 * @param $content
 * @param int $length
 * @return string
 */
function truncate($content, $length = 200)
{
    if(strlen($content) > $length ){
        $content = rtrim(mb_strimwidth($content, 0, $length))."...";
    }
    return sanitize($content);
}