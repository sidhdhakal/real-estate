<?php
// --- DIAGNOSTIC SCRIPT - V2 ---

echo "<h1>OpenSSL Path and Permissions Test (V2 - Direct Config)</h1>";

// Define the path we are testing
$path = 'C:\\xampp\\apache\\conf\\openssl.cnf';

echo "<p><b>Testing Path:</b> " . $path . "</p>";

// The previous tests for file_exists and is_readable passed, so we can be confident here.
echo "<h2>Test: Generating a Key using Direct Configuration</h2>";
echo "<p>This method bypasses putenv() and passes the config path directly to OpenSSL. This is the most reliable way.</p>";


// Create the configuration array for the OpenSSL function
$configArgs = [
    'config' => $path, // This is the key!
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
];

// Clear any old errors
while(openssl_error_string() !== false) {}

// Call the function with the direct configuration
$res = openssl_pkey_new($configArgs);

if ($res) {
    echo "<p style='color:green; font-weight:bold;'>SUCCESS: Key generated successfully using the direct method!</p>";
    echo "<p>You should now update your main application's `generatePublicPrivateKey` function with this technique.</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>FAILURE: Key generation failed even with the direct method.</p>";
    echo "<p><b>Error Message:</b> " . openssl_error_string() . "</p>";
    echo "<p>This strongly suggests an external program (like an Antivirus or Firewall) is blocking PHP's OpenSSL module. Please try temporarily disabling your antivirus and run this test again.</p>";
}

?>