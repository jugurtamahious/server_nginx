#!/bin/bash

# Parcourir le fichier auth.log
grep "sshd" /var/log/auth.log | while read line
do
    # Extraire la date, l'utilisateur et l'adresse IP de la ligne
    date=$(echo "$line" | awk '{print $1, $2, $3}')
    user=$(echo "$line" | awk '{print $9}')
    ip=$(echo "$line" | awk '{print $11}')

    # Si la ligne contient "Failed password" ou "Accepted password"
    if [[ "$line" == *"Failed password"* || "$line" == *"Accepted password"* ]]; then
        # Afficher la date, l'utilisateur et l'adresse IP
        echo "$date $user $ip" >> /var/www/html/logs.txt
    fi
done
