<?php
require ('feistel-logic.php');

$K = "01101001";
$keys = generate_keys($K);

$N = "01100010";
$C = encrypt($N, $keys);
echo "Message chiffré: $C\n";

$decrypted = decrypt($C, $keys);
echo "Message déchiffré: $decrypted\n";

?>
