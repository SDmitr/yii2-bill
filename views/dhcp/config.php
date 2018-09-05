# Created at <?= date("Y-m-d H:i:s") ?> 
# user <?= Yii::$app->user->identity->username ?> 
<?= str_repeat('#', 32) ?>


option domain-name "<?= Yii::$app->params['DHCP']['domainName'] ?>";
option domain-name-servers <?= Yii::$app->params['DHCP']['dnsPrimary'] ?>, <?= Yii::$app->params['DHCP']['dnsSecondary'] ?>;
option netbios-name-servers <?= Yii::$app->params['DHCP']['dnsSecondary'] ?>;
option netbios-node-type 8;

default-lease-time <?= Yii::$app->params['DHCP']['defaultLeaseTime'] ?>;
max-lease-time <?= Yii::$app->params['DHCP']['maxLeaseTime'] ?>;

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

shared-network <?= Yii::$app->params['DHCP']['sharedNetwork']['name'] ?> {
    subnet <?= Yii::$app->params['DHCP']['sharedNetwork']['subnet'] ?> netmask <?= Yii::$app->params['DHCP']['sharedNetwork']['netmask'] ?> {
        range <?= Yii::$app->params['DHCP']['sharedNetwork']['firstIp'] ?> <?= Yii::$app->params['DHCP']['sharedNetwork']['lastIp'] ?>;
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
