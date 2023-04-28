#!/bin/bash

for userfile in /home/*; do tar -cvzf /home/$userfile/backup_$userfile.tar.gz /home/$userfile/files; done
