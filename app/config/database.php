<?php

$config['database'] = array(
    'default'       => 'mysql',

    'connections'   => array(
        'mysql'     => array(
            'driver'    => 'mysql',
            'host'      => isset($_SERVER['DB1_HOST']) ? $_SERVER['DB1_HOST'] : 'localhost',
            'database'  => isset($_SERVER['DB1_NAME']) ? $_SERVER['DB1_NAME'] : 'invoice',
            'username'  => isset($_SERVER['DB1_USER']) ? $_SERVER['DB1_USER'] : 'invoice',
            'password'  => isset($_SERVER['DB1_PASS']) ? $_SERVER['DB1_PASS'] : 'JwBuQ3TPMuC5jmbH',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        )
    )
);