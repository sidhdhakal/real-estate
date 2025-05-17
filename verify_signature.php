<?php

function verifyDigitalSignature($data, $base64Signature, $publicKeyString) {
    $signature = base64_decode($base64Signature);
    $publicKey = openssl_pkey_get_public($publicKeyString);

    if (!$publicKey) {
        return ['success' => false, 'message' => "❌ Invalid public key"];
    }

    $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);

    if ($verified == 1) {
        return ['success' => true, 'message' => "✅ Signature is valid"];
    } elseif ($verified === 0) {
        return ['success' => false, 'message' => "❌ Signature is invalid"];
    } else {
        return ['success' => false, 'message' => "⚠️ Verification error: " . openssl_error_string()];
    }
}

function generatePublicPrivateKey(){
    $config = [
        "digest_alg" => "sha256",
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ];

    $res = openssl_pkey_new($config);
    if (!$res) {
        return ['error' => true, 'message' => '❌ Failed to generate key pair'];
    }

    openssl_pkey_export($res, $private_key);

    $public_key_details = openssl_pkey_get_details($res);
    if (!$public_key_details || !isset($public_key_details["key"])) {
        return ['error' => true, 'message' => '❌ Failed to extract public key'];
    }

    return ['private_key'=> $private_key, 'public_key'=>$public_key_details["key"]];
}

function createDigitalSignature($privateKey, $data) {
    $privateKeyResource = openssl_pkey_get_private($privateKey);

    if (!$privateKeyResource) {
        return ["error" => true, "message" => "❌ Invalid private key format"];
    }

    $signature = '';
    $success = openssl_sign($data, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

    if (!$success) {
        return ["error" => true, "message" => "❌ Error creating digital signature"];
    }

    $base64Signature = base64_encode($signature);
    return ['error' => false, "signature" => $base64Signature];
}

?>