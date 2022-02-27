#!/bin/bash

clients=3
startnum=110

cd /etc/openvpn/easy-rsa
mkdir -p bundles

for i in `seq 1 $clients`
do

cd /etc/openvpn/easy-rsa

num=$(($startnum + $i))
#echo test$num

source ./vars
#./build-key ktb$num
#echo -en "\n\n\n\n\n\n\n\n\n\ny\ny\n" | ./build-key ktb$num

(echo -en "\n\n\n\n\n\n\n\n"; sleep 1; echo -en "\n"; sleep 1; echo -en "\n"; sleep 3; echo -en "y"; echo -en "\n"; sleep 3; echo -en "y"; echo -en "\n") | ./build-key ktb$num

mkdir -p bundles/ktb$num

cp keys/ktb$num.crt bundles/ktb$num/ktb$num.crt
cp keys/ktb$num.key bundles/ktb$num/ktb$num.key

cp vpn.cnf bundles/ktb$num/vpn.cnf
sed -i "s/cert ktb.crt/cert ktb$num.crt/" bundles/ktb$num/vpn.cnf
sed -i "s/key ktb.key/key ktb$num.key/" bundles/ktb$num/vpn.cnf

cp keys/ca.crt bundles/ktb$num/ca.crt

echo "Creating tar..."
cd /etc/openvpn/easy-rsa/bundles/ktb$num
tar -cf ktb$num.tar ktb$num.crt ktb$num.key vpn.cnf ca.crt

echo ""
echo "#############################################################"
echo ""

done
