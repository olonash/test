<?php
session_start();
include('functions.php');
// Appel de la fonction de connexion à la base de données
$db = db_connect();

if(user_verified()) {
	$_SESSION['groupid'] = $_POST['groupid'];	
	/*$query = $db->prepare('
		SELECT * FROM chat_groupsaccounts WHERE groupsaccounts_accountid = :accountid AND groupsaccounts_groupid =:groupid
	');
	$query->execute(array(
		'accountid' => $_SESSION['id'],
		'groupid' 	=> $_SESSION['groupid']
	));*/
	/*$count = $query->rowCount();
	if ($count == 0){
		//set group
	    $insert = $db->prepare('
		INSERT INTO chat_groupsaccounts (groupsaccounts_accountid, groupsaccounts_groupid, groupsaccounts_creationdate) 
		VALUES(:accountid, :groupidip, :time)
		');
		$insert->execute(array(
				'accountid' => $_SESSION['id'],
				'groupidip' => $_SESSION['groupid'],
				'time' => date("Y-m-d h:i:s")
		));
	}*/
}
?>