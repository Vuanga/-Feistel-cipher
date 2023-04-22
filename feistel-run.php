<?php
// require ('feistel-logic.php');

// $K = "01101001";
// $keys = keyGeneration($K);

// $N = "01100010";
// $C = encrypt($N, $keys);
// echo "Message chiffré: $C\n";

// $decrypted = decrypt($C, $keys);
// echo "Message déchiffré: $decrypted\n";




/*

function H($K, $permutation) {
    // Appliquer la fonction de permutation H
}

function P($G, $permutation) {
    // Appliquer la fonction de permutation P
}

function leftShift($k, $n) {
    // Appliquer le décalage à gauche d’ordre n pour k
}

function rightShift($k, $n) {
    // Appliquer le décalage à droite d’ordre n pour k
}

function generateSubkeys($K, $permutation, $leftShiftOrder, $rightShiftOrder) {
    H($K, $permutation);
    $k1 = substr($K, 0, 4);
    $k2 = substr($K, 4);
    $k1 = $k1 ^$k2;
    $k2 = $k2 &$k1;
    leftShift($k1,$leftShiftOrder);
    rightShift($k2,$rightShiftOrder);
    return array($k1,$k2);
}

function encrypt($N,$subkeys,$permutation) {
    list($k1,$k2) =$subkeys;
    H($N,$permutation);
    $G0 = substr($N, 0, 4);
    $D0 = substr($N, 4);
    $D1 = P($G0,$permutation) ^$k1;
    $G1 = $D0 ^ ($G0 |$k1);
    $D2 = P($G1,$permutation) ^$k2;
    $G2 =$D1 ^ ($G1 |$k2);
    $C =$G2 .$D2;
    H(strrev($C), strrev($permutation));
    return$C;
}

function decrypt($C,$subkeys,$permutation) {
    list($k1,$k2) =$subkeys;
    H($C,$permutation);
    $G2 = substr($C, 0, 4);
    $D2 = substr($C, 4);
    $G1 = strrev(P($D2 ^$k2,$permutation));
    $D1 =$G2 ^ ($G1 |$k2);
    $G0 = strrev(P($D1 ^$k1,$permutation));
    $D0 =$G1 ^ ($G0 |$k1);
    $N =$G0 .$D0;
    H(strrev($N), strrev($permutation));
    return$N;
}

// Demander à l'utilisateur de définir la permutation et l'ordre de décalage
echo "Entrez la permutation (8 entiers naturels séparés par des espaces) : ";
$permutation = explode(" ", trim(fgets(STDIN)));
echo "Entrez l'ordre de décalage à gauche : ";
$leftShiftOrder = intval(trim(fgets(STDIN)));
echo "Entrez l'ordre de décalage à droite : ";
$rightShiftOrder = intval(trim(fgets(STDIN)));

// Générer les sous-clés
echo "Entrez la clé K (longueur 8) : ";
$K = trim(fgets(STDIN));
$subkeys = generateSubkeys($K,$permutation,$leftShiftOrder,$rightShiftOrder);

// Chiffrer un texte
echo "Entrez le texte à chiffrer (longueur 8) : ";
$N = trim(fgets(STDIN));
$C = encrypt($N,$subkeys,$permutation);

// Déchiffrer un texte
echo "Entrez le texte à déchiffrer (longueur 8) : ";
$C = trim(fgets(STDIN));
$N = decrypt($C,$subkeys,$permutation);
*/


class Cipher {
    private $H;
    private $shiftOrder;
    private $k1;
    private $k2;

    public function __construct($H, $shiftOrder) {
        $this->H = $H;
        $this->shiftOrder = $shiftOrder;
    }

    public function generateSubkeys($K) {
        // Appliquer la fonction de permutation H
        $K = $this->permute($K, $this->H);

        // Diviser K en deux blocs de 4 bits
        $k1 = substr($K, 0, 4);
        $k2 = substr($K, 4);

        // k1 = k′1⊕k′2 et k2 = k′2∧k′1
        $k1 = $k1 ^ $k2;
        $k2 = $k2 & $k1;

        // Appliquer le décalage à gauche d’ordre 2 pour k1 et le décalage à droite d’ordre 1 pour k2
        $k1 = ($k1 << 2) | ($k1 >> (4 - 2));
        $k2 = ($k2 >> 1) | ($k2 << (4 - 1));

        // Stocker les sous-clés pour une utilisation ultérieure
        $this->k1 = $k1;
        $this->k2 = $k2;

        // Sortie : Deux sous-clés (k1 , k2) de longueur 4
        return array($this->k1, $this->k2);
    }

    public function encrypt($N) {
        // Appliquer la permutation sur π partant de la longueur de H toujours
        $N = $this->permute($N, range(0, strlen($this->H) - 1));

        // Diviser N en deux blocs de 4 bits : N = G0||D0
        $G0 = substr($N, 0, 4);
        $D0 = substr($N, 4);

        // Premier Round, calculer : D1 = P(G0)⊕k1 et G1 = D0⊕(G0∨k1)
        // où P (avec 4 entiers naturels de 0 à 9) est la permutation
        $D1 = ($G0 ^ $this->k1);
        $G1 = ($D0 ^ ($G0 | $this->k1));

        // Deuxième Round, calculer : D2 = P(G1)⊕k2 et G2 = D1⊕(G1∨k2)
        $D2 = ($G1 ^$this->k2);
        $G2 = ($D1 ^ ($G1 |$this->k2));

        // C = G2||D2 (la concaténation)
        $C = ($G2 << 4) |$D2;

        // Appliquer l’inverse de la permutation π^−1 (C)
        // Sortie : Le texte chiffré C de longueur 8
        return strrev(decbin($C));
    }

    public function decrypt($C) {
        // Appliquer la permutation π partant toujours de la longueur précédente
        $C = bindec(strrev($C));
        $G2 = ($C >> 4) & 0b1111;
        $D2 = $C & 0b1111;

        // Premier Round, calculer : G1 = P^−1 (D2 ⊕k2) et D1 = G2⊕(G1∨k2)
        // où P (même valeur avec 4 entiers naturels de 0 à 9, comme précédemment) est la permutation
        $G1 = ($D2 ^$this->k2);
        $D1 = ($G2 ^ ($G1 |$this->k2));

        // Deuxième Round, calculer : G0 = P^−1 (D1 ⊕k1) et D0 = G1⊕(G0∨k1)
        $G0 = ($D1 ^$this->k1);
        $D0 = ($G1 ^ ($G0 |$this->k1));

        // N = G0||D0 (la concaténation)
        $N = ($G0 << 4) |$D0;

        // Appliquer l’inverse de la permutation π^−1 (N)
        // Sortie : Le texte clair N de longueur 8
        return strrev(decbin($N));
    }

    private function permute($input, $permutation) {
        $output = 0;
        foreach ($permutation as $i => $position) {
            if (($input >> $i) & 1) {
                $output |= (1 << $position);
            }
        }
        return $output;
    }
}



?>
