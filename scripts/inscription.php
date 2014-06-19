
<?php
include('functions.php');
$db = db_connect();

$msg ="";
if (isset($_POST['submit'])){
	$login = $_POST['login'];
	$pass = $_POST['password'];   
	// On crée une requête pour rechercher un compte ayant pour nom $login
	$query = $db->prepare("SELECT * FROM chat_accounts WHERE account_login = :login");
	$query->execute(array(
	    'login' => $login
	));
	// On compte le nombre d'entrées
	$count=$query->rowCount();
	            
	// Si ce nombre est nul, alors on crée le compte, sinon on le connecte simplement
	if($count == 0) {           
	    // Création du compte
	    $insert = $db->prepare('
	        INSERT INTO chat_accounts (account_id, account_login, account_pass) 
	        VALUES(:id, :login, :pass)
	    ');
	    $insert->execute(array(
	        'id' => '',
	        'login' => htmlspecialchars($login),
	        'pass' => md5($pass)
	    ));
	    
	    $msg = 'Inscription réussite. Aller à la <a href="chat.php">page de connexion </a> pour commencer'  ;         

	} else {
	    $msg = "Login déja utilisé";
	}
	            
	// On termine la requête
	$query->closeCursor();

}
?>
	<div>
		<div>Formulaire d'inscription</div>
		<form action="#" method="POST">
			<div><label>Name</label> <input name="name"/></div>
			<div><label>Last name</label> <input name="lastname"/></div>
			<div><label>Login</label> <input name="login"/></div>
			<div><label>Password</label> <input name="password"/></div>
			<div><label>Email</label> <input name="Email"/></div>
			<div><input type="submit" name ="submit" value="Valider"></div>
		</form>
		<div><?php echo $msg ?> </div>
	</div>

