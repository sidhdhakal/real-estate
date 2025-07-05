<?php

/**
 * Verifies a digital signature against the provided data and public key.
 */
function verifyDigitalSignature($data, $base64Signature, $publicKeyString) {
    // This function is correct.
    $signature = base64_decode($base64Signature);
    $publicKey = openssl_pkey_get_public($publicKeyString);

    if (!$publicKey) {
        return ['success' => false, 'message' => "❌ Invalid public key"];
    }..

    {

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

/**
 * Generates a new RSA public/private key pair.
 */
function generatePublicPrivateKey() {
    // Define the path to the config file.
    $opensslConfPath = 'C:\\xampp\\apache\\conf\\openssl.cnf';

    // Create the configuration array to pass to the OpenSSL functions.
    $configArgs = [
        "config" => $opensslConfPath,
        "digest_alg" => "sha256",
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ];

    // Clear any old OpenSSL errors.
    while(openssl_error_string() !== false) {}

    // Call the function with the direct configuration arguments.
    $res = openssl_pkey_new($configArgs);

    if (!$res) {
        $error = openssl_error_string();
        return ['error' => true, 'message' => '❌ Failed to generate key pair: ' . $error];
    }

  

    $exportResult = openssl_pkey_export($res, $private_key, null, $configArgs);

    if (!$exportResult) {
        $error = openssl_error_string();
        return ['error' => true, 'message' => '❌ Failed to export private key: ' . $error];
    }

    $public_key_details = openssl_pkey_get_details($res);
    if (!$public_key_details || !isset($public_key_details["key"])) {
        $error = openssl_error_string();
        return ['error' => true, 'message' => '❌ Failed to extract public key: ' . $error];
    }

    return [
        'error'       => false,
        'private_key' => $private_key,
        'public_key'  => $public_key_details["key"]
    ];
}

/**
 * Creates a digital signature for the given data using a private key.
 */
function createDigitalSignature($privateKey, $data) {
    // This function is correct.
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