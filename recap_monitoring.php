<?php

include('connectToLocalDB.php');

$sql = "SELECT
        kc.upd_dt,
        rkm.Nom_kpi,    
        kc.kpi_value as kpi_j,
        kc2.kpi_value as kpi_j_7,
        kc3.kpi_value as kpi_j_14,
        kc4.kpi_value as kpi_j_21,
        kc5.kpi_value as kpi_j_28,
        kc6.kpi_value as kpi_j_35,
        kc7.kpi_value as kpi_j_42,
        kc8.kpi_value as kpi_j_49
    FROM
        oma_control.rf_kpi_monitoring rkm
        LEFT JOIN oma_control.kpis_cb kc ON rkm.Nom_kpi = kc.kpi
        AND kc.upd_dt BETWEEN '2025-01-16'
        AND '2025-01-16'
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
        LEFT JOIN oma_control.kpis_cb kc8 ON kc8.upd_dt = kc.upd_dt - INTERVAL 42 DAY
        AND rkm.Nom_kpi = kc8.kpi
        LEFT JOIN oma_control.rf_unit ru ON ru.id = rkm.unit_id
    WHERE
        rkm.Groupe_kpi IN (
            'Parc',
            'revenu',
            'Traffic',
            'OM',
            'Zebra',
            'Usage OM'
        )
    GROUP BY
        kc.upd_dt,
        rkm.Nom_kpi
    ORDER BY
    kc.upd_dt,
        rkm.Nom_kpi";

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // Insertion des résultats dans la table list_kpi_vs_Jm7
    while ($row = $result->fetch_assoc()) {
        $upd_dt = $conn->real_escape_string($row['upd_dt']);
        $Nom_kpi = $conn->real_escape_string($row['Nom_kpi']);
        $kpi_j = $conn->real_escape_string($row['kpi_j'] ?? 'NULL');
        $kpi_j_7 = $conn->real_escape_string($row['kpi_j_7'] ?? 'NULL');
        $kpi_j_14 = $conn->real_escape_string($row['kpi_j_14'] ?? 'NULL');
        $kpi_j_21 = $conn->real_escape_string($row['kpi_j_21'] ?? 'NULL');
        $kpi_j_28 = $conn->real_escape_string($row['kpi_j_28'] ?? 'NULL');
        $kpi_j_35 = $conn->real_escape_string($row['kpi_j_35'] ?? 'NULL');
        $kpi_j_42 = $conn->real_escape_string($row['kpi_j_42'] ?? 'NULL');
        $kpi_j_49 = $conn->real_escape_string($row['kpi_j_49'] ?? 'NULL');

        // Requête d'insertion
        $insert_sql = "INSERT INTO list_kpi_vs_Jm7 (
            upd_dt,
            Nom_kpi,
            kpi_j,
            kpi_j_7,
            kpi_j_14,
            kpi_j_21,
            kpi_j_28,
            kpi_j_35,
            kpi_j_42,
            kpi_j_49
        ) VALUES (
            '$upd_dt',
            '$Nom_kpi',
            '$kpi_j',
            '$kpi_j_7',
            '$kpi_j_14',
            '$kpi_j_21',
            '$kpi_j_28',
            '$kpi_j_35',
            '$kpi_j_42',
            '$kpi_j_49'
        )";

        if (!$conn->query($insert_sql)) {
            echo "Error: " . $conn->error;
        }
    }

if ($result->num_rows > 0) {
    // Display results in a table
    echo "<table id='example' class='table table-striped table-bordered' style='width:100%'>";
    echo "<thead>
            <tr>
                <th>Date</th>
                <th>Nom KPI</th>
                <th>KPI J</th>
                <th>KPI J-7</th>
                <th>KPI J-14</th>
                <th>KPI J-21</th>
                <th>KPI J-28</th>
                <th>KPI J-35</th>
                <th>KPI J-42</th>
                <th>KPI J-49</th>
            </tr>
          </thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['upd_dt'] . "</td>";
        echo "<td>" . $row['Nom_kpi'] . "</td>";
        echo "<td>" . ($row['kpi_j'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['kpi_j_7'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['kpi_j_14'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['kpi_j_21'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['kpi_j_28'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['kpi_j_35'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['kpi_j_42'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['kpi_j_49'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "Aucun résultat trouvé.";
}

?>