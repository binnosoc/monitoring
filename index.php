<?php

$d = "2025-01-21";
include('connectToDB.php');
include('connectToLocalDB.php');

$exceptKPI = array('Internet Entreprise', 'Internet PRO', 'Wifiber', 'Freefiber');

$excludedKPIs = [
    'ACTIVATION POSTPAID',
    'RECONNEXION POSTPAID',
    'DECONNEXION POSTPAID',
    'Revenu ROAMING SMS',
    'Revenu ROAMING VOIX',
    'REVENU EC',
    'REVENU PYG'
];
$includeGroupe = [
    'Parc',
    'revenu',
    'Traffic',
    'OM',
    'Zebra',
    'Usage OM',
    'EC',
    'DWH'
];
$excludedKPIsString = "'" . implode("','", $excludedKPIs) . "'";
$includeGroupeString = "'" . implode("','", $includeGroupe) . "'";

$sql = "SELECT
            kc.upd_dt,
            rkm.Groupe_kpi as groupe_kpi,
            rkm.Nom_kpi,    
            kc.kpi_value as kpi_j,
            kc2.kpi_value as kpi_j_7,
            kc3.kpi_value as kpi_j_14,
            kc4.kpi_value as kpi_j_21,
            kc5.kpi_value as kpi_j_28,
            kc6.kpi_value as kpi_j_35,
            kc7.kpi_value as kpi_j_42,
            kc8.kpi_value as kpi_j_49,
            kc.slot_count as slot,
            COALESCE(ROUND(((kc.kpi_value-kc_big5.kpi_value)/kc.kpi_value)*100,2),'') as cube_vs_cbm,
            COALESCE(ROUND(((kc2.kpi_value + kc3.kpi_value + kc4.kpi_value + kc5.kpi_value + kc6.kpi_value + kc7.kpi_value + kc8.kpi_value)/7), 0), '') AS average_Jm7,
            COALESCE(ROUND(((kc.kpi_value - ((kc2.kpi_value + kc3.kpi_value + kc4.kpi_value + kc5.kpi_value + kc6.kpi_value + kc7.kpi_value + kc8.kpi_value)/7)) / kc.kpi_value) * 100, 2), '') AS delta_vs_Jm7
        FROM
            oma_control.rf_kpi_monitoring rkm
        
            LEFT JOIN oma_control.kpis_cb kc ON rkm.Nom_kpi = kc.kpi
            AND kc.upd_dt BETWEEN '" . $d . "'
            AND '" . $d . "'
            
            LEFT JOIN oma_control.kpis_cc kc_big5 ON kc_big5.kpi = CONCAT('CBM - ', rkm.Nom_kpi) 
            AND kc_big5.kpi = CONCAT('CBM - ', kc.kpi) 
            AND kc.upd_dt = kc_big5.upd_dt
            
            LEFT JOIN oma_control.kpis_cb kc2 ON kc2.upd_dt = kc.upd_dt - INTERVAL 7 DAY
            AND rkm.Nom_kpi = kc2.kpi
            
            LEFT JOIN oma_control.kpis_cb kc3 ON kc3.upd_dt = kc.upd_dt - INTERVAL 14 DAY
            AND rkm.Nom_kpi = kc3.kpi
            
            LEFT JOIN oma_control.kpis_cb kc4 ON kc4.upd_dt = kc.upd_dt - INTERVAL 21 DAY
            AND rkm.Nom_kpi = kc4.kpi
            
            LEFT JOIN oma_control.kpis_cb kc5 ON kc5.upd_dt = kc.upd_dt - INTERVAL 28 DAY
            AND rkm.Nom_kpi = kc5.kpi
            
            LEFT JOIN oma_control.kpis_cb kc6 ON kc6.upd_dt = kc.upd_dt - INTERVAL 35 DAY
            AND rkm.Nom_kpi = kc6.kpi
            
            LEFT JOIN oma_control.kpis_cb kc7 ON kc7.upd_dt = kc.upd_dt - INTERVAL 42 DAY
            AND rkm.Nom_kpi = kc7.kpi
            AND rkm.Nom_kpi = kc6.kpi
            
            LEFT JOIN oma_control.kpis_cb kc8 ON kc8.upd_dt = kc.upd_dt - INTERVAL 42 DAY
            AND rkm.Nom_kpi = kc8.kpi
            AND rkm.Nom_kpi = kc7.kpi
            
            LEFT JOIN oma_control.rf_unit ru ON ru.id = rkm.unit_id
        WHERE
            rkm.Groupe_kpi IN ($includeGroupeString) AND
             rkm.Nom_kpi NOT IN ($excludedKPIsString)
        GROUP BY
            kc.upd_dt,
            rkm.Nom_kpi
        ORDER BY
            kc.upd_dt,
            rkm.Nom_kpi";

