<?php
require ('feistel-fonction.php');

    
function main() {
    echo "********ALGORITHME DE FREISNEL CIPHER*********\n";
    echo "Donnez une clé K de longueur 8\n";
    $key = readline();
    while (strlen($key) < 8) {
        echo "La taille de la clé doit être de longueur 8\n";
        $key = readline();
    }
    echo "Donnez la fonction H de permutation\n";
    $h = readline();
    while (strlen($h) < 8) {
        echo "La taille doit être de longueur 8\n";
        $h = readline();
    }
    $decg = 0;
    $decd = 0;
    echo "Entrez l'ordre de décalage à gauche\n";
    $decg = readline();
    while ($decg <= 0) {
        echo "L'ordre doit être supérieur à 0\n";
        $decg = readline();
    }
    echo "Entrez l'ordre de décalage à droite\n";
    $decd = readline();
    while ($decd <= 0) {
        echo "L'ordre doit être supérieur à 0\n";
        $decd = readline();
    }
    $kgen = generateKey($key, $h, $decg, $decd);
    echo "Entrez la valeur N ou C à traiter\n";
    $n = readline();
    while (strlen($n) < 8) {
        echo "La taille doit être de longueur 8\n";
        $n = readline();
    }
    $choix = -1;
    while ($choix != 1 &&$choix != 2) {
        echo "Voulez-vous chiffrer ou dechiffrer? (1 pour dechiffrer et 2 pour chiffrer)\n";
        $choix = readline();
    }
    echo "Entrez la permutation P de 4 bits\n";
    $p = readline();
    while (strlen($p) < 4) {
        echo "La taille doit être de longueur 4\n";
        $p = readline();
    }
    echo "Entrez la clé de permutation pour l'opération de chiffrement ou déchiffrement\n";
    $keyc = readline();
    while (strlen($keyc) < 8) {
        echo "La taille doit être de longueur 8\n";
        $keyc = readline();
    }
    $tkey = explode(",",$kgen);
    if ($choix == 2) {
        $pn=permut($n,$keyc);
        $g0=substr($pn,0,4);
        $d0=substr($pn,4,8);
        $d1=roundDChiffre($g0,$p,$tkey[0]);
        $g1=roundGChiffre($d0,$g0,$tkey[0]);
        $d2=roundDChiffre($g1,$p,$tkey[1]);
        $g2=roundGChiffre($d1,$g1,$tkey[1]);
        $c =($g2).($d2);
        $ikey = inverse_permut(($keyc));
        $res = permut(($c),($ikey));
        echo("La valeur chiffrée est :".($res)."\n");
    }
    else {
        $pn = permut(($n),($keyc));
        $g2 = substr(($pn),0,4);
        $d2 = substr(($pn),4,8);
        $g1 = roundGDechiffre(($d2),($p),($tkey)[1]);
        $d1 = roundDDechiffre(($g2),($g1),($tkey)[1]);
        $g0 = roundGDechiffre(($d1),($p),($tkey)[0]);	
        $d0 = roundDDechiffre($g1,$g0,$tkey[0]);
        $Nd = ($g0) + ($d0);
        $ikey=inverse_permut($keyc);
        $res=permut($Nd,$ikey);
       echo("La valeur déchiffrée est : ". $res);
    }
}

main();


?>