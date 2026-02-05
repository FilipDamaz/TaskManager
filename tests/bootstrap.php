<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'test';
if (!isset($_SERVER['KERNEL_CACHE_DIR'])) {
    $cacheDir = sys_get_temp_dir().'/taskmanager_cache_'.$env;
    $_SERVER['KERNEL_CACHE_DIR'] = $cacheDir;
    $_ENV['KERNEL_CACHE_DIR'] = $cacheDir;
}

if (in_array($env, ['test', 'integration'], true)) {
    $privateKey = $_SERVER['JWT_SECRET_KEY'] ?? null;
    $publicKey = $_SERVER['JWT_PUBLIC_KEY'] ?? null;

    if (!$privateKey || !$publicKey) {
        $jwtDir = dirname(__DIR__).'/var/jwt';
        $privateKey = $jwtDir.'/private.pem';
        $publicKey = $jwtDir.'/public.pem';
        $_SERVER['JWT_SECRET_KEY'] = $privateKey;
        $_SERVER['JWT_PUBLIC_KEY'] = $publicKey;
        $_ENV['JWT_SECRET_KEY'] = $privateKey;
        $_ENV['JWT_PUBLIC_KEY'] = $publicKey;
    }

    $jwtDir = dirname($privateKey);
    $_SERVER['JWT_PASSPHRASE'] = $_SERVER['JWT_PASSPHRASE'] ?? '';
    $_ENV['JWT_PASSPHRASE'] = $_SERVER['JWT_PASSPHRASE'];

    if (!is_dir($jwtDir)) {
        mkdir($jwtDir, 0777, true);
    }

    if (!file_exists($privateKey) || !file_exists($publicKey)) {
        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        $res = openssl_pkey_new($config);
        if ($res === false) {
            throw new \RuntimeException('Failed to generate JWT keys for tests.');
        }

        openssl_pkey_export($res, $privKey);
        $details = openssl_pkey_get_details($res);
        $pubKey = $details['key'] ?? null;

        if ($pubKey === null) {
            throw new \RuntimeException('Failed to extract public key for tests.');
        }

        file_put_contents($privateKey, $privKey);
        file_put_contents($publicKey, $pubKey);
    }
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}
