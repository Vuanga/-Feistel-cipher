<?php

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

function permute(string$input,array$permutation){
   return implode('',array_map(function ($i) use ($input){return substr($input,$i,1);},$permutation));
}

function xor_strings(string$a,string$b){
   return implode('',array_map(function ($x,$y){return (int)$x^(int)$y;},str_split($a),str_split($b)));
}

function and_strings(string$a,string$b){
   return implode('',array_map(function ($x,$y){return (int)$x&(int)$y;},str_split($a),str_split($b)));
}
function or_strings(string$a,string$b){
   return implode('',array_map(function ($x,$y){return (int)$x|(int)$y;},str_split($a),str_split($b)));
}

function left_shift(string$a,int$shift){
   return substr("$a$a",-$shift-strlen($a),-strlen($a)-$shift+strlen("$a$a"));
}

function right_shift(string$a,int$shift){
   return substr("$a$a",$shift,strlen("$a$a")-$shift-strlen("$a$a"));
}

?>
