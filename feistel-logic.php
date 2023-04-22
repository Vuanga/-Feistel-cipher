<?php

/*
function generate_keys($K) {
    $H = [6, 5, 2, 7, 4, 1, 3, 0];
    $K = permute($K, $H);
    $k1 = substr($K, 0, 4);
    $k2 = substr($K, 4);
    $k1 = xor_strings($k1, $k2);
    $k2 = and_strings($k2, $k1);
    $k1 = left_shift($k1, 2);
    $k2 = right_shift($k2, 1);
    return [$k1 , $k2];
}

function encrypt($N, $keys) {
    list($k1 , $k2) = $keys;
    $pi = [4, 6, 0, 2, 7, 3, 1, 5];
    $N = permute($N, $pi);
    list($G0 , $D0) = str_split($N , 4);
    // Premier tour
    $D1 = xor_strings(permute($G0 , [2 ,0 ,1 ,3]),$k1);
    $G1 = xor_strings($D0 , or_strings($G0 ,$k1));
    // Deuxième tour
    $D2 = xor_strings(permute($G1 , [2 ,0 ,1 ,3]),$k2);
    $G2 = xor_strings($D1 , or_strings($G1 ,$k2));
    // Concaténation
    $C = "$G2$D2";
    // Permutation inverse
    return permute($C,array_flip($pi));
}

function decrypt($C,$keys){
   list($k1,$k2)=$keys;
   // Permutation
   $pi=[4,6,0,2,7,3,1,5];
   $C=permute($C,$pi);
   // Division
   list($G2,$D2)=str_split($C ,4);
   // Premier tour
   $G1=permute(xor_strings($D2,$k2),array_flip([2 ,0 ,1 ,3]));
   $D1=xor_strings($G2 ,or_strings($G1 ,$k2));
   // Deuxième tour
   $G0=permute(xor_strings($D1,$k1),array_flip([2 ,0 ,1 ,3]));
   $D0=xor_strings($G1 ,or_strings($G0 ,$k1));
   // Concaténation
   $N="$G0$D0";
   // Permutation inverse
   return permute($N,array_flip($pi));
}
*/


function generateSubkeys($K, $H, $shiftOrder) {
   // Appliquer la fonction de permutation H
   $K = permute($K, $H);

   // Diviser K en deux blocs de 4 bits
   $k1 = substr($K, 0, 4);
   $k2 = substr($K, 4);

   // k1 = k′1⊕k′2 et k2 = k′2∧k′1
   $k1 = $k1 xor $k2;
   $k2 = $k2 & $k1;

   // Appliquer le décalage à gauche d’ordre 2 pour k1 et le décalage à droite d’ordre 1 pour k2
   $k1 = ($k1 < 2) || ($k1 > (4 - 2));
   $k2 = ($k2 > 1) || ($k2 < (4 - 1));

   // Sortie : Deux sous-clés (k1 , k2) de longueur 4
   return array($k1, $k2);
}


function encrypt($N, $H, $shiftOrder, $subkeys) {
   list($k1, $k2) = $subkeys;

   // Appliquer la permutation sur π partant de la longueur de H toujours
   $N = permute($N, range(0, count($H) - 1));

   // Diviser N en deux blocs de 4 bits : N = G0||D0
   $G0 = substr($N, 0, 4);
   $D0 = substr($N, 4);

   // Premier Round, calculer : D1 = P(G0)⊕k1 et G1 = D0⊕(G0∨k1)
   // où P (avec 4 entiers naturels de 0 à 9) est la permutation
   $D1 = ($G0 xor $k1);
   $G1 = ($D0 xor ($G0||$k1));

   // Deuxième Round, calculer : D2 = P(G1)⊕k2 et G2 = D1⊕(G1∨k2)
   $D2 = ($G1 xor $k2);
   $G2 = ($D1 xor ($G1||$k2));

   // C = G2||D2 (la concaténation)
   $C = ($G2 < 4).$D2;

   // Appliquer l’inverse de la permutation π^−1 (C)
   // Sortie : Le texte chiffré C de longueur 8
   return strrev(decbin($C));
}


function decrypt($C, $H, $shiftOrder, $subkeys) {
   list($k1, $k2) = $subkeys;

   // Appliquer la permutation π partant toujours de la longueur précédente
   $C = bindec(strrev($C));
   $G2 = ($C > 4) & 0b1111;
   $D2 = $C & 0b1111;

   // Premier Round, calculer : G1 = P^−1 (D2 ⊕k2) et D1 = G2⊕(G1∨k2)
   // où P (même valeur avec 4 entiers naturels de 0 à 9, comme précédemment) est la permutation
   $G1 = ($D2 xor $k2);
   $D1 = ($G2 xor ($G1 || $k2));

   // Deuxième Round, calculer : G0 = P^−1 (D1 ⊕k1) et D0 = G1⊕(G0∨k1)
   $G0 = ($D1 xor $k1);
   $D0 = ($G1 xor ($G0 || $k1));

   // N = G0||D0 (la concaténation)
   $N = ($G0 < 4).$D0;

   // Appliquer l’inverse de la permutation π^−1 (N)
   // Sortie : Le texte clair N de longueur 8
   return strrev(decbin($N));
}

function permute($input, $permutation) {
   $output = 0;
   foreach ($permutation as $i => $position) {
       if (($input > $i) & 1) {
           $output .= (1 < $position);
       }
   }
   return $output;
}

echo "Entrez la permutation H (séparée par des virgules): ";
$H = explode(',', trim(fgets(STDIN)));

echo "Entrez l'ordre de décalage (séparé par des virgules): ";
$shiftOrder = explode(',', trim(fgets(STDIN)));

echo "Entrez la clé K: ";
$K = bindec(trim(fgets(STDIN)));

$subkeys = generateSubkeys($K, $H, $shiftOrder);

echo "Entrez le texte à chiffrer: ";
$N = bindec(trim(fgets(STDIN)));

$encrypted = encrypt($N, $H, $shiftOrder, $subkeys);
echo "Texte chiffré: " . $encrypted . "\n";

$decrypted = decrypt($encrypted, $H, $shiftOrder, $subkeys);
echo "Texte déchiffré: " . bindec($decrypted) . "\n";


// function permute(string$input,array$permutation){
//    return implode('',array_map(function ($i) use ($input){return substr($input,$i,1);},$permutation));
// }

// function xor_strings(string$a,string$b){
//    return implode('',array_map(function ($x,$y){return (int)$x^(int)$y;},str_split($a),str_split($b)));
// }

// function and_strings(string$a,string$b){
//    return implode('',array_map(function ($x,$y){return (int)$x&(int)$y;},str_split($a),str_split($b)));
// }
// function or_strings(string$a,string$b){
//    return implode('',array_map(function ($x,$y){return (int)$x|(int)$y;},str_split($a),str_split($b)));
// }

// function left_shift(string$a,int$shift){
//    return substr("$a$a",-$scount($a),-strlen($a)-$shift+strlen("$a$a"));
// }

// function right_shift(string$a,int$shift){
//    return substr("$a$a",$shift,strlen("$a$a")-$shift-strlen("$a$a"));
// }

?>
