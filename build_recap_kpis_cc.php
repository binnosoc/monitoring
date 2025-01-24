<?php

$d = "2025-01-23";
include('connectToDB.php');
include('connectToLocalDB.php');


$excludedKPIs = [
    'ACTIVATION POSTPAID',
    'RECONNEXION POSTPAID',
    'DECONNEXION POSTPAID',
    'Revenu ROAMING SMS',
    'Revenu ROAMING VOIX',
    'REVENU EC',
    'Revenu EC VOIX BUNDLE',
    'Revenu EC SMS BUNDLE',
    'REVENU PYG',
    'EC FEES',
];


$excludedKPIsString = "'" . implode("','", $excludedKPIs) . "'";


$sql = "
    SELECT
        kc.upd_dt,
        rkc.groupe,
        rkc.frequence,
        rkc.source,
        rkc.kpi,
        kc.kpi_value AS kpi_j,
        kc2.kpi_value AS kpi_j_7,
        kc3.kpi_value AS kpi_j_14,
        kc4.kpi_value AS kpi_j_21,
        kc5.kpi_value AS kpi_j_28,
        kc6.kpi_value AS kpi_j_35,
        kc7.kpi_value AS kpi_j_42,
        kc8.kpi_value AS kpi_j_49,
        kc.slot_count AS slot,
        CASE 
            WHEN kc.kpi_value IS NULL 
              OR kc2.kpi_value IS NULL 
              OR kc3.kpi_value IS NULL 
              OR kc4.kpi_value IS NULL 
              OR kc5.kpi_value IS NULL 
              OR kc6.kpi_value IS NULL 
              OR kc7.kpi_value IS NULL 
              OR kc8.kpi_value IS NULL THEN NULL
            ELSE COALESCE(ROUND(((kc2.kpi_value + kc3.kpi_value + kc4.kpi_value + kc5.kpi_value + kc6.kpi_value + kc7.kpi_value + kc8.kpi_value) / 7), 0), '')
        END AS average_Jm7,
        CASE 
            WHEN kc.kpi_value IS NULL 
              OR kc2.kpi_value IS NULL 
              OR kc3.kpi_value IS NULL 
              OR kc4.kpi_value IS NULL 
              OR kc5.kpi_value IS NULL 
              OR kc6.kpi_value IS NULL 
              OR kc7.kpi_value IS NULL 
              OR kc8.kpi_value IS NULL THEN NULL
            ELSE COALESCE(ROUND(((kc.kpi_value - ((kc2.kpi_value + kc3.kpi_value +kc4.kpi_value + kc5.kpi_value + kc6.kpi_value + kc7.kpi_value + kc8.kpi_value) / 7)) / kc.kpi_value) * 100, 2), '')
        END AS delta_vs_Jm7
    FROM
        oma_control.rf_kpis_control rkc
        JOIN oma_control.kpis_cc kc 
            ON kc.frequence = rkc.frequence
            AND kc.source = rkc.source
            AND kc.kpi = rkc.kpi         
        LEFT JOIN oma_control.kpis_cc kc2 
            ON kc2.upd_dt = kc.upd_dt - INTERVAL 7 DAY
            AND kc2.frequence = rkc.frequence
            AND kc2.source = rkc.source
            AND kc2.kpi = rkc.kpi
        LEFT JOIN oma_control.kpis_cc kc3 
            ON kc3.upd_dt = kc.upd_dt - INTERVAL 14 DAY
            AND kc3.frequence = rkc.frequence
            AND kc3.source = rkc.source
            AND kc3.kpi = rkc.kpi
        LEFT JOIN oma_control.kpis_cc kc4 
            ON kc4.upd_dt = kc.upd_dt - INTERVAL 21 DAY
            AND kc4.frequence = rkc.frequence
            AND kc4.source = rkc.source
            AND kc4.kpi = rkc.kpi
        LEFT JOIN oma_control.kpis_cc kc5 
            ON kc5.upd_dt = kc.upd_dt - INTERVAL 28 DAY
            AND kc5.frequence = rkc.frequence
            AND kc5.source = rkc.source
            AND kc5.kpi = rkc.kpi
        LEFT JOIN oma_control.kpis_cc kc6 
            ON kc6.upd_dt = kc.upd_dt - INTERVAL 35 DAY
            AND kc6.frequence = rkc.frequence
            AND kc6.source = rkc.source
            AND kc6.kpi = rkc.kpi
        LEFT JOIN oma_control.kpis_cc kc7 
            ON kc7.upd_dt = kc.upd_dt - INTERVAL 42 DAY
            AND kc7.frequence = rkc.frequence
            AND kc7.source = rkc.source
            AND kc7.kpi = rkc.kpi
        LEFT JOIN oma_control.kpis_cc kc8 
            ON kc8.upd_dt = kc.upd_dt - INTERVAL 49 DAY
            AND kc8.frequence = rkc.frequence
            AND kc8.source = rkc.source
            AND kc8.kpi = rkc.kpi
    WHERE 
        kc.upd_dt = '$d'
        AND rkc.control_1 = 1  
        AND rkc.kpi NOT IN ($excludedKPIsString)
    ORDER BY 
        kc.kpi ASC;
