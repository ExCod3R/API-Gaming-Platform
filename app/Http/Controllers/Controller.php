<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-25
     *
     * Handle image url file upload to storage
     */
    function handleImageUrlFileUpload($imageUrl, $folder)
    {
        $filename = uniqid() . time() . '.png';

        $image = file_get_contents($imageUrl);
        $path = $folder . '/' . $filename;
        Storage::put('public/' . $path, $image);

        return $path;
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-07-21
     *
     * Handle encrypt AES 256
     */
    public static function encryptAES256($data)
    {
        $password = "22#bbtrm8814z5";

        // Set a random salt
        $salt = openssl_random_pseudo_bytes(8);

        $salted = '';
        $dx = '';

        // Salt the key(32) and iv(16) = 48
        while (strlen($salted) < 48) {
            $dx = md5($dx . $password . $salt, true);
            $salted .= $dx;
        }

        $key = substr($salted, 0, 32);
        $iv  = substr($salted, 32, 16);

        $encrypted_data = openssl_encrypt($data, 'aes-256-cbc', $key, true, $iv);

        return base64_encode('Salted__' . $salt . $encrypted_data);
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-07-21
     *
     * Handle decrypt AES 256
     */
    public static function decryptAES256($edata)
    {
        $password = "22#bbtrm8814z5";

        $data = base64_decode($edata);
        $salt = substr($data, 8, 8);
        $ct = substr($data, 16);

        $rounds = 3;
        $data00 = $password . $salt;
        $md5_hash = array();
        $md5_hash[0] = md5($data00, true);
        $result = $md5_hash[0];

        for ($i = 1; $i < $rounds; $i++) {
            $md5_hash[$i] = md5($md5_hash[$i - 1] . $data00, true);
            $result .= $md5_hash[$i];
        }

        $key = substr($result, 0, 32);
        $iv  = substr($result, 32, 16);

        return openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    }
}
