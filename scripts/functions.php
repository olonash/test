<?php
session_start();

/**
* connexion à la base de données
*
*/
function db_connect() {
    // définition des variables de connexion à la base de données   
    try {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $host 		= 'localhost';
        $dbname 	= 'chat_ajax';
        $user 		= 'root';
        $password 	= 'root';
        $db = new PDO('mysql:host='.$host.';dbname='.$dbname.'', $user, $password, $pdo_options);
        return $db;
    } catch (Exception $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }
}

function user_verified() {
    //print_r($_SESSION['id']); die;
    return isset($_SESSION['id']);
}

function urllink($content='') {
    $content = preg_replace('#(((https?://)|(w{3}\.))+[a-zA-Z0-9&;\#\.\?=_/-]+\.([a-z]{2,4})([a-zA-Z0-9&;\#\.\?=_/-]+))#i', '<a href="$0" target="_blank">$0</a>', $content);
    // Si on capte un lien tel que www.test.com, il faut rajouter le http://
    if(preg_match('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', $content)) {
        $content = preg_replace('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', '<a href="http://www.$1" target="_blank">www.$1</a>', $content);
        //preg_replace('#<a href="www\.(.+)">#i', '<a href="http://$0">$0</a>', $content);
    }
    $content = stripslashes($content);
    return $content;
}
?>