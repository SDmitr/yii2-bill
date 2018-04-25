# Created at <?= date("Y-m-d H:i:s") ?> 
# user <?= Yii::$app->user->identity->username ?> 
<?= str_repeat('#', 32) ?>


option domain-name "Eliton";
option domain-name-servers 8.8.8.8, 91.220.204.253;
default-lease-time 21600;
max-lease-time 43200;

ddns-update-style none;
deny unknown-clients;

authoritative;
log-facility local7;

# Local Subnet

<?php foreach ($subnets as $subnet): ?>
# <?= $subnet->name?> #
shared-network SUBNET_<?= $subnet->id ?> {
    subnet <?= $subnet->subnet ?> netmask <?= $subnet->mask ?> {
        range <?= $subnet->first_ip ?> <?= $subnet->last_ip ?>;
        option subnet-mask <?= $subnet->mask ?>;
        option routers <?= $subnet->gateway ?>;
        option domain-name "Eliton";
        option domain-name-servers <?= $subnet->dns1 ?>, <?= $subnet->dns2 ?>;
    }
}
<?php endforeach; ?>

shared-network VLAN2 {
    subnet 172.16.255.248 netmask 255.255.255.248 {
        range 172.16.255.249 172.16.255.254;
    }
}

<?php $notebooks = Yii::$app->params['Notebook'];?>
<?php foreach ($notebooks as $notebook): ?>
# NOTEBOOK <?= $notebook['name'] ?> #
host notebook_<?= $notebook['name'] ?> {
    hardware ethernet <?= $notebook['mac'] ?>;
    fixed-address <?= $notebook['ip'] ?>;
}
<?php endforeach;?>

# USERS
<?php foreach ($users as $user): ?>
# <?= $user->client->num ?> <?= $user->client->name?> #
host user_<?= $user->id ?> {
    hardware ethernet <?= $user->mac ?>;
    fixed-address <?= $user->ip ?>;
}
<?php endforeach; ?>