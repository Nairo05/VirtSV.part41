<!DOCTYPE html>
<?php
session_start();

//wenn nicht eingeloggt, zurück zur Startseite
if (!isset($_SESSION['loggedin'])) {
	header('Location:../index.php');
	exit;
}

//	[...]  
//
// ---------------------------------------------------------------
//
//	Hier werden n-Benutzer (Zeilen) n-Gruppen(Spalten) zugeordnet 
//
// ---------------------------------------------------------------

//DB-Zugangsdaten stehen in der Config Datei
$config = include('../../config.php');

//DB-Zugangsdaten aus Config
$db_host = $config['host'];
$db_port = $config['port'];
$db_username = $config['username'];
$db_password = $config['password'];
$db_database = $config['schul_index_database'];
$db_table =  $config['table']; . 'groups';

//Mit der Datenbank verbinden
$db = pg_connect("host=$db_host port=$db_port dbname=$db_database user=$db_username password=$db_password");

//Die Namen der Spalten holen
$query = "SELECT column_name FROM information_schema.columns WHERE table_name='$db_table'";
$result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

$tablecount = -2;
$values = "";

//Die Namen in einem String speichern, wird später benötigt
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    foreach ($line as $col_value) {
      $tablecount++;
      $values = $values."!;split;!".$col_value;
    }
}


?>

<html lang="de" >
	<head>
		<meta charset="utf-8">
		<title>Virtuelle Schulverwaltung</title>
		<link rel="stylesheet" href="../panelstyle.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<script src="https://kit.fontawesome.com/4ef9acb6f3.js" crossorigin="anonymous"></script>
		
		<!-- Für Popup-Confirm fenster -->
		<script src="scripts\jquerry.js"></script>
		
		<!-- Für Button-Animation -->
		<script src="scripts\verwaltung.js"></script>
		
		<!-- zum verstecken und anzeigen der weiteren "Infos" -->
		<script src="scripts\infoverstecken.js"></script>
		
		<!-- Popup des Support Fensters -->
		<script src="scripts\support.js"></script>

	</head>

	<!-- Navigationsleiste und Kopfzeile 
	
	[...]

	-->
	<?php include('../headinfo.php'); ?>
	<?php include('../sidebar.php'); ?>

	<body>

		<div id="main">

			<h3 class="headline">Benutzer zu Gruppe (<?php echo $_SESSION['schule']; ?>)</h3>

			<div class="main_content" id="main_usertogroup_div">

				<?php
				
					//Daten aus der Tabelle holen
					$query = "SELECT * FROM $db_table";
					$result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

					?>

      		<table>

				<tr>
				
					<!-- Spalte mit der ID der Benutzer -->
					<th><a style='color:black; padding-right:10px;' href='usertogroup.php?sort=nop'>
						<!-- aktuelle Sotierung anzeigen -->
						<?php if ($sort == "nop") {
							echo "<i class='fas fa-sort-numeric-down'></i>";
						} else if ($sort == "nopinvert") {
							echo "<i class='fas fa-sort-numeric-up'></i>";
						} else {
							echo "<i class='fas fa-sort'></i>";
						} 
						?> 
						ID </a></th>
									
					<!-- Spalte mit dem Anmeldename der Benutzer -->				
					<th><a style='color:black;' href='usertogroup.php?sort=anmeldename'>
						<!-- aktuelle Sotierung anzeigen -->
						<?php if ($sortname == "nop") {
							echo "<i class='fas fa-sort-numeric-down'></i>";
						} else if ($sortname == "nopinvert") {
							echo "<i class='fas fa-sort-numeric-up'></i>";
						} else {
							echo "<i class='fas fa-sort'></i>";
						} 
						?>  			
						Anmeldename </a></th>

					<?php

					$counter = 0;

					while ($counter < $tablecount) {
					  $counter++;
					  //
					  
					  //[...]
					  
					  // Überschriften der Spalten
					  
					  ?>
					  <th><a style='color:black;' href='usertogroup.php?sort=anmeldename'>
					  <?php 
						if ($sort == "name") {
							echo "<i class='fas fa-sort-alpha-down'></i>";
						} else {
							echo "<i class='fas fa-sort'></i>";
						}

						$split = explode("!;split;!", $values);
						echo " ".$split[$counter+2];

					// [...]
					
					?>
					
					</a></th>
					 
					<?php
					
					}

					//Checkboxen

					$selected_id = -1;
					$linecounter = 0;

					while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
						$linecounter++;

						  if (($linecounter % 2 )== 0) {
							echo "\t<tr>\n";
						  } else {
								echo "\t<tr style='background: #fafafa;'>\n";
						  }
					  ?>

						<form class="" action="usertogroupconfirm.php?confirm" method="post">

					  <?php

					  $counter = -1;
						foreach ($line as $col_value) {
						  $counter++;
						  if ($counter == 0) {
							$selected_id = $col_value;
						  }
						  if ($counter < 20 && $counter > 1) {

							?>

							<td style='padding: 8px;'>

							

							<input type="checkbox" name="col<?php echo $counter; ?>" value="soccer" value="" 
							
							<?php
							// aktueller Wert der Checkbox

							if ($col_value == "t") {
								echo "checked='true'";
							}
							?>>

							</td>

							<?php
							}

							if ($counter <= 1) {
								echo "\t\t<td style='padding: 8px;'>$col_value</td>\n";
							}
						}
						
						// [...]

					  ?>

					</form>

				</tr>
				
				}
      		
			</table>
			
			<?php

      		// Speicher freigeben
      		pg_free_result($result);

      		// Verbindung schließen
      		pg_close($dbconn);

			?>

			</div>

		</div>

		<?php include('supportbtn.php'); ?>

	</body>
</html>