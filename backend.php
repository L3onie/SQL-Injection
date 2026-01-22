<?php

session_start();

// Session Timeout hinzugefügt 
define('SESSION_TIMEOUT', 15 * 60);
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    echo "<script>
            alert('Session abgelaufen. Bitte erneut einloggen.');
            window.location.href='frontend.php';
          </script>";
    exit;
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['user'])) {
    echo "<script>
            alert('Zugriff verweigert! Bitte zuerst einloggen.');
            window.location.href='frontend.php';
          </script>";
    exit;
}


include "database.php";

$sql = "SELECT * FROM `user`";
$result = database::dbConnection()->query($sql);
$out = "";

while($row = $result->fetch_assoc()) {
    $out .= "ID: " . htmlspecialchars($row["id"]) . 
            " || Name: " . htmlspecialchars($row["name"]) . 
            " || Passwort: " . htmlspecialchars($row["password"]) . "<br>";
}

database::dbConnection()->close();
?>

<html lang="de">
<head>
    <title>SQL-Injections BACKEND</title>
</head>
<body>
<h1>Test-Seite BACKEND</h1>
<?=$out?>
</body>
</html>
