<?php
session_start();

include('db_engine.php');

$dbname = $_SESSION['username'];
$username = $_SESSION['username'];
$password = $_SESSION['password'];


$file = $_GET['file'] ?? $_GET['dump'];
$type = $_GET['file'] ? 'files' : 'dump';

$filepath = "/home/$username/$type/$file";

if($type === 'dump'){
    if(file_exists($filepath)){
        unlink($filepath);die;
    }
    shell_exec("sudo mysqldump -u $username -p$username $username | sudo tee > $filepath");
}

try {
    $pdo = new PDO("$engine:host=$host:$port;dbname=$dbname", $username ,$password);

    $stmt = $pdo->prepare("SELECT EXISTS(SELECT * FROM files where name = :file) as `exists`");

    $stmt->execute([
        'file' => $file
    ]);
    $bool = $stmt->fetch()['exists'];

    if ((! $bool || ! file_exists($filepath)) && $type !== 'dump') {
        throw new Exception("Ce fichier n'existe pas.");
    }

    header('Content-Description: Download File');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    flush();
    readfile($filepath);

} catch (\PDOException|\Exception|\Throwable $th) {
    echo 'Message d\'erreur : ' .$th->getMessage();
}
