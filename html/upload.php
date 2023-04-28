<?php
session_start();

$sardoche = $_SESSION['username'];
include('db_engine.php');
$dbSize = 0;

shell_exec("sudo chown -R www-data:www-data /home/".$sardoche."/files && sudo chmod -R 755 /home/".$sardoche."/files");


try {
    $dbname = $sardoche;
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];

    $pdo = new PDO("$engine:host=$host:$port;dbname=$dbname", $sardoche ,$password);

    $stmt = $pdo->query("
        SELECT table_schema \"Database Name\", SUM(data_length + index_length) / 1024 \"Database Size (KB)\"
        FROM information_schema.TABLES GROUP BY table_schema
    ");

    $req = $stmt->fetch();

    $dbSize = array_sum($req);

    $stmt = $pdo->query("SELECT * FROM files");
    $req = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $files = $req;

    $status = null;
    if (!empty($_FILES) && $_FILES['monfichier']['error'] === 0) {
        $tmp_name = $_FILES['monfichier']['tmp_name'];
        $name = basename($_FILES['monfichier']['name']);
        // $ext = end((explode(".", $name)));
        $destination = '/home/'.$sardoche.'/files/' . $name;

        if (move_uploaded_file($tmp_name, $destination)) {
            $stmt = $pdo->prepare("Insert into files (name) VALUES (:name)");
            $stmt->execute([
                'name' => $name . $ext
            ]);

            $status = "Le fichier a été téléchargé avec succès.";
        } else {
            $status = "Une erreur est survenue lors du téléchargement du fichier.";
        }
    }
    
} catch (\PDOException|\Throwable $th) {
    $error = 'Message d\'erreur : ' .$th->getMessage();
    echo $error;
}

$usage = shell_exec("sudo du -sh -s /home/".$sardoche."/files");

$usage = trim($usage); // supprime les espaces en début et fin de chaîne
$usage = preg_replace('/\s+/', ' ', $usage); // remplace les espaces multiples par un seul espace
$usage = explode(' ', $usage)[0]; // récupère la première partie de la chaîne (la taille)
$usage = explode('K', $usage)[0];
$usage += $dbSize;
?>
<!DOCTYPE html>
<html>
<head>
	<title>Téléchargement de fichiers</title>
</head>
<body>
	<h1>Téléchargement de fichiers</h1>
	<h2>Vous utilisez <?php echo $usage.'K'?> de mémoire disque</h2>
	<form  method="post" enctype="multipart/form-data">
		<label for="file">Sélectionnez un fichier à télécharger :</label>
		<input type="file" name="monfichier" id="file"><br><br>
		<input type="submit" name="submit" value="Télécharger">
	</form>
    <?php if(isset($files)){
        foreach($files as $file){
            echo "Fichier : <a href=\"download.php?file={$file["name"]}\">{$file["name"]}</a></br>";
        }
    }
    echo $status;
    ?>
    <p>Voulez-vous avoir un backup de votre Database Principale ?</p>
    <form action="download.php?dump=data-dump.sql" method="post">
        <input type="submit" value="Backup">
    </form>
</body>
</html> 
<?php 



?>
