<?php
session_start();
var_dump($_SESSION);
if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)){
    include('db_engine.php');
    $form_password = $_POST['password'];
    $user = $_SESSION['username'];
    try{
        $passwordHash = password_hash($passwordForm, PASSWORD_DEFAULT);

        $pdo = new PDO("$engine:host=$host:$port;dbname=$dbname", $username ,$password);
        $stm = $pdo->prepare("UPDATE users SET password = :passwordHash WHERE username = :user");

        $stm->bindValue(':passwordHash', $passwordHash, \PDO::PARAM_STR);
        $stm->bindValue(':user', $user, \PDO::PARAM_STR);

        $stm->execute();

        $output = shell_exec("sudo su; sudo echo \"$user:$form_password\" | sudo chpasswd");
        var_dump($output);
    }
    catch(PDOException $e){
        echo "erreur lors de la modification de mot de passe" . $e->getMessage();

        exit();
    }
} 

?> 

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST">
        <label for="password">nouveau mot de passe</label>
        <input type="password" id="password" name="password"/>
        <input type="submit"/>
    </form>
    <?php
        echo "<pre>$output</pre>";
    ?>
</body>
</html>
