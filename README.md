﻿# server_nginx
PARTIE 1 :

disposer d'un utilisateur -> register.php
        commande : "shell_exec("sudo useradd -m -s /bin/bash
                $usernameForm && echo $usernameForm:$passwordForm
                | sudo chpasswd");
        verif : cd /home;

crééer un domaine pour l'utilisateur -> register.php
        commande : "shell_exec("sudo sed -e 's/MYUSERNAME/$usernameForm/'
                -e 's/MYDOMAIN/$domaine/' /etc/nginx/templateSite
                > /etc/nginx/sites-enabled/$domaine");"
        ceci crée le nom de domaine dans la conf personnalisée de l'user
        verif : cd /etc/nginx/sites-enabled

crééer une bdd perso -> register.php
        commande : "shell_exec("sudo su; sudo /var/www/html/db_script.sh
                $usernameForm $usernameForm $passwordForm");
        verif : cd /home;

consulter espace consommé par user et sa bdd -> upload.php
        requête : "SELECT table_schema \"Database Name\",
                SUM(data_length + index_length) / 1024 \"Database Size (KB)\"
                FROM information_schema.TABLES GROUP BY table_schema
                ");

PARTIE 2 :

utilisateur peut changer son mdp Linux -> password_reset.php
        commande : "sudo su; sudo echo \"$user:$form_password\" | sudo chpasswd"
        + requête SQL;

téléchargement des backup :
        bdd -> upload.php
        commande : "download.php?dump=data-dump.sql"

        archive des fichiers -> upload.php
        commande : echo "Fichier : <a href=\"download.php?file=
        {$file["name"]}\">{$file["name"]}</a></br>";

utilisateur peut ajouter second site : en cours

BONUS :

charge CPU -> infos.php
        get_content de /proc/stat
        on trim les valeurs puis on calcul

charge RAM -> infos.php
        get_content de /proc/meminfo;
        explode des results puis calcul de memoire utilisée / memoire totale *100;
occupation disque dur -> infos.php
        commande ; "df -h";

analyse connexions SSH -> logs.sh
        commande : on loop sur /var/log/auth.log
                on récupère les failed/success et on vient écrire dans le fichier
                /var/www/html/logs.txt;
