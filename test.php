<?php

use Random\Engine\Mt19937;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Engine\Secure;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

require __DIR__ . '/vendor/autoload.php';

// generate 10000 getInt and 10000 nextInt

$engines = [
    'mt' => new Mt19937(),
    'mt_legacy' => new Mt19937(null, MT_RAND_PHP),
    'secure' => new Secure(),
    'pcg' => new PcgOneseq128XslRr64(),
    'xoshiro' => new Xoshiro256StarStar(),
];

foreach ($engines as $name => $engine) {
    $r = new Randomizer($engine);

    $time = microtime(true);

    for ($i = 0; $i < 10000; $i++) {
        $r->getInt(0, 65535);
    }

    echo $name, ': getInt(65535): ', sprintf("%.4f", (microtime(true) - $time) * 1000), PHP_EOL;

    $time = microtime(true);

    for ($i = 0; $i < 10000; $i++) {
        $r->getInt(0, 31337);
    }

    echo $name, ': getInt(31337): ', sprintf("%.4f", (microtime(true) - $time) * 1000), PHP_EOL;

    $time = microtime(true);

    for ($i = 0; $i < 10000; $i++) {
        $r->nextInt();
    }

    echo $name, ': nextInt(): ', sprintf("%.4f", (microtime(true) - $time) * 1000), PHP_EOL;

    echo PHP_EOL;
}
