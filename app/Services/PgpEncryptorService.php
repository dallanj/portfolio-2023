<?php

namespace App\Services;

use OpenPGP;
use OpenPGP_Crypt_RSA;
use OpenPGP_Message;
use OpenPGP_LiteralDataPacket;
use OpenPGP_CompressedDataPacket;
use OpenPGP_Crypt_Symmetric;
use OpenPGP_PublicKeyPacket;
use Illuminate\Support\Facades\Storage;

class PgpEncryptorService
{
    protected $publicKeyPath;

    public function __construct(?string $publicKeyPath)
    {
        $this->publicKeyPath = $publicKeyPath ?? Storage::path('/keys/pubkey.asc');
    }

    public function encrypt(string $plainText): ?string
    {
        $pubKeyArmored = file_get_contents($this->publicKeyPath);
        $publicKey = OpenPGP_Message::parse(OpenPGP::unarmor($pubKeyArmored, 'PGP PUBLIC KEY BLOCK'));
        
        $data = new OpenPGP_LiteralDataPacket($plainText, ['format' => 'u']);
        $compressed = new OpenPGP_CompressedDataPacket($data);
        $encrypted = OpenPGP_Crypt_Symmetric::encrypt($publicKey, new OpenPGP_Message([$compressed]));

        
        return OpenPGP::enarmor($encrypted->to_bytes(), 'PGP MESSAGE');
    }
}
// dd(OpenPGP::enarmor($encrypted->to_bytes(), 'PGP MESSAGE'));

// $arr = [];
        // foreach ($publicKey as $packet) {
        //     if ($packet instanceof OpenPGP_PublicKeyPacket) {
        //         array_push($arr, 'Key ID: ' . strtoupper(bin2hex($packet->key_id)) . PHP_EOL);
        //         // dd('Key ID: ' . strtoupper(bin2hex($packet->key_id)) . PHP_EOL);
        //     }
        // }
        // dd($arr);