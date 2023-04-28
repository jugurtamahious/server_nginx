<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
include('db_engine.php');
$usernameForm = $_POST['username'];
$passwordForm = $_POST['password'];

try{
  $pdo = new PDO("$engine:host=$host:$port;dbname=$dbname", $username ,$password);

  $stm = $pdo->prepare('SELECT * FROM users where username = ?');

  $stm->execute([
    $usernameForm
  ]);
  $log = $stm->fetch(PDO::FETCH_ASSOC);
  var_dump($log);
  var_dump(password_verify($passwordForm, $log["password"]));
  if(!empty($log) && password_verify($passwordForm, $log["password"])){
  session_start();
        $_SESSION['id'] = $log['id'];
        $_SESSION['username'] = $log['username'];
        $_SESSION['password'] = $_POST['password'];
        $_SESSION['connecte'] = true;

        header("Location: upload.php");
        exit();
  }
}
catch(PDOException $e){
  echo 'Échec lors de la connexion : ' . $e->getMessage();
  exit();
}
}
?>
<!doctype html>
<html lang="fr">
<head>
    <title>Formulaire de vérification de nom</title>
</head>
<body>
    <h1>Vérification de nom</h1>
    <form method="POST" >
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" name="username" id="username" />
        <br />
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" id="password" />
        <br />
        <input type="submit" value="Login" />
    </form>
</body>
</html>
