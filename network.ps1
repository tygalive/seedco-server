#Since main sedeco server can be accessed from this pc, foward requests to specified port

#TODO: Set address manually
$remoteport = $($(wsl hostname -I).Trim());

#[Ports]

#All the ports you want to forward separated by coma
$ports=@(1430);


#[Static ip]
$addr = "0.0.0.0";
$ports_a = $ports -join ",";

$firewall_rule_name = "SEEDCO SERVER"

#Remove Firewall Exception Rules
iex "Remove-NetFireWallRule -DisplayName '$firewall_rule_name'";

#adding Exception Rules for inbound and outbound Rules
iex "New-NetFireWallRule -DisplayName '$firewall_rule_name' -Direction Outbound -LocalPort $ports_a -Action Allow -Protocol TCP";
iex "New-NetFireWallRule -DisplayName '$firewall_rule_name' -Direction Inbound -LocalPort $ports_a -Action Allow -Protocol TCP";

for( $i = 0; $i -lt $ports.length; $i++ ){
  $port = $ports[$i];
  iex "netsh interface portproxy delete v4tov4 listenport=$port listenaddress=$addr";
  iex "netsh interface portproxy add v4tov4 listenport=$port listenaddress=$addr connectport=80 connectaddress=$remoteport";
}

echo "remote : ${remoteport}";

#TODO: Set manually
$iface = "Wifi";
$addr = $(Get-NetIPAddress -AddressFamily IPv4 -InterfaceAlias $iface).IPv4Address;

ping -n 1 "$addr" > $null

if( !$?  ) {
    echo "error: cannot determine the external IP address";
    exit 1;
}
echo "connect : ${addr}:${port}";