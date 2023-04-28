
<?php
session_start();
    $username = $_SESSION['username'];
    $domaine = $_POST['domaine'];
if(isset($username) && isset($domaine)){

secondHost();
}
var_dump($username,$domaine);
function cpu_load() {
    $cpu_info = file_get_contents('/proc/stat');
    $cpu_info = explode("\n", $cpu_info);
    $cpu_times = explode(" ", $cpu_info[0]);
    $total_time = 0;
    $idle_time = $cpu_times[4]; // le temps passé en mode idle
    foreach($cpu_times as $time) {
        $total_time += $time;
    }
    $load = (1 - $idle_time / $total_time) * 100;
    return $load;
}
function secondHost(){
	echo "##############################################################################";
    $command = "sudo sed -e 's/MYUSERNAME/$username/' -e 's/MYDOMAIN/$domaine/' /etc/nginx/templateSite";
 //   $output = shell_exec($command);
echo "output _>>>>>>>>>>>>>>>>>>>>>>>>>><";
//echo $output;
//   return $output;
}
function ram_load() {
    $mem_info = file_get_contents('/proc/meminfo');
    $mem_info = preg_replace("/[^0-9]/", " ", $mem_info); // retirer les lettres et les remplacer par des espaces
    $mem_info = explode(" ", $mem_info);
    $mem_total = $mem_info[18] * 1024; // le total de la RAM physique
    $mem_free = $mem_info[42] * 1024; // la quantité de RAM libre
    $mem_used = $mem_total - $mem_free; // la quantité de RAM utilisée
    $load = $mem_used / $mem_total * 100;
    return $load;
}

function hardware_load(){
	$hw_info = shell_exec("df -h");
	//var_dump($hw_info);
	$hw_info = explode(" ", $hw_info);
	//var_dump($hw_info);
	$new = explode("%", $hw_info[28]);
	
	$hw_final = 100 - $new[0];
	return $hw_final;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cc</title>
</head>
<body>
        
	<form method="POST">
	<label for="domaine">Second Domaine</label>
        <input type="text" name="domaine" id="domaine" />

    </form>

    <h1>Reporting</h1>
	<h2>Mémoire CPU restant :</h1>
	<span><?php echo "CPU : " . cpu_load() . "%";?></span>

	<h2>Mémoire RAM restant: </h2>
	<span><?php echo "Charge RAM : " . ram_load() . "%";?></span>

	<h2>Éspace disque dur restant :</h2>
        <span><?php echo "Disque Dur restant : " . hardware_load() . "%";?></span>

</body>
</html>
