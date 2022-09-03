<?php

use Random\Engine\Mt19937;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Engine\Secure;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

require __DIR__ . '/vendor/autoload.php';

$engines = [
    'mt' => new Mt19937(),
    'mt_legacy' => new Mt19937(null, MT_RAND_PHP),
    'secure' => new Secure(),
    'pcg' => new PcgOneseq128XslRr64(),
    'xoshiro' => new Xoshiro256StarStar(),
];

$randomizers = array_map(function ($e) {
    return new Randomizer($e);
}, $engines);

$max = intval(getenv('MAX'));

if ($max === -1) {
    foreach ($randomizers as $name => $r) {
        $time = microtime(true);

        for ($i = 0; $i < 10000; $i++) {
            $r->nextInt();
        }

        echo '|', sprintf("%.4f", (microtime(true) - $time) * 1000), PHP_EOL;
    }
} else {
    foreach ($randomizers as $name => $r) {
        $time = microtime(true);

        for ($i = 0; $i < 10000; $i++) {
            $r->getInt(0, $max);
        }

        echo '|', sprintf("%.4f", (microtime(true) - $time) * 1000), PHP_EOL;
    }
}

