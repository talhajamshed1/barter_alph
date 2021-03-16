<?php
include ("./includes/config.php");
session_start();
include ("./includes/functions.php");

//typed amount
$Amount = round($_GET['q'], 2);

if(DisplayLookUp("Enable Escrow")=="Yes") {
    $escrowType = DisplayLookUp("EscrowCommissionType");
    if($escrowType=="range") {
                $var_calc_amnt=($Amount < 0)?(-1 * $Amount):(1 * $Amount);
                $es_sql_1 = "SELECT * FROM ".TABLEPREFIX."escrowrangefee
                                    WHERE vActive = '1' AND above < '".$var_calc_amnt."' AND above != 0";
                $es_rs_1  = mysqli_query($conn, $es_sql_1);
                if(mysqli_num_rows($es_rs_1)){
                    $es_rw = mysqli_fetch_array($es_rs_1);
                    $fee_percent = $es_rw["nPrice"];
                    $var_escrow=$var_calc_amnt * $fee_percent / 100;
                }else{


                $es_sql = "SELECT * FROM ".TABLEPREFIX."escrowrangefee
                                    WHERE vActive = '1' AND (ROUND(nFrom) <= '".$var_calc_amnt."' AND ROUND(nTo) >= '".$var_calc_amnt."')";
                $es_rs  = mysqli_query($conn, $es_sql);
                if(mysqli_num_rows($es_rs)>0) {
                            $es_rw = mysqli_fetch_array($es_rs);
                            $fee_percent = $es_rw["nPrice"];
                            $var_escrow=$var_calc_amnt * $fee_percent / 100;
                        }
                }
            }else {
                if($escrowType=="percentage"){
                    $fee_percent = DisplayLookUp('14');
                    $var_calc_amnt=($Amount < 0)?(-1 * $Amount):(1 * $Amount);
                    $var_escrow=$var_calc_amnt * $fee_percent / 100;
                }else{
                    $fee_percent = DisplayLookUp('14');
                    $var_escrow = $fee_percent;
                }
            }
        }
        echo round($var_escrow,2);
        if($escrowType=="range" || $escrowType=="percentage")
            echo " (".$fee_percent."%)";
?>
