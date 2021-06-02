#!/bin/bash
IP=pricing.vseinstrumenti.ru
DUSER=pricing
echo $IP &&
ssh ${DUSER}@${IP} "ls -l /home/pricing/app/web" &&
ssh ${DUSER}@${IP} "sudo rm -rf /home/pricing/app/web/masquerade" &&
rsync -av -e ssh --exclude='.git*' ~/src/pricing/masquerade/dist ${DUSER}@${IP}:/home/pricing/app/web &&
ssh ${DUSER}@${IP} "mv -f /home/pricing/app/web/dist /home/pricing/app/web/masquerade" &&
ssh ${DUSER}@${IP} "ls -l /home/pricing/app/web"