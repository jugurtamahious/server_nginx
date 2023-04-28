#!/usr/bin/expect -f

set user [lindex $argv 0]
set form_password [lindex $argv 1]

echo "$user:$form_password" | sudo chpasswd
echo "mot de passe modifié"
