<?php
session_start();
include('scripts/functions.php');
$db = db_connect();

$msg = "";
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/common.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="js/chat.js"></script>	
</head>

<body>
	<div id="container">
		<h1>Test chat</h1>
		<?php
		
		//deconnexion
		if(isset($_GET['logout']) && $_GET['id'] && $_GET['logout']==1){
			unset($_SESSION['id']);
			unset($_SESSION['time']);
			unset($_SESSION['login']);	
			unset($_SESSION['groupid']);
		}


		// permettra de créer l'utilisateur lors de la validation du formulaire
		if(isset($_POST['login']) AND !empty($_POST['login'])  AND !preg_match("#^[-. ]+$#", $_POST['login'])) {
			/* On crée la variable login qui prend la valeur POST envoyée
			car on va l'utiliser plusieurs fois */
			$login = $_POST['login'];
			$pass = $_POST['pass'];   
			// On crée une requête pour rechercher un compte ayant pour nom $login
			$query = $db->prepare("SELECT * FROM chat_accounts WHERE account_login = :login");
			$query->execute(array(
			    'login' => $login
			));
			// On compte le nombre d'entrées
			$count=$query->rowCount();          
			// Si ce nombre est nul, alors on crée le compte, sinon on le connecte simplement
			if($count == 0) {           
			    $msg = 'login incorrect';
			} else {
			    $data = $query->fetch();                
			    if($data['account_pass'] == md5($pass)) {           
			        $_SESSION['id'] = $data['account_id'];
			        // On crée une session time qui prend la valeur de la date de connexion
			        $_SESSION['time'] = time();
			        $_SESSION['login'] = $data['account_login'];
			        $_SESSION['groupid'] = 1;

			        //set status
			        $insert = $db->prepare('
					INSERT INTO chat_online (online_id, online_ip, online_user, online_status, online_time) 
					VALUES(:id, :ip, :user, :status, :time)
					');
					$insert->execute(array(
							'id' => '',
							'ip' => $_SERVER["REMOTE_ADDR"],
							'user' => $_SESSION['id'],
							'status' => 2,
							'time' => time()
					));

					//set group
			        $insert = $db->prepare('
					INSERT INTO chat_groupsaccounts (groupsaccounts_accounid, groupsaccounts_groupid, groupsaccounts_creationdate) 
					VALUES(:accountid, :groupidip, :time)
					');
					$insert->execute(array(
							'accountid' => $_SESSION['id'],
							'groupidip' => 1,
							'time' => date("Y-m-d h:i:s")
					));

			    }else{
			    	$msg = 'password incorrect';
			    }
			}
			            
			// On termine la requête
			$query->closeCursor();
		}else{
			$msg = 'login or password incorrect';
		}

		/* Si l'utilisateur n'est pas connecté, 
		d'où le ! devant la fonction, alors on affiche le formulaire */
		if(!user_verified()) {
		?>
		<div class="unlog">
			
			<form action="" method="post">	
			
			<center>
				Connexion 
				<br><br>
				<input type="text" name="login" placeholder="Pseudo" /><br />
		                <input type="password" name="pass" placeholder="Mot de passe" /><br /> 
				<input type="submit" value="Connexion" />
				<a href="inscription.php"> Inscription</a>
				<div><?php echo $msg ?></div>
			</center>
			
			</form>
			
		</div>
		<?php
		} else {
		?>
	    
	    <!-- Statut //////////////////////////////////////////////////////// -->				
		<table class="status">
			<tr>
				<td><a href="chat.php?logout=1&id=<?php echo $_SESSION['id'] ?>">Deconnexion</a>
					<span id="statusResponse"></span>
					<select name="status" id="status" style="width:200px;" onchange="setStatus(this)">
						<option value="0">Absent</option>
						<option value="1">Occup&eacute;</option>
						<option value="2" selected>En ligne</option>
					</select>
					<button onclick="getOnlineUsers()"> aaaaa</button>
				</td>
			</tr>
		</table>

		<table class="chat">
			<tr>		
				<!-- zone des messages -->
				<td valign="top" id="text-td">
			            	<div id="annonce"></div>
							<div id="text">
								<div id="loading">
									<center>
									<span class="info" id="info">Chargement du chat en cours...</span><br />
									<img src="ajax-loader.gif" alt="patientez...">
									</center>
								</div>
							</div>
				</td>
						
				<!-- colonne avec les membres connectés au chat -->
				<td valign="top" id="users-td">
					<!--button onclick="getGroups()"> getGroups</button-->
					<div >Liste des groups</div>
					<ul id="group-list">
						<li>Groups1</li>
						<li>Group2</li>
					</ul>
					<div>Liste d'utilisateur</div>
					<ul id="users">
						<li>Chargement</li>
					</ul>
				</td>
			</tr>
		</table>
		

		<!-- Zone de texte //////////////////////////////////////////////////////// -->
	    <a name="post"></a>
		<table class="post_message">
			<tr>
				<td>
				<form action="" method="" onsubmit="postMessage(); return false;">
					<input type="text" id="message" maxlength="255" />
					<input type="button" onclick="postMessage()" value="Envoyer" id="post" />
				</form>
		                <div id="responsePost" style="display:none"></div>
				</td>
			</tr>
		</table>
		<?php } ?>
	</div>

	<input type="hidden" id="dateConnexion" value="<?php echo $_SESSION['time']; ?>" />

</body>
</html>