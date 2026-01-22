<?php
include 'database.php';

session_start();

// CSRF Token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}
$csrf_token = generate_csrf_token();


// Insert
if (isset($_POST['submit_insert'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Ungültiges CSRF-Token.');
    }
    
    $conn = database::dbConnection();
    $name = $_POST['Name'] ?? '';
    $passwordHash = password_hash($_POST['Passwort'] ?? '', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO user (name, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $passwordHash);

    $stmt->execute();
    echo "Daten wurden gespeichert!";
}


// Login
if (isset($_POST['submit_login'])) {

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Ungültiges CSRF-Token.');
    }

    $conn = database::dbConnection();

    $name = $_POST['Name'] ?? '';
    $pass = $_POST['Passwort'] ?? '';

    // Nur das Hash aus DB holen, dann mit password_verify prüfen
    $stmt = $conn->prepare("SELECT password FROM user WHERE name = ? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($hash);
    if ($stmt->fetch() && password_verify($pass, $hash)) {
        session_regenerate_id(true);    
        $_SESSION['user'] = $name;
        header("Location: backend.php");
        exit;
    } else {
        echo "Passwort oder User-Name falsch";
    }
    $stmt->close();
}


// Output
$sql = "SELECT * FROM `user`";
$result = database::dbConnection()->query($sql);
$out = "";
// Ausgabe
while($row = $result->fetch_assoc()) {
    $id = htmlspecialchars($row["id"]);
    $name = htmlspecialchars($row["name"]);
    $password = htmlspecialchars($row["password"]);
    $out .= "ID: $id || Name: $name || Passwort: $password<br>";
}

database::dbConnection()->close();
?>

<html lang="de">
<head>
    <title>SQL-Injections</title>
</head>
<body>

<h1>Test-Seite SQL Injections</h1>
<h2>Insert</h2>
<!-- get zu post -->
<form action="frontend.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

    <input type="text" name="Name" placeholder="Name" pattern="[A-Za-zÄÖÜäöüß\s]+" required><br>
    <input type="password" name="Passwort" placeholder="Passwort" required><br>
    <input type="submit" name="submit_insert" value="Insert">
</form>
<hr>
<h2>Login</h2>
<form action="frontend.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    
    <input type="text" name="Name" placeholder="Name" pattern="[A-Za-zÄÖÜäöüß\s]+" required><br>
    <input type="password" name="Passwort" placeholder="Passwort" required><br>
    <input type="submit" name="submit_login" value="Login">
</form>
<hr>
<h2>Output</h2>
<?=$out?>
</body>
</html>
