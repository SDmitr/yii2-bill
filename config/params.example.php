<?php

return [
    'name' => 'My Company',
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'managementNetwork' => [
        'subnet' => '192.168.0.0',
        'mask' => '255.255.255.0',
        'snmpCommunity' => 'public',
    ],
    'DHCP' => [
        'domainName' => 'Example',
        'dnsPrimary' => '8.8.8.8',
        'dnsSecondary' => '8.8.4.4',
        'defaultLeaseTime' => 21600,
        'maxLeaseTime' => 43200,
        'sharedNetwork' => [
            'name' => 'VLAN',
            'subnet' => '10.0.255.248',
            'netmask' => '255.255.255.248',
            'firstIp' => '10.0.255.249',
            'lastIp' => '10.0.255.254',
        ],
    ],
    'telnetSettings' => [
        'port' => 23,
        'length' => 1024,
    ],
    'Core' => [
        'address' => '192.168.0.1',
        'login' => 'admin',
        'password' => 'admin',
    ],
    'OLT' => [
        [
            'name' => 'OLT',
            'address' => '192.168.0.2',
            'login' => 'admin',
            'password' => 'admin',
        ],
    ],
    'onuService' => [
        'ff:ff:ff:aa:aa:aa' => 'Distribution',
    ],
    'Mikrotik' => [
        'address' => '192.168.0.254',
        'login' => 'admin',
        'password' => 'admin',
        'blacklist' => 'BLACKLIST-IP',
    ],
    'Notebook' => [
        [
            'name' => 'VLAN1',
            'mac' => 'aa:aa:aa:ff:ff:ff',
            'ip' => '10.0.0.1',
        ],
    ],   
    'mask' => [
        '255.255.0.0' => '/16 (255.255.0.0)', 
        '255.255.128.0' => '/17 (255.255.128.0)',
        '255.255.192.0' => '/18 (255.255.192.0)',
        '255.255.224.0' => '/19 (255.255.224.0)',
        '255.255.240.0' => '/20 (255.255.240.0)',
        '255.255.248.0' => '/21 (255.255.248.0)',
        '255.255.252.0' => '/22 (255.255.252.0)',
        '255.255.254.0' => '/23 (255.255.254.0)',
        '255.255.255.0' => '/24 (255.255.255.0)',
        '255.255.255.128' => '/25 (255.255.255.128)',
        '255.255.255.192' => '/26 (255.255.255.192)',
        '255.255.255.224' => '/27 (255.255.255.224)',
        '255.255.255.240' => '/28 (255.255.255.240)',
        '255.255.255.248' => '/29 (255.255.255.248)',
        '255.255.255.252' => '/30 (255.255.255.252)',
        '255.255.255.254' => '/31 (255.255.255.254)',
        '255.255.255.255' => '/32 (255.255.255.255)',
    ],
    'hideParams' => [
        'id', 'aton', 'date_on', 'date_off', 'deleted',
    ],
];
