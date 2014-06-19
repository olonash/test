<?php
session_start();
include('functions.php');
// Appel de la fonction de connexion à la base de données
$db = db_connect();

$json['groups'] = '';
if(user_verified()) {
	$query = $db->prepare('
		SELECT * FROM chat_groups WHERE groups_status = :status ORDER BY groups_id ASC
	');
	$query->execute(array(
		'status' => 1
	));
	$count = $query->rowCount();
	if($count != 0) {
		while ($data = $query->fetch()) {
			$json['groups'] .= '<li class ="setgroup" onclick = "setGroup('.$data['groups_id'].')">'. $data['groups_name'] . '</li>';
		}
	}
	echo json_encode($json);
}
?>