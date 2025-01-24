<?php
include('connectToDB.php');
$kpi = "ACTIF POSTPAID";
$source = "HBY";
$frequence = "Daily";
$groupe = "Parc";
$selectSQL = "SELECT * FROM oma_control.rf_kpis_control WHERE kpi = '$kpi' AND source = '$source' AND frequence = '$frequence' AND groupe = '$groupe' AND control_1 = 1";
$resultSQL = $conn->query($selectSQL);
$row_control = $resultSQL->fetch_assoc();
foreach($row_control as $key => $value) {
    echo $key . " : " . $value . "<br>";
}
?>

