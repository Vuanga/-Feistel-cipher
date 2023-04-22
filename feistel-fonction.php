<?php

function OuExclusif($val1, $val2) {
    $res = "";
    $tabk = array_fill(0, strlen($val1), "");
    for ($i = 0; $i < strlen($val1); $i++) {
        $v1 = substr($val1, $i, 1);
        $v2 = substr($val2, $i, 1);
        $tabk[$i] = ($v1 == $v2) ? "0" : "1";
    }
    foreach ($tabk as $i) {
        $res .= $i;
    }
    return $res;
}

function OuLogique($val1, $val2) {
    $res = "";
    $tabk = array_fill(0, strlen($val1), "");
    for ($i = 0; $i < strlen($val1); $i++) {
        $v1 = substr($val1, $i, 1);
        $v2 = substr($val2, $i, 1);
        $tabk[$i] = ($v1 == "1" || $v2 == "1") ? "1" : "0";
    }
    foreach ($tabk as $i) {
        $res .= $i;
    }
    return $res;
}

function ETlogique($va11, $val2) {
    $res = "";
    $tabk = array_fill(0, strlen($va11), "");
    for ($i = 0; $i < strlen($va11); $i++) {
        $v1 = substr($va11, $i, 1);
        $v2 = substr($val2, $i, 1);
        $tabk[$i] = ($v1 == "1" &&$v2 == "1") ? "1" : "0";
    }
    foreach ($tabk as $value) {
        $res .= $value;
    }
    return $res;
}

function permut($val, $k) {
    $res = "";
    $tabk = array_fill(0,strlen($val),0);
    for ($i=0; $i<strlen($val); $i++) {
        $id=substr($k,$i,1);
        $vid=$id;
        $tabk[$i] = $val[$vid];
        $res = $tabk[$i];
    }
    return $res;
}



function inverse_permut($k) {
    $res = "";
    $tabk = array_fill(0, strlen($k), 0);
    for ($i = 0; $i < strlen($k); $i++) {
        $id = substr($k, $i, 1);
        $vid = $id;
        $tabk[$vid] = $i;
    }
    $res = implode("", $tabk);
    return $res;
}

function decalage($val, $ordre, $gauche) {
    $res = "";
    $tabk = array_fill(0, strlen($val), "");
    $s = ($gauche) ? -1 : 1;
    for ($i = 0; $i < strlen($val); $i++) {
        $v1 = substr($val, $i, 1);
        $o = $ordre;
        $j = $i;
        while ($o > 0) {
            if ($j + $s < 0) {
                $j = strlen($val) - 1;
            } elseif ($j + $s >= strlen($val)) {
                $j = 0;
            } else {
                $j = $j + $s;
            }
            $o -= 1;
        }
        $tabk[$j] = $v1;
    }
    $res = implode("",$tabk);
    return $res;
}

function generateKey($k, $pk, $gdecalage, $ddecalage) {
    $res = "";
    $nk = permut($k,$pk);
    $k1=substr($nk,0,4);
    $k2=substr($nk,4,8);
    $nk1=OuExclusif($k1,$k2);
    $nk2=ETlogique($k1,$k2);
    $dnk1=decalage($nk1,$gdecalage,true);
    $dnk2=decalage($nk2,$ddecalage,false);
    $res=(($dnk1).",".($dnk2));
    return ($res);
}

function roundDChiffre($val, $kp, $k) {
$res = "";
$perm = permut(($val),($kp));
$res = OuExclusif(($perm),($k));

return ($res);

}



function roundGChiffre($vald, $valg, $k) {
    $res = "";
    $fc = OuLogique($valg, $k);
    $res = OuExclusif($vald, $fc);
    return $res;
}

function roundGDechiffre($val, $kp, $k) {
    $res = "";
    $nkp = inverse_permut($kp);
    $c = OuExclusif($val, $k);
    $res = permut($c, $nkp);
    return $res;
}

function roundDDechiffre($vald, $valg, $k) {
    $res = "";
    $fc = OuLogique($valg, $k);
    $res = OuExclusif($vald, $fc);
    return $res;
}

?>