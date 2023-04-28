<?php
session_start();

function separator($separator, $form)
{
    //$formKey = array_keys($form);
    if (count(explode($separator, $form[0])) > 1) {
        $separator = $separator === ' ' ? 'espace' : $separator;
        die("L\'username ne doit pas comporter de champ $separator");
    }

    if (count(explode($separator, $form[1])) > 1) {
        $separator = $separator === ' ' ? 'espace' : $separator;
        die("Le password ne doit pas comporter de champ $separator");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    include('db_engine.php');
    $usernameForm = $_POST['username'];
    $passwordForm = $_POST['password'];
    $domaine = $_POST['domaine'];


    separator('-', [$username, $passwordForm]);
    separator(' ', [$username, $passwordForm]);

    $error = null;
    //echo "sed -e 's/MYUSERNAME/$usernameForm/' -e 's/MYDOMAIN/$domaine/' /etc/nginx/templateSite > /etc/nginx/sites-enabled/$domaine";die; 
    try {
        $existUser = shell_exec("id -u $usernameForm");
        $pdo = new PDO("$engine:host=$host:$port;dbname=$dbname", $username, $password);
        $stm = $pdo->prepare("SELECT EXISTS(SELECT * FROM `users` WHERE `username` = :username) as `exists`");
        $stm->execute([
            'username' => $usernameForm,
        ]);

        if ($stm->fetch()['exists'] || $existUser !== null) {
            throw new Exception("L'username est existant, veuillez un choisir un nouveau.");
        }

        shell_exec("sudo useradd -m -s /bin/bash $usernameForm && echo $usernameForm:$passwordForm | sudo chpasswd");
        //shell_exec("sudo chmod 700 /home/$usernameForm/files");
        shell_exec("sudo sed -e 's/MYUSERNAME/$usernameForm/' -e 's/MYDOMAIN/$domaine/' /etc/nginx/templateSite > /etc/nginx/sites-enabled/$domaine");
        echo "$domaine";

        $passwordHash = password_hash($passwordForm, PASSWORD_DEFAULT);
        shell_exec("sudo su; sudo /var/www/html/db_script.sh $usernameForm $usernameForm $passwordForm");
        $stm = $pdo->prepare('Insert Into users (username, password) VALUES (:username,:password)');
        $stm->execute([
            'username' => $usernameForm,
            'password' => $passwordHash
        ]);

        $_SESSION['username'] = $_POST['username'];
    } catch (PDOException | Exception $e) {
        $error = 'Message d\'erreur : ' . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html>

<head>
    <title>Inscription</title>
</head>

<body>
    <h1>Inscription</h1>
    <form method="POST">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" name="username" id="username" />
        <br />
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" id="password" />
        <br />
        <input type="submit" value="Register" />
        <label for="domaine">Domaine</label>
        <input type="domaine" name="domaine" id="domaine" />
        <br />

    </form>
    <p><?= $error ?></p>
</body>

</html>