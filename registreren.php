<?php
include("registreren.html");
include("bibliotheek/mailen.php");
include("DBconfig.php");
if(isset($_POST["submit"])) {
  $melding = "";
	$voornaam = htmlspecialchars($_POST['voornaam']);
	$achternaam = htmlspecialchars($_POST['achternaam']);
	$klant = $voornaam . " " .$achternaam;
	$adres = htmlspecialchars($_POST['adres']);
	$postcode = htmlspecialchars($_POST['postcode']);
	$woonplaats = htmlspecialchars($_POST['woonplaats']);
	$email = htmlspecialchars($_POST['email']);
	$wachtwoord = htmlspecialchars($_POST['wachtwoord']);
	$wachtwoordHash = password_hash($wachtwoord, PASSWORD_DEFAULT);

	// controleer of email al bestaat
	$sql = "SELECT * FROM klant WHERE email = ?";
	$stmt = $verbinding->prepare($sql);
	$stmt->execute(array($email));
	$resultaat = $stmt->fetch(PDO::FETCH_ASSOC);
	if($resultaat) { 
    $melding = "Dit email is al geregistreerd";
	}else{
		$sql = "INSERT INTO klant (ID, voornaam,
		achternaam, adres, postcode, woonplaats, email,
		wachtwoord, rol) values (null,?,?,?,?,?,?,?,?)";
		$stmt = $verbinding->prepare($sql);
		try{
			$stmt->execute(array(
        $voornaam, 
        $achternaam, 
        $adres, 
        $postcode, 
        $woonplaats, 
        $email,
        $wachtwoordHash,
        0)
      );
      $melding = "Nieuw account aangemaakt."; 
    }catch(PDOException $e) {
      $melding = "Kon geen account maken." . 
      $e->getMessage();
		}
    echo "<div id='melding'>".$melding."</div>";
    // bevestiging per email
    $onderwerp = "Nieuwe account";
    $bericht = "Gechte  $klant, bij deze bevestigen we je nieuwe account.";
    mailen($email, $klant, $onderwerp, $bericht);
  }
}
?>
