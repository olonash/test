<?php
session_start();
include('functions.php');
$db = db_connect();
?>

<?php
// On vérifie que l'utilisateur est inscrit dans la base de données
/*$query = $db->prepare("
	SELECT *
	FROM chat_online
	WHERE online_user = :user 
");
$query->execute(array(
	'user' => $_SESSION['id']
));
// On compte le nombre d'entrées
$count = $query->rowCount();
$data = $query->fetch();

if(user_verified()) {
	
	if($count == 0) {
		$insert = $db->prepare('
			INSERT INTO chat_online (online_id, online_ip, online_user, online_status, online_time) 
			VALUES(:id, :ip, :user, :status, :time)
		');
		$insert->execute(array(
			'id' => '',
			'ip' => $_SERVER["REMOTE_ADDR"],
			'user' => $_SESSION['id'],
			'status' => '2',
			'time' => time()
		));
	} else {
		$update = $db->prepare('UPDATE chat_online SET online_time = :time WHERE online_user = :user');
		$update->execute(array(
			'time' => time(),
			'user' => $_SESSION['id']
		));
	}
}

$query->closeCursor();*/
?>

<?php
// On supprime les membres qui ne sont pas sur le chat,
// donc qui n'ont pas actualisé automatiquement ce fichier récemment
/*$time_out = time()-5;
$delete = $db->prepare('DELETE FROM chat_online WHERE online_time < :time');
$delete->execute(array(
	'time' => $time_out
));*/
?>


<?php
$query = $db->prepare("
	SELECT online_id, online_user, online_status , account_id, account_login
	FROM chat_online 
	LEFT JOIN chat_accounts ON chat_accounts.account_id = chat_online.online_user 
	LEFT JOIN chat_groupsaccounts on chat_groupsaccounts.groupsaccounts_groupid = :groupid
	ORDER BY online_time DESC

");


$query->execute(array(
	'groupid' => $_SESSION['groupid']
));
// On compte le nombre de membres
$count = $query->rowCount();

/* Si au moins un membre est connecté, on l'affiche.
Sinon, on affiche un message indiquant que personne n'est connecté */
if($count != 0) {
	// On affiche qu'il n'y a aucune erreur
	$json['error'] = '0';
	
	$i = 0;
	$array = array();
	
	while($data = $query->fetch()) {
		if(!in_array($data['online_user'], $array)){
			if($data['online_status'] == '0') {
				$status = 'inactive';
			} elseif($data['online_status'] == '1') {
				$status = 'busy';
			} elseif($data['online_status'] == '2') {
				$status = 'active';
			}
			
			// On enregistre dans la colonne [status] du tableau
			// le statut du membre : busy, active ou inactive (occupé, en ligne, absent)
			$infos["status"] = $status;
			// Et on enregistre dans la colonne [login] le pseudo
			$infos["login"] = $data['account_login'];
			
			// Enfin on enregistre le tableau des infos de CE MEMBRE
			// dans la [i ème] colonne du tableau des comptes 
			$accounts[$i] = $infos;
			$i++;
			$array[] = $data['online_user'];
		}
	}

	//print_r($array); die;
	// On enregistre le tableau des comptes dans la colonne [list] de JSON
	$json['list'] = $accounts;
} else {
	// Il y a une erreur, aucun membre dans la liste
	$json['error'] = '1';
}

$query->closeCursor();

// Encodage de la variable tableau json et affichage
echo json_encode($json);
?>