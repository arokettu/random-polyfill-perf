<?php

// Math should be patched with the following method:
$math = <<<'PHP'
    protected static function build(int $sizeof): self
    {
        static $engines = null;
        $engines = $engines ?? intval(getenv('MATH'));
        
        // only less because PHP int is always signed
        if (($engines & 0b001) && $sizeof < \PHP_INT_SIZE) {
            return new MathNative($sizeof);
        }

        if (($engines & 0b010) && \extension_loaded('gmp')) {
            return new MathGMP($sizeof);
        }

        if ($engines & 0b100) {
            return new MathUnsigned($sizeof);
        }
        
        throw new \LogicException('All engines are disabled');
    }
PHP;

const GMP_PURE = 0b010;
const GMP_WITH_NATIVE = 0b011;
const UNS_PURE = 0b100;
const UNS_WITH_NATIVE = 0b101;

$envs = [
    'Native (PHP 8.2)' => ['php82', 0],
    'GMP (pure, PHP 7.4)' => ['php74', GMP_PURE],
    'GMP (with native, PHP 7.4)' => ['php74', GMP_WITH_NATIVE],
    'unsigned (pure, PHP 7.4)' => ['php74', UNS_PURE],
    'unsigned (with native, PHP 7.4)' => ['php74', UNS_WITH_NATIVE],
    'GMP (pure, PHP 8.0)' => ['php80', GMP_PURE],
    'GMP (with native, PHP 8.0)' => ['php80', GMP_WITH_NATIVE],
    'unsigned (pure, PHP 8.0)' => ['php80', UNS_PURE],
    'unsigned (with native, PHP 8.0)' => ['php80', UNS_WITH_NATIVE],
    'GMP (pure, PHP 8.0 + jit)' => ['php80 -dopcache.enable_cli=1 -dopcache.jit_buffer_size=100M', GMP_PURE],
    'GMP (with native, PHP 8.0 + jit)' => ['php80 -dopcache.enable_cli=1 -dopcache.jit_buffer_size=100M', GMP_WITH_NATIVE],
    'unsigned (pure, PHP 8.0 + jit)' => ['php80 -dopcache.enable_cli=1 -dopcache.jit_buffer_size=100M', UNS_PURE],
    'unsigned (with native, PHP 8.0 + jit)' => ['php80 -dopcache.enable_cli=1 -dopcache.jit_buffer_size=100M', UNS_WITH_NATIVE],
];

$tests = [
    '31337',
    '65535',
    '4398046511103',
    '8027757784328',
    '-1',
];

foreach ($tests as $max) {
    echo 'MAX: ' . $max, PHP_EOL, PHP_EOL;

    foreach ($envs as $title => [$cmd, $env]) {
        echo '|', $title, PHP_EOL;
        echo `MATH=$env MAX=$max $cmd test_int.php`;
        echo PHP_EOL;
    }
}