$result = $conn->query($sql);


$recap = [];



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap DataTable Example</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Liste KPI des 7 Derniers Même Jours / KPI du 2025-01-16 </h2>


        <?php







        echo "<table id='example' class='table table-striped table-bordered' style='width:100%'>";
        echo "<legend>Tableau KPI Monitoring</legend>";
        echo "<thead>
                    <tr>
                        
                        <th>Groupe</th>
                        <th>Nom KPI</th>
                        <th>KPI J</th>
                        <th>KPI J-7</th>
                        <th>KPI J-14</th>
                        <th>KPI J-21</th>
                        <th>KPI J-28</th>
                        <th>KPI J-35</th>
                        <th>KPI J-42</th>
                        <th>KPI J-49</th>
                        <th>Slot</th>
                        <th>Cube/CBM</th>
                        <th>Average</th>
                        <th>DeltaJm7</th>
                    </tr>
                  </thead>";
        echo "<tbody>";

        foreach ($result as $row) {
            echo "<tr>";
            // echo "<td>" . $row['upd_dt'] . "</td>";
            echo "<td>" . $row['groupe_kpi'] . "</td>";
            echo "<td>" . $row['Nom_kpi'] . "</td>";
            echo "<td>" . ($row['kpi_j'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['kpi_j_7'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['kpi_j_14'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['kpi_j_21'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['kpi_j_28'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['kpi_j_35'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['kpi_j_42'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['kpi_j_49'] ?? 'N/A') . "</td>";
            echo "<td>" . $row['slot'] . "</td>";
            echo "<td>" . (!empty($row['cube_vs_cbm']) ? $row['cube_vs_cbm'] . '%' : '') . "</td>";
            echo "<td>" . (!empty($row['average_Jm7']) ? $row['average_Jm7'] . '' : '') . "</td>";
            echo "<td>" . (!empty($row['delta_vs_Jm7']) ? $row['delta_vs_Jm7'] . '%' : '') . "</td>";


            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";

        function initGroup($groupName)
        {
            global $recap;
            $groupName = strtoupper($groupName);
            if (!array_key_exists($groupName, $recap)) {
                $recap[$groupName] = [
                    'slot_status' => 'OK',
                    'cube_vs_cbm_status' => 'OK',
                    'delta_vs_Jm7_status' => 'OK',
                    'zebra_control_status' => 'OK',
                    'zebra_channel_control_status' => 'OK',
                    'status' => 'OK',
                ];
            }
        }

        function updateRecapTable()
        {
            global $recap, $connLocal, $d;
            // Vérifier si la sélection contient des lignes
            $sql = 'SELECT * FROM my_test_database.recap_monitoring WHERE upd_dt = ?';
            $stmt = $connLocal->prepare($sql);
            $stmt->bind_param('s', $d); // 's' pour chaîne de caractères
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result->num_rows > 0) {

                $sqlInsert = "INSERT INTO my_test_database.recap_monitoring 
        (upd_dt, base, groupe, slot_status, cube_vs_cbm_status, delta_vs_Jm7_status, status, comment, processing_time) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmtInsert = $connLocal->prepare($sqlInsert);


                $data = [
                    [$d, 'Cube CB', 'REVENUE', 'OK', 'OK', 'OK', 'OK', '', ''],
                    [$d, 'Cube CB', 'PARC', 'OK', 'OK', 'OK', 'OK', '', ''],
                    [$d, 'Cube CB', 'TRAFFIC', 'OK', 'OK', 'OK', 'OK', '', ''],
                    [$d, 'Cube CB', 'ZEBRA', 'OK', 'OK', 'OK', 'OK', '', ''],
                    [$d, 'Cube CB', 'ZEBRA CHANNEL', 'OK', 'OK', 'OK', 'OK', '', ''],
                    [$d, 'Cube OD', 'OM', 'OK', 'OK', 'OK', 'OK', '', ''],
                    [$d, 'CBM', 'USAGE OM', 'OK', 'OK', 'OK', 'OK', '', ''],
                    [$d, 'Cube CB', 'EC', 'OK', 'OK', 'OK', 'OK', '', ''],
                    [$d, 'Cube CB', 'DWH', 'OK', 'OK', 'OK', 'OK', '', ''],
                    [$d, 'CBM', 'OM TRANSACTIONS', '', '', '', '', '', ''],
                    [$d, 'CBM', 'GLOBAL DAILY USAGE', '', '', '', '', '', ''],
                    [$d, 'CBM', 'CONTRAT', '', '', '', '', '', ''],
                ];


                foreach ($data as $row) {
                    $stmtInsert->bind_param('sssssssss', ...$row);
                    if (!$stmtInsert->execute()) {
                        echo "Erreur lors de l'insertion : " . $stmtInsert->error;
                    }
                }

                echo "Données initiales insérées avec succès pour la date $d.";
            } else {

                $sqlUpdate = "UPDATE my_test_database.recap_monitoring 
                            SET slot_status = ?, cube_vs_cbm_status = ?, delta_vs_Jm7_status = ?, status = ?
                            WHERE upd_dt = ? AND groupe = ? AND status !=''";

                $stmtUpdate = $connLocal->prepare($sqlUpdate);

                foreach ($recap as $groupName => $statuses) {

                    $global_status = "OK";
                    $slot_status = $statuses['slot_status'];
                    $cube_vs_cbm_status = $statuses['cube_vs_cbm_status'];
                    $delta_vs_Jm7_status = $statuses['delta_vs_Jm7_status'];

                    if ($slot_status == "KO" || $cube_vs_cbm_status == "KO" || $delta_vs_Jm7_status == "KO") {
                        $global_status = "KO";

                    } elseif ($slot_status == "NOK" || $cube_vs_cbm_status == "NOK" || $delta_vs_Jm7_status == "NOK") {
                        $global_status = "NOK";
                    }

                    $recap[strtoupper($groupName)]['status'] = $global_status;

                    $stmtUpdate->bind_param(
                        'ssssss',
                        $slot_status,
                        $cube_vs_cbm_status,
                        $delta_vs_Jm7_status,
                        $global_status,
                        $d,
                        $groupName
                    );

                    if (!$stmtUpdate->execute()) {
                        echo "Erreur lors de la mise à jour pour le groupe $groupName : " . $stmtUpdate->error;
                    }
                }

                echo "Données mis à jours avec succès pour la date $d.";
            }



        }

        function monitorSlot($row)
        {
            global $recap, $row_control;
            if (!array_key_exists(strtoupper($row['groupe_kpi']), $recap)) {
                initGroup(strtoupper($row['groupe_kpi']));
            }


            if (!empty($row_control)) {
                if ($row['slot'] != $row_control['Slot']) {
                    $recap[strtoupper($row['groupe_kpi'])]['slot_status'] = "KO";
                }
            }
        }




        function monitorCubeVsCBM($row)
        {

            global $recap, $row_control;
            if (!array_key_exists(strtoupper($row['groupe_kpi']), $recap)) {
                initGroup(strtoupper($row['groupe_kpi']));
            }
            if (!empty($row_control)) {
                if (abs((float) $row['cube_vs_cbm']) >= 1 && $row['cube_vs_cbm'] < 5) {
                    $recap[strtoupper($row['groupe_kpi'])]['cube_vs_cbm_status'] = "NOK";

                }
                if (abs((float) $row['cube_vs_cbm']) >= 5) {
                    $recap[strtoupper($row['groupe_kpi'])]['cube_vs_cbm_status'] = "KO";

                }
            }


        }

        function monitorTrend_Jm7($row)
        {
            global $recap, $row_control;

            if (!array_key_exists(strtoupper($row['groupe_kpi']), $recap)) {
                initGroup(strtoupper($row['groupe_kpi']));
            }

            if (!empty($row_control)) {


                if (abs((float)$row['delta_vs_Jm7']) >= $row_control['Borne Orange'] && abs((float)$row['delta_vs_Jm7']) < $row_control['Borne Rouge']) {
                    $recap[strtoupper($row['groupe_kpi'])]['delta_vs_Jm7_status'] = "NOK";


                }
                if (abs((float)$row['delta_vs_Jm7']) >= $row_control['Borne Rouge']) {
                    echo $row_control['KPI'] . " " . $row['delta_vs_Jm7'] . " " . $row_control['Borne Rouge'] . "<br>";

                    $recap[strtoupper($row['groupe_kpi'])]['delta_vs_Jm7_status'] = "KO";

                }
            }

        }

        function monitorGroupeZEBRA(): void
        {
            global $recap, $conn, $row_control, $d;

            if (!array_key_exists('ZEBRA', $recap)) {
                initGroup('ZEBRA');
            }


            $sql_d1 = 'SELECT  SUM( cbz1.qty) as sum_qty_d1,
                        sum(cbz1.amount) as sum_mnt_d1
                        FROM DM_CB.cb_zebra cbz1
                        WHERE cbz1.upd_dt ="' . $d . '";';

            $sql_d2 = 'SELECT  SUM( cbz2.qty) as sum_qty_d2,
                        sum(cbz2.amount) as sum_mnt_d2
                        FROM DM_CB.cb_zebra cbz2
                        WHERE cbz2.upd_dt ="' . $d . '" - INTERVAL 7 DAY ;';



            $resultSQL_d1 = $conn->query($sql_d1);
            $resultSQL_d2 = $conn->query($sql_d2);

            $row_zebra_d1 = $resultSQL_d1->fetch_assoc();
            $row_zebra_d2 = $resultSQL_d2->fetch_assoc();

            $delta_qty_zebra = round((($row_zebra_d1['sum_qty_d1'] - $row_zebra_d2['sum_qty_d2']) / $row_zebra_d1['sum_qty_d1']) * 100, 2);
            $delta_amount_zebra = round((($row_zebra_d1['sum_mnt_d1'] - $row_zebra_d2['sum_mnt_d2']) / $row_zebra_d1['sum_mnt_d1']) * 100, 2);

            if ((abs((float) $delta_qty_zebra) < 15 && abs((float) $delta_qty_zebra) > 5) || (abs((float) $delta_amount_zebra) < 15 && abs((float) $delta_amount_zebra) > 5)) {
                $recap['ZEBRA']['zebra_control_status'] = "NOK";
            }

            if ($delta_qty_zebra > 15 && $delta_amount_zebra > 15) {
                $recap['ZEBRA']['zebra_control_status'] = "KO";
            }
        }



        function monitorGroupeZEBRAChannel(): void
        {
            global $recap, $conn, $row_control, $d;


            initGroup('ZEBRA Channel');


            $sql_d1 = 'SELECT  SUM( cbz1.qty) as sum_qty_d1,
                        sum(cbz1.transfer_amount) as sum_mnt_d1
                        FROM DM_CB.cb_zebra_channel cbz1
                        WHERE cbz1.upd_dt ="' . $d . '";';

            $sql_d2 = 'SELECT  SUM( cbz2.qty) as sum_qty_d2,
                        sum(cbz2.transfer_amount) as sum_mnt_d2
                        FROM DM_CB.cb_zebra_channel cbz2
                        WHERE cbz2.upd_dt ="' . $d . '" - INTERVAL 7 DAY ;';



            $resultSQL_d1 = $conn->query($sql_d1);
            $resultSQL_d2 = $conn->query($sql_d2);

            $row_zebra_d1 = $resultSQL_d1->fetch_assoc();
            $row_zebra_d2 = $resultSQL_d2->fetch_assoc();

            $delta_qty_zebra = round((($row_zebra_d1['sum_qty_d1'] - $row_zebra_d2['sum_qty_d2']) / $row_zebra_d1['sum_qty_d1']) * 100, 2);
            $delta_amount_zebra = round((($row_zebra_d1['sum_mnt_d1'] - $row_zebra_d2['sum_mnt_d2']) / $row_zebra_d1['sum_mnt_d1']) * 100, 2);

            if ((abs((float) $delta_qty_zebra) < 15 && abs((float) $delta_qty_zebra) > 5) || (abs((float) $delta_amount_zebra) < 15 && abs((float) $delta_amount_zebra) > 5)) {
                $recap['ZEBRA CHANNEL']['zebra_channel_control_status'] = "NOK";
            }

            if ($delta_qty_zebra > 15 && $delta_amount_zebra > 15) {
                $recap['ZEBRA CHANNEL']['zebra_channel_control_status'] = "KO";
            }




        }

        echo "<table id='example2' class='mt-5 table table-striped table-bordered' style='width:100%'>";
        echo "<legend class='mt-5' >Tableau Recapitulatif</legend>";
        echo "<thead class='mt-5'>
                    <tr>
                        <th>Date</th>
                        <th>Base</th>
                        <th>Groupe</th>
                        
                        <th>Slot status</th>
                        <th>cube vs cbm</th>     
                        <th>Delta Jm7</th>  
                        <th>Status</th>               
                        
                    </tr>
                  </thead>";
        echo "<tbody>";
        foreach ($result as $row) {


            $nom_kpi = $row['Nom_kpi'];
            if (!in_array($nom_kpi, $exceptKPI)) {
                $selectSQL = "SELECT * FROM my_test_database.kpi_control WHERE KPI = '$nom_kpi' AND KPI NOT IN ($excludedKPIsString) ";
                $resultSQL = $connLocal->query($selectSQL);
                $row_control = $resultSQL->fetch_assoc();
            } else {
                $row_control = "";
            }


            monitorSlot($row);
            monitorCubeVsCBM($row);
            monitorTrend_Jm7($row);


        }

        // monitorGroupeZEBRA();
        // monitorGroupeZEBRAChannel();
        updateRecapTable();

        $sql = 'SELECT upd_dt, base, groupe, slot_status, cube_vs_cbm_status, delta_vs_Jm7_status, status 
        FROM my_test_database.recap_monitoring 
        WHERE upd_dt = "' . $d . '" AND status!="" ;';

        $result = $connLocal->query($sql);

        while ($groupDetails = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $d . "</td>";
            echo "<td>" . $groupDetails['base'] . "</td>";
            echo "<td>" . $groupDetails['groupe'] . "</td>";


            if ($groupDetails['slot_status'] === 'OK') {
                $slotClass = 'table-success';
            } elseif ($groupDetails['slot_status'] === 'KO') {
                $slotClass = 'table-danger';
            } elseif ($groupDetails['slot_status'] === 'NOK') {
                $slotClass = 'table-warning';
            } else {
                $slotClass = '';
            }
            echo "<td class='" . $slotClass . "'>" . $groupDetails['slot_status'] . "</td>";

            if ($groupDetails['cube_vs_cbm_status'] === 'OK') {
                $cubeClass = 'table-success';
            } elseif ($groupDetails['cube_vs_cbm_status'] === 'KO') {
                $cubeClass = 'table-danger';
            } elseif ($groupDetails['cube_vs_cbm_status'] === 'NOK') {
                $cubeClass = 'table-warning';
            } else {
                $cubeClass = '';
            }
            echo "<td class='" . $cubeClass . "'>" . $groupDetails['cube_vs_cbm_status'] . "</td>";


            if ($groupDetails['delta_vs_Jm7_status'] === 'OK') {
                $trendClass = 'table-success';
            } elseif ($groupDetails['delta_vs_Jm7_status'] === 'KO') {
                $trendClass = 'table-danger';
            } elseif ($groupDetails['delta_vs_Jm7_status'] === 'NOK') {
                $trendClass = 'table-warning';
            } else {
                $trendClass = '';
            }
            echo "<td class='" . $trendClass . "'>" . $groupDetails['delta_vs_Jm7_status'] . "</td>";


            if ($groupDetails['status'] === 'OK') {
                $statusClass = 'table-success';
            } elseif ($groupDetails['status'] === 'KO') {
                $statusClass = 'table-danger';
            } elseif ($groupDetails['status'] === 'NOK') {
                $statusClass = 'table-warning';
            } else {
                $statusClass = '';
            }
            echo "<td class='" . $statusClass . "'>" . $groupDetails['status'] . "</td>";

            echo "</tr>";
        }

        // foreach ($recap as $groupName => $groupDetails) {
        //     echo "<tr>";
        //     echo "<td>" . $d . "</td>";
        //     echo "<td>" . $groupDetails['zebra_channel_control_status'] . "</td>";
        //     echo "<td>" . $groupName . "</td>";
        

        //     if ($groupDetails['slot_status'] === 'OK') {
        //         $slotClass = 'table-success';
        //     } elseif ($groupDetails['slot_status'] === 'KO') {
        //         $slotClass = 'table-danger';
        //     } elseif ($groupDetails['slot_status'] === 'NOK') {
        //         $slotClass = 'table-warning';
        //     } else {
        //         $slotClass = ''; 
        //     }
        //     echo "<td class='" . $slotClass . "'>" . $groupDetails['slot_status'] . "</td>";
        
        //     if ($groupDetails['cube_vs_cbm_status'] === 'OK') {
        //         $cubeClass = 'table-success';
        //     } elseif ($groupDetails['cube_vs_cbm_status'] === 'KO') {
        //         $cubeClass = 'table-danger';
        //     } elseif ($groupDetails['cube_vs_cbm_status'] === 'NOK') {
        //         $cubeClass = 'table-warning';
        //     } else {
        //         $cubeClass = ''; 
        //     }
        //     echo "<td class='" . $cubeClass . "'>" . $groupDetails['cube_vs_cbm_status'] . "</td>";
        

        //     if ($groupDetails['delta_vs_Jm7_status'] === 'OK') {
        //         $trendClass = 'table-success';
        //     } elseif ($groupDetails['delta_vs_Jm7_status'] === 'KO') {
        //         $trendClass = 'table-danger';
        //     } elseif ($groupDetails['delta_vs_Jm7_status'] === 'NOK') {
        //         $trendClass = 'table-warning';
        //     } else {
        //         $trendClass = ''; 
        //     }
        //     echo "<td class='" . $trendClass . "'>" . $groupDetails['delta_vs_Jm7_status'] . "</td>";
        

        //      if ($groupDetails['status'] === 'OK') {
        //         $statusClass = 'table-success';
        //     } elseif ($groupDetails['status'] === 'KO') {
        //         $statusClass = 'table-danger';
        //     } elseif ($groupDetails['status'] === 'NOK') {
        //         $statusClass = 'table-warning';
        //     } else {
        //         $statusClass = ''; 
        //     }
        //     echo "<td class='" . $statusClass . "'>" . $groupDetails['status'] . "</td>";
        
        //     echo "</tr>";
        // }
        ?>


    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#example').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
                }
            });

            // $('#example2').DataTable({
            //     paging: true,
            //     searching: true,
            //     ordering: true,
            //     info: true,
            //     language: {
            //         url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            //     }
            // });
        });
    </script>
</body>

</html>

<?php
// Close the connection
$conn->close();
?>