<!DOCTYPE html>
<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location:index.php');
	exit;
}

// [...]

$config = include('../../config.php');

$db_host = $config['host'];
$db_port = $config['port'];
$db_username = $config['username'];
$db_password = $config['password'];
$table = $_SESSION['tabelle'];
$db_database = $config['schul_index_database'];

$db = pg_connect("host=$db_host port=$db_port dbname=$db_database user=$db_username password=$db_password");
?>

<html lang="de" >
	<head>
		<meta charset="utf-8">
		<title>Virtuelle Schulverwaltung</title>
		<link rel="stylesheet" href="../panelstyle.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<script src="https://kit.fontawesome.com/4ef9acb6f3.js" crossorigin="anonymous"></script>
		
		<script src="scripts\jquerry.js"></script>
		<script src="scripts\info.js"></script>
	</head>

	<?php include('../headinfo.php'); ?>
	<?php include('../sidebar.php'); ?>

	<body>

		<div id="main">

			<h3 class="headline">Einen neuen Benutzer anlegen</h3>

			<form class="" action="createnewlocaluser.php" method="post">

	      <div class="sidecontentnewuser" style="margin-top:80px">

	        <h3>Die eindeutige ID des Benutzers (automatisch generiert)</h3>

					<?php
					
					//Gibt es die ID Bereits ?

					$counter = -1;
					$rows = 0;
					
					do {
						$counter++;
						$query = "SELECT * FROM $table WHERE id = '$counter'";
						$result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());
						$rows = pg_num_rows($result);
					} while ($rows > 0);

					 ?>

	          <input type="text" name="id" value="<?php echo $counter; ?>" readonly>

	        <p>Die eindeutige ID dient dazu den Benutzer innerhalb der Schule eindeutig zu identifizieren. Um Probleme und Fehler zu vermeiden, wir diese automatisch generiert</p>

	      </div>

				<div class="sidecontentnewuser">

	        <h3>Vorname und Nachname (Eingabe nötig)</h3>

	          <input type="text" id="vorname" name="vorname" value="">
	          <input type="text" id="nachname" name="nachname" value="">

	        <p>Geben den Vornamen und Nachnamen der Person ein.</p>

				</div>

	      <div class="sidecontentnewuser">

	        <h3>Benutzername / Loginname (automatisch generiert)</h3>

	          <input type="text" id="loginname" style="width: 22%;" name="loginname" value="" readonly>

	        <p>Wird generiert nach der Eingabe vom Vornamen und Nachnamen</p>

				</div>

	      <div class="sidecontentnewuser">

	        <h3>Passwort (automatisch generiert)</h3>

					<input type="text" id="password" name="password" value="" readonly>

					<script type="text/javascript">

						/* !BREAKPOINT
						   TODO: fix bug 
						   Generiere ein Passwort nach ??vorgaben?? -> nachfragen */

						function createpassword(){
							var downletters = "abcdefghijkmnopqrstuvwxyz";
							var upletters = "ABCDEFGHJKMNPQRTUVW";
							var numbers = "23456789";
							var combi = downletters+upletters+numbers;
							var response = document.getElementById('password');
							var pass = "";

							pass += upletters.charAt(Math.floor(Math.random() * upletters.length));

							for (var i = 0; i < 8; i++) {
								pass += combi.charAt(Math.floor(Math.random() * combi.length));
							}

							for (var j = 0; j < pass.length; j++) {
								var number = 0;

								for (var k = 0; k < numbers.length; k++) {
									if (pass.charAt(j) == numbers.charAt(k)) {
										number = 1;
									}
								}

								if (j == pass.length-1 && number == 0) {
									createpassword();
								}
							}
							response.value=pass;
						}


						createpassword();

					</script>

	        <p>Das Startpasswort wird automatisch generiert, sie erhalten es nachdem der Benutzer erstellt wurde</p>

				</div>

				<div class="sidecontentnewuser">

	        <h3>Zweifaktor (automatisch generiert)</h3>

	          <input type="text" id="zeifaktor" style="width: 22%;" name="zweifaktor" value="" readonly>

						<script type="text/javascript">
						
						/* Erstellen einen Zweiten Faktor ()
						
							Vorgabe: Großbuchstabe + Kombination aus Großbuchstaben und Zahlen
						
						*/

						function createzweifaktor(){
							var upletters = "ABCDEFGHJKMNPQRTUVW";
							var numbers = "234567";
							var combi = upletters+numbers;
							var response = document.getElementById('zeifaktor');
							var pass = "";

							pass += upletters.charAt(Math.floor(Math.random() * upletters.length));

							for (var i = 1; i < 16; i++) {
								pass += combi.charAt(Math.floor(Math.random() * combi.length));
							}

							response.value=pass;
						}


						createzweifaktor();

						</script>

	        <p>Dieses Feld wird automatisch ausgefüllt</p>

				</div>

	      <div class="sidecontentnewuser">

	        <h3>Email (Eingabe nötig)</h3>

	          <input type="text" name="email" value="" style="width: 22%;">

						<p>Geben die Email des Benutzer ein</p>

						<br>
						<br>
						<h3>Aliase (Eingabe optional)</h3>
						<br>

	          <input type="text" name="" value="" style="width: 22%;">
						<br>
	          <input type="text" name="" value="" style="width: 22%;">
						<br>
	          <input type="text" name="" value="" style="width: 22%;">
						<br>
	          <input type="text" name="" value="" style="width: 22%;">
						<br>
	          <input type="text" name="" value="" style="width: 22%;">

	        <p>Geben sie otional bis zu 5 weitere Aliase ein</p>

				</div>

	      <div class="sidecontentnewuser">

	        <h3>Kürzel (Eingabe optional)</h3>

	          <input type="text" id="kuerzel" name="kuerzel" value="">

	        <p>Geben sie das Kürzel ein, es gelten folgende Regeln. Das automatisch generierte ist lediglich ein Vorschlag</p>

					<script>
					
					/*	Nutzernamen generieren nach Vorgabe 
					
						Vorgabe: R	+	SCHULNUMMER	+	"-"	+	NACHNAME	+	Erster Buchstabe vom VORNAME	
					
					*/
					
					var changed = false;
					var changed2 = false;
					var textarea = document.getElementById('vorname');
					var textarea2 = document.getElementById('nachname');
					var vorname = "";
					var nachname = "";

					textarea.onchange = function() {
						vorname = textarea.value;
						createuser();
						changed = true;
					}
					textarea.onblur = function() {
						 var response = document.getElementById('loginname');
						 if (changed === false) response.value = "";
					}

					textarea2.onchange = function() {
						nachname = textarea2.value;
						createuser();
						changed2 = true;

						if (textarea.value.length > 2 && textarea2.value.length > 2) {
							var response2 = document.getElementById('kuerzel');
							var pass = "";

							pass += textarea2.value.charAt(0)
							pass += textarea2.value.charAt(1)
							pass += textarea.value.charAt(0)

							response2.value=pass;
						}
					}
					textarea2.onblur = function() {
						 var response = document.getElementById('loginname');
						 if (changed2 === false) response.value = "";
					}

					function createuser(){
						var response = document.getElementById('loginname');

						response.value = "R"+"
						
						<?php
						$tmp = $_SESSION['tabelle'];
						$tmp2 = preg_replace("/rlp/", "", $tmp);
						echo $tmp2;
						?>
						
						"+"-"+nachname+vorname.charAt(0);

					}
					</script>


	      </div>

				<div class="sidecontentnewuser">

					<button type="submit" id="createuserbutton" name="create"><i class="fas fa-user-plus"></i>  Neuen Nutzer erstellen</button>

				</div>

			</form>

		</div>

		<?php include('supportbtn.php'); ?>

	</body>
</html>