";

$resSQL = $conn->query($sql);

$result = [];


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
        <h2>Liste KPI des 7 Derniers Même Jours / KPI du 2025-01-21 </h2>


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
                        <th>Average</th>
                        <th>DeltaJm7</th>
                    </tr>
                  </thead>";
        echo "<tbody>";
        $kpi_keys_init = ['kpi_j_7', 'kpi_j_14', 'kpi_j_21', 'kpi_j_28', 'kpi_j_35', 'kpi_j_42', 'kpi_j_49'];

        foreach ($resSQL as $row) {
            // Filtrer les clés commençant par 'kpi_j_' et transformer leurs valeurs
            $kpis = array_filter(
                $row,
                fn($val, $key) => str_starts_with($key, 'kpi_j_') && $val !== null,
                ARRAY_FILTER_USE_BOTH
            );
            $kpis = array_map(fn($val) => (float) str_replace(',', '.', $val), $kpis);

            // Calcul de la moyenne et de l'écart-type
            $values = array_values($kpis);
            $count = count($values);
            $average = $count > 0 ? array_sum($values) / $count : 0;

            $stdDev = $count > 0
                ? sqrt(array_sum(array_map(fn($v) => pow($v - $average, 2), $values)) / $count)
                : 0;

            $threshold = 1.5 * $stdDev;

            // Identifier les valeurs aberrantes
            $outliers = array_filter(
                $kpis,
                fn($value) => abs($value - $average) > $threshold
            );

            // Recalculer la moyenne après exclusion des outliers
            foreach ($outliers as $value) {
                $average = ($average * $count - $value) / --$count;
            }

            // Calcul de delta_vs_Jm7
            $kpi_j = (float) str_replace(',', '.', $row['kpi_j'] ?? 0);
            $delta_vs_Jm7 = $kpi_j ? round((($kpi_j - $average) / $kpi_j * 100), 2) : 0;

            // Ajouter les informations calculées
            $row['delta_vs_Jm7'] = $delta_vs_Jm7;
            $row['kpi_j'] = $kpi_j;
            $row['average_Jm7'] = $average;
            $result[] = $row;

            // Début du tableau
            echo "<tr>";
            echo "<td>{$row['groupe']}</td>";
            echo "<td>{$row['kpi']}</td>";
            echo "<td class='" . (isset($outliers['kpi_j']) ? 'text-danger' : '') . "'>" .
                ($row['kpi_j'] == 0 ? 'N/A' : $row['kpi_j']) . "</td>";

            foreach ($kpi_keys_init as $key) {
                $value = $kpis[$key] ?? 0;
                $is_outlier = isset($outliers[$key]) ? 'bg-danger text-white' : '';
                echo "<td class='$is_outlier'>" . ($value == 0 ? 'N/A' : $value) . "</td>";
            }

            echo "<td>{$row['slot']}</td>";
            echo "<td>" . ($average ? round($average, 2) : '') . "</td>";
            echo "<td>" . ($delta_vs_Jm7 ? $delta_vs_Jm7 . '%' : '') . "</td>";
            echo "</tr>";
        }



        echo "</tbody>";
        echo "</table>";


        function loadControlData($conn, $result)
        {
            $conditions = [];
            foreach ($result as $row) {
                $conditions[] = "(
                    kpi = '" . $conn->real_escape_string($row['kpi']) . "' 
                    AND source = '" . $conn->real_escape_string($row['source']) . "' 
                    AND frequence = '" . $conn->real_escape_string($row['frequence']) . "' 
                    AND groupe = '" . $conn->real_escape_string($row['groupe']) . "' 
                    AND control_1 = 1
                )";
            }

            $sql = "SELECT * FROM oma_control.rf_kpis_control WHERE " . implode(' OR ', $conditions);
            $resultSQL = $conn->query($sql);

            $controlData = [];
            while ($row = $resultSQL->fetch_assoc()) {
                $concat=$row['groupe'].$row['kpi'].$row['source'].$row['frequence'];
                $controlData[$concat] = $row;                                
            }

            return $controlData;
        }

        function processRow($row, &$recap, $row_control)
        {
            $base = str_starts_with($row['kpi'], "CBM") ? 'CBM' : 'Cube OD';

            $base = ($base !== 'CBM' && str_starts_with($row['groupe'], "CBM")) ? 'CBM' : $base;

            if (!array_key_exists($row['groupe'], $recap) || $recap[$row['groupe']]['base'] !== $base) {
                $recap[$row['groupe']] = [
                    'base' => $base,
                    'slot_status' => 'OK',
                    'delta_vs_Jm7_status' => 'OK',
                    'status' => 'OK',
                ];
            }

           
            

            if (!empty($row_control)) {
                // Monitor slot
                if ($row['slot'] != $row_control['slot_count'] && $row_control['slot_count'] != 0 && $row_control['with_slot']) {
                    $recap[$row['groupe']]['slot_status'] = "KO";
                }

                // Monitor trend
                if (!empty($row_control) && $row['delta_vs_Jm7'] != NULL) {

                    if ($row['groupe'] == 'Localisation' && (float) $row['delta_vs_Jm7'] >= 0) {
                        return;
                    }
    
                    $delta = abs((float) $row['delta_vs_Jm7']);
                    $borne_orange = $row_control['borne_orange'];
                    $borne_rouge = $row_control['borne_rouge'];
    
                    if ($borne_orange != 0 && $delta >= $borne_orange && $delta < $borne_rouge) {
                        echo " NOK ======= " . $row_control['kpi'] . " ======= " . $row['delta_vs_Jm7'] . " >= " . $borne_orange . "<br>";
                        $recap[$row['groupe']]['delta_vs_Jm7_status'] = "NOK";
                    }
    
                    if ($borne_rouge != 0 && $delta >= $borne_rouge) {
                        echo " KO ======= " . $row_control['kpi'] . " ======= " . $row['delta_vs_Jm7'] . " >= " . $borne_rouge . "<br>";
                        $recap[$row['groupe']]['delta_vs_Jm7_status'] = "KO";
                    }
    
    
    
                }
            }

            // Calculate global status
            $statuses = [$recap[$row['groupe']]['slot_status'], $recap[$row['groupe']]['delta_vs_Jm7_status']];
            if (in_array("KO", $statuses, true)) {
                $recap[$row['groupe']]['status'] = "KO";
            } elseif (in_array("NOK", $statuses, true)) {
                $recap[$row['groupe']]['status'] = "NOK";
            }
        }

        function updateRecapTableBatch($connLocal, $recap, $d)
        {
            $sqlInsert = "INSERT INTO my_test_database.recap_kpis_cc 
                (upd_dt, base, groupe, slot_status, delta_vs_Jm7_status, status)
                VALUES ";
            $values = [];

            foreach ($recap as $groupName => $group_details) {
                $values[] = sprintf(
                    "('%s', '%s', '%s', '%s', '%s', '%s')",
                    $connLocal->real_escape_string($d),
                    $connLocal->real_escape_string($group_details['base']),
                    $connLocal->real_escape_string($groupName),
                    $connLocal->real_escape_string($group_details['slot_status']),
                    $connLocal->real_escape_string($group_details['delta_vs_Jm7_status']),
                    $connLocal->real_escape_string($group_details['status'])
                );
            }

            $sqlDelete = "DELETE FROM my_test_database.recap_kpis_cc WHERE upd_dt = '$d';";
            $connLocal->query($sqlDelete);

            $sqlInsert .= implode(',', $values) . ";";
            if (!$connLocal->query($sqlInsert)) {
                die("Erreur lors de l'insertion batch : " . $connLocal->error);
            }
        }

        // Main Process
        $controlData = loadControlData($conn, $result);
        $recap = [];

        foreach ($result as $row) {
            $concat=$row['groupe'].$row['kpi'].$row['source'].$row['frequence'];
            $row_control = $controlData[$concat] ?? null;
            processRow($row, $recap, $row_control);
        }

        updateRecapTableBatch($connLocal, $recap, $d);

        // Render the recap table
        $sql = 'SELECT upd_dt, base, groupe, slot_status, delta_vs_Jm7_status, status 
                FROM my_test_database.recap_kpis_cc 
                WHERE upd_dt = "' . $d . '" AND status!="" 
                ORDER BY base, groupe;';
        $resSQL = $connLocal->query($sql);

        echo "<table id='example2' class='mt-5 table table-striped table-bordered' style='width:100%'>";
        echo "<legend class='mt-5'>Tableau Recapitulatif</legend>";
        echo "<thead class='mt-5'>
                <tr>
                    <th>Date</th>
                    <th>Base</th>
                    <th>Groupe</th>
                    <th>Slot status</th>                          
                    <th>Delta Jm7</th>  
                    <th>Status</th>               
                </tr>
              </thead>";
        echo "<tbody>";

        while ($groupDetails = $resSQL->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $d . "</td>";
            echo "<td>" . $groupDetails['base'] . "</td>";
            echo "<td>" . $groupDetails['groupe'] . "</td>";

            $slotClass = match ($groupDetails['slot_status']) {
                'OK' => 'table-success',
                'KO' => 'table-danger',
                'NOK' => 'table-warning',
                default => '',
            };
            echo "<td class='$slotClass'>" . $groupDetails['slot_status'] . "</td>";

            $trendClass = match ($groupDetails['delta_vs_Jm7_status']) {
                'OK' => 'table-success',
                'KO' => 'table-danger',
                'NOK' => 'table-warning',
                default => '',
            };
            echo "<td class='$trendClass'>" . $groupDetails['delta_vs_Jm7_status'] . "</td>";

            $statusClass = match ($groupDetails['status']) {
                'OK' => 'table-success',
                'KO' => 'table-danger',
                'NOK' => 'table-warning',
                default => '',
            };
            echo "<td class='$statusClass'>" . $groupDetails['status'] . "</td>";

            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
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
$connLocal->close();
?>