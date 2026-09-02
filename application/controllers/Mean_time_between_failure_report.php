<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mean_time_between_failure_report extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        //$this->load->helper('url');

        $this->load->library('pagination');

        if (!$this->user_model->logged_in()) {
            die(redirect("/dashboard?error=No permission to view this content."));
        }
    }

    public function index()
    {


        $assetTypes = $this->db->select('*')
            ->from('asset_types')
            ->get()
            ->result();


        // Load Views
        $this->load->view('header', ['title' => "M T B F", 'title2' => "M T B F", "styles" => [
            "design/css/performance.css",
            "design/css/custom-datatable.css",
        ]]);

        $this->load->view('mean-time-between-failure-report', [

            'assetTypes' => $assetTypes
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/report-suite.js?v=1',
            'design/js/mean-time-between-failure-report.js?v=2',
        ]]);
    }

    /**
     * Fetch summary (by type) + breakdown (all rows) in one go
     * Returns: array of items:
     * [
     *   'Type ID' => 1,
     *   'Type' => 'Pump',
     *   'Average MTBF (Days)' => 2,
     *   'Average MTBF (Hours)' => 3,
     *   'Cycles Counted' => 5,
     *   'Breakdowns' => [
     *      ['Asset Code'=>'FT-01','Asset Name'=>'Pump A','Type'=>'Pump',
     *       'Serviceable Date'=>'06-Aug-2025 12:54','Unserviceable Date'=>'08-Aug-2025 13:39',
     *       'MTBF (Days)'=>2,'MTBF (Hours)'=>0,'MTBF (Minutes)'=>45],
     *      ...
     *   ]
     * ]
     */
    private function fetchSummaryWithBreakdown($year, $month)
    {
        $year        = $year ?: date('Y');
        $month_param = $month;

        // Date range
        if (!empty($month_param)) {
            $startDate = sprintf('%04d-%02d-01 00:00:00', $year, (int)$month_param);
            $endDate   = date('Y-m-t 23:59:59', strtotime("$year-$month_param-01"));
        } else {
            $startDate = "$year-01-01 00:00:00";
            $endDate   = "$year-12-31 23:59:59";
        }

        // ---- Summary (by type)
        $sqlSummary = "
        SELECT 
            at.asset_id AS type_id,
            at.name     AS type_name,
            AVG(TIMESTAMPDIFF(SECOND, s.timestamp, u.timestamp)) AS avg_seconds,
            COUNT(*) AS cycle_count
        FROM asset_logs s
        JOIN equipments_asset ea ON ea.equipment_id = s.log_item_id
        JOIN asset_types at      ON at.asset_id = ea.equipment_type
        JOIN asset_logs u ON u.log_item_id = s.log_item_id
            AND u.log_code = 'Asset_Updated'
            AND u.log_description LIKE '%Unserviceable%'
            AND u.timestamp = (
                SELECT MIN(u2.timestamp)
                FROM asset_logs u2
                WHERE u2.log_item_id = s.log_item_id
                  AND u2.log_code = 'Asset_Updated'
                  AND u2.log_description LIKE '%Unserviceable%'
                  AND u2.timestamp > s.timestamp
            )
        WHERE s.log_code = 'Asset_Updated'
          AND s.log_description LIKE '%Serviceable%'
          AND u.timestamp BETWEEN ? AND ?
        GROUP BY at.asset_id, at.name
        ORDER BY at.name
        ";
        $summaryRows = $this->db->query($sqlSummary, [$startDate, $endDate])->result_array();

        $result = [];

        foreach ($summaryRows as $r) {
            $avgSecs = (int) round($r['avg_seconds']);
            $days    = intdiv($avgSecs, 86400);
            $hours   = intdiv(($avgSecs % 86400), 3600);
            $minutes = intdiv(($avgSecs % 3600), 60);

            // ---- Breakdown for this type
            $sqlBreakdown = "
            SELECT
                ea.equipment_registration  AS asset_code,
                ea.equipment_name,
                at.name                    AS type_name,
                s.timestamp                AS service_ts,
                u.timestamp                AS unservice_ts,
                TIMESTAMPDIFF(SECOND, s.timestamp, u.timestamp) AS diff_seconds
            FROM asset_logs s
            JOIN equipments_asset ea ON ea.equipment_id = s.log_item_id
            JOIN asset_types at      ON at.asset_id = ea.equipment_type
            JOIN asset_logs u
                ON u.log_item_id = s.log_item_id
               AND u.log_code = 'Asset_Updated'
               AND u.log_description LIKE '%Unserviceable%'
               AND u.timestamp = (
                    SELECT MIN(u2.timestamp)
                    FROM asset_logs u2
                    WHERE u2.log_item_id = s.log_item_id
                      AND u2.log_code = 'Asset_Updated'
                      AND u2.log_description LIKE '%Unserviceable%'
                      AND u2.timestamp > s.timestamp
               )
            WHERE s.log_code = 'Asset_Updated'
              AND s.log_description LIKE '%Serviceable%'
              AND u.timestamp BETWEEN ? AND ?
              AND at.asset_id = ?
            ORDER BY ea.equipment_registration, s.timestamp
        ";

            $rows = $this->db->query($sqlBreakdown, [$startDate, $endDate, $r['type_id']])->result_array();

            $breakdowns = [];
            foreach ($rows as $row) {
                $diffSecs = (int) $row['diff_seconds'];
                if ($diffSecs <= 0) continue;

                $d = intdiv($diffSecs, 86400);
                $h = intdiv(($diffSecs % 86400), 3600);
                $m = intdiv(($diffSecs % 3600), 60);

                $breakdowns[] = [
                    'Asset Code'         => $row['asset_code'],
                    'Asset Name'         => $row['equipment_name'],
                    'Type'               => $row['type_name'],
                    'Serviceable Date'   => date('d-M-Y H:i', strtotime($row['service_ts'])),
                    'Unserviceable Date' => date('d-M-Y H:i', strtotime($row['unservice_ts'])),
                    'MTBF'               => "{$d}d {$h}h {$m}m",   // <-- combined format
                ];
            }

            // and for the summary result (no Cycles Counted, no separate d/h/m)
            $result[] = [
                'Type ID'      => (int)$r['type_id'],
                'Type'         => $r['type_name'],
                'Average MTBF' => "{$days}d {$hours}h {$minutes}m",
                'Breakdowns'   => $breakdowns,
            ];
        }

        return $result; // combined structure
    }

    private function componentdownloadRecord($year, $month)
    {
        $year        = $year ?: date('Y');
        $month_param = $month;

        // Date range
        if (!empty($month_param)) {
            $startDate = sprintf('%04d-%02d-01 00:00:00', $year, (int)$month_param);
            $endDate   = date('Y-m-t 23:59:59', strtotime("$year-$month_param-01"));
        } else {
            $startDate = "$year-01-01 00:00:00";
            $endDate   = "$year-12-31 23:59:59";
        }

        // ---- Summary (by component type)
        $sqlSummary = <<<SQL
        SELECT
            id AS type_id,
            item_type AS type_name,
            AVG(mtbf_seconds) AS avg_seconds
        FROM (
            SELECT
                it.id AS id,
                it.name AS item_type,
                TIMESTAMPDIFF(SECOND, l1.timestamp, MIN(l2.timestamp)) AS mtbf_seconds
            FROM asset_logs l1
            JOIN asset_logs l2
                ON l1.log_item_id = l2.log_item_id
                AND l2.timestamp > l1.timestamp
                AND l2.log_description LIKE '%→ ''UNSERVICEABLE''%'
                AND l2.log_code = 'Component_Updated'
                AND l2.timestamp BETWEEN ? AND ?
            JOIN add_asset_items items
                ON items.id = l1.log_item_id
            JOIN item_types it
                ON it.id = items.item_type_id
            WHERE l1.log_description LIKE '%→ ''SERVICEABLE''%'
              AND l1.log_code = 'Component_Updated'
              AND l1.timestamp BETWEEN ? AND ?
              AND NOT EXISTS (
                  SELECT 1
                  FROM asset_logs lx
                  WHERE lx.log_item_id = l1.log_item_id
                    AND lx.timestamp > l1.timestamp
                    AND lx.timestamp < l2.timestamp
                    AND lx.log_description LIKE '%→ ''SERVICEABLE''%'
                    AND lx.log_code = 'Component_Updated'
              )
            GROUP BY
                l1.log_item_id,
                l1.timestamp,
                it.name
        ) AS item_mtbfs
        GROUP BY
            item_type
        SQL;

        $summaryRows = $this->db->query($sqlSummary, [$startDate, $endDate, $startDate, $endDate])->result_array();

        $result = [];

        foreach ($summaryRows as $r) {
            $avgSecs = (int) round($r['avg_seconds']);
            $days    = intdiv($avgSecs, 86400);
            $hours   = intdiv(($avgSecs % 86400), 3600);
            $minutes = intdiv(($avgSecs % 3600), 60);

            // ---- Breakdown for this component type
            $sqlBreakdown = <<<SQL
            SELECT
                ai.serial_number,
                ai.item_name,
                it.name AS type_name,
                s.timestamp AS service_ts,
                u.timestamp AS unservice_ts,
                TIMESTAMPDIFF(SECOND, s.timestamp, u.timestamp) AS diff_seconds
            FROM asset_logs s
            JOIN add_asset_items ai
                ON ai.id = s.log_item_id
            JOIN item_types it
                ON it.id = ai.item_type_id
            JOIN asset_logs u
                ON u.log_item_id = s.log_item_id
               AND u.log_code = 'Component_Updated'
               AND u.log_description LIKE '%→ ''UNSERVICEABLE''%'
               AND u.timestamp = (
                    SELECT MIN(u2.timestamp)
                    FROM asset_logs u2
                    WHERE u2.log_item_id = s.log_item_id
                      AND u2.log_code = 'Component_Updated'
                      AND u2.log_description LIKE '%→ ''UNSERVICEABLE''%'
                      AND u2.timestamp > s.timestamp
               )
            WHERE s.log_code = 'Component_Updated'
              AND s.log_description LIKE '%→ ''SERVICEABLE''%'
              AND u.timestamp BETWEEN ? AND ?
              AND it.id = ?
            ORDER BY ai.serial_number, s.timestamp
        SQL;

            $rows = $this->db->query($sqlBreakdown, [$startDate, $endDate, $r['type_id']])->result_array();

            $breakdowns = [];
            foreach ($rows as $row) {
                $diffSecs = (int) $row['diff_seconds'];
                if ($diffSecs <= 0) continue;

                $d = intdiv($diffSecs, 86400);
                $h = intdiv(($diffSecs % 86400), 3600);
                $m = intdiv(($diffSecs % 3600), 60);

                $breakdowns[] = [
                    'Component Code'      => $row['serial_number'],
                    'Component Name'     => $row['item_name'],
                    'Type'               => $row['type_name'],
                    'Serviceable Date'   => date('d-M-Y H:i', strtotime($row['service_ts'])),
                    'Unserviceable Date' => date('d-M-Y H:i', strtotime($row['unservice_ts'])),
                    'MTBF'               => "{$d}d {$h}h {$m}m",
                ];
            }

            $result[] = [
                'Type ID'      => (int)$r['type_id'],
                'Type'         => $r['type_name'],
                'Average MTBF' => "{$days}d {$hours}h {$minutes}m",
                'Breakdowns'   => $breakdowns,
            ];
        }

        return $result;
    }

    /**
     * POST: download_type = 'pdf'|'excel', year, month
     */
    public function downloadReport()
    {
        $fileType = $this->input->post('download_type'); // pdf or excel
        $year     = $this->input->post('year') ?: date('Y');
        $month    = $this->input->post('month');         // e.g. '08' or empty

        log_message('debug', "Download request: type=$fileType, year=$year, month=$month");

        $reportData = $this->fetchSummaryWithBreakdown($year, $month);

        if (strtolower($fileType) === 'excel') {
            $this->generateExcel($reportData, $year, $month);
        } else {
            $this->generatePdf($reportData, $year, $month);
        }
    }

    public function downloadComponentReport()
    {
        $fileType = $this->input->post('download_type'); // pdf or excel
        $year     = $this->input->post('year') ?: date('Y');
        $month    = $this->input->post('month');         // e.g. '08' or empty

        log_message('debug', "Component report download: type=$fileType, year=$year, month=$month");

        // 🔑 use your new component method instead of asset one
        $reportData = $this->componentdownloadRecord($year, $month);

        if (strtolower($fileType) === 'excel') {
            $this->generateComponentExcel($reportData, $year, $month);
        } else {
            $this->generateComponentPdf($reportData, $year, $month);
        }
    }



    /**
     * Generate MTBF PDF Report from combined $reportData
     */
    private function generatePdf(array $reportData, string $year, ?string $month = null)
    {
        $this->load->library('pdf'); // application/libraries/Pdf.php

        // Slice combined structure into flat tables
        $summaryRows   = [];
        $breakdownRows = [];

        foreach ($reportData as $item) {
            $summaryRows[] = [
                'Type'         => $item['Type'],
                'Average MTBF' => $item['Average MTBF'],  // e.g. "2d 18h 49m"
            ];

            foreach ($item['Breakdowns'] as $br) {
                $breakdownRows[] = $br; // already shaped with Days/Hours/Minutes
            }
        }

        // Build HTML
        $period = $year . ($month ? " / {$month}" : " / ALL");
        $html = "
        <style>
            body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 11px; }
            h2 { margin: 0 0 10px 0; }
            h3 { margin: 18px 0 8px 0; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #777; padding: 6px; }
            th { background: #f0f0f0; text-align: left; }
        </style>
        <h2>MTBF Report – {$period}</h2>";

        // Summary
        $html .= "<h3>Summary (by Type)</h3>";
        $html .= "<table>
                <tr>
                <th>Type</th>
                <th>Average MTBF</th>
                </tr>";

        if (empty($summaryRows)) {
            $html .= "<tr><td colspan='4' style='text-align:center;'>No data found for the selected period.</td></tr>";
        } else {
            foreach ($summaryRows as $row) {
                $html .= "<tr>
                        <td>{$row['Type']}</td>
                        <td>{$row['Average MTBF']}</td>
                      </tr>";
            }
        }
        $html .= "</table>";

        // Breakdown
        $html .= "<h3 style='page-break-before: always;'>Breakdown (All MTBF)</h3>";
        $html .= "<table>
                <tr>
                    <th>Asset Code</th>
                    <th>Asset Name</th>
                    <th>Type</th>
                    <th>Serviceable Date</th>
                    <th>Unserviceable Date</th>
                    <th>MTBF</th>
                </tr>";

        if (empty($breakdownRows)) {
            $html .= "<tr><td colspan='8' style='text-align:center;'>No breakdown records for the selected period.</td></tr>";
        } else {
            foreach ($breakdownRows as $row) {
                $html .= "<tr>
                        <td>{$row['Asset Code']}</td>
                        <td>{$row['Asset Name']}</td>
                        <td>{$row['Type']}</td>
                        <td>{$row['Serviceable Date']}</td>
                        <td>{$row['Unserviceable Date']}</td>
                        <td>{$row['MTBF']}</td>
                      </tr>";
            }
        }
        $html .= "</table>";

        $filename = "MTBF_Report_{$year}_" . ($month ?: 'ALL');
        $this->pdf->createPDF($html, $filename, TRUE, 'A3', 'landscape');
    }

    private function generateComponentPdf(array $reportData, string $year, ?string $month = null)
    {
        $this->load->library('pdf'); // application/libraries/Pdf.php

        $summaryRows   = [];
        $breakdownRows = [];

        foreach ($reportData as $item) {
            $summaryRows[] = [
                'Type'         => $item['Type'],
                'Average MTBF' => $item['Average MTBF'],
            ];

            foreach ($item['Breakdowns'] as $br) {
                $breakdownRows[] = $br;
            }
        }

        // Build HTML
        $period = $year . ($month ? " / {$month}" : " / ALL");
        $html = "
        <style>
            body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 11px; }
            h2 { margin: 0 0 10px 0; }
            h3 { margin: 18px 0 8px 0; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #777; padding: 6px; }
            th { background: #f0f0f0; text-align: left; }
        </style>
        <h2>MTBF Component Report – {$period}</h2>";

        // Summary
        $html .= "<h3>Summary (by Component Type)</h3>";
        $html .= "<table>
                <tr>
                    <th>Component Type</th>
                    <th>Average MTBF</th>
                </tr>";

        if (empty($summaryRows)) {
            $html .= "<tr><td colspan='2' style='text-align:center;'>No data found for the selected period.</td></tr>";
        } else {
            foreach ($summaryRows as $row) {
                $html .= "<tr>
                        <td>{$row['Type']}</td>
                        <td>{$row['Average MTBF']}</td>
                      </tr>";
            }
        }
        $html .= "</table>";

        // Breakdown
        $html .= "<h3 style='page-break-before: always;'>Breakdown (All MTBF)</h3>";
        $html .= "<table>
                <tr>
                    <th>Component Code</th>
                    <th>Component Name</th>
                    <th>Type</th>
                    <th>Serviceable Date</th>
                    <th>Unserviceable Date</th>
                    <th>MTBF</th>
                </tr>";

        if (empty($breakdownRows)) {
            $html .= "<tr><td colspan='6' style='text-align:center;'>No breakdown records for the selected period.</td></tr>";
        } else {
            foreach ($breakdownRows as $row) {
                $html .= "<tr>
                        <td>{$row['Component Code']}</td>
                        <td>{$row['Component Name']}</td>
                        <td>{$row['Type']}</td>
                        <td>{$row['Serviceable Date']}</td>
                        <td>{$row['Unserviceable Date']}</td>
                        <td>{$row['MTBF']}</td>
                      </tr>";
            }
        }
        $html .= "</table>";

        $filename = "MTBF_Component_Report_{$year}_" . ($month ?: 'ALL');
        $this->pdf->createPDF($html, $filename, TRUE, 'A3', 'landscape');
    }



    /**
     * Generate MTBF Excel Report from combined $reportData
     * Sheet 1: Summary   Sheet 2: Breakdown
     */
    private function generateExcel(array $reportData, string $year, ?string $month = null)
    {
        // Flatten data
        $summaryRows   = [];
        $breakdownRows = [];

        foreach ($reportData as $item) {
            $summaryRows[] = [
                $item['Type'],
                $item['Average MTBF'],
            ];
            foreach ($item['Breakdowns'] as $br) {
                $breakdownRows[] = [
                    $br['Asset Code'],
                    $br['Asset Name'],
                    $br['Type'],
                    $br['Serviceable Date'],
                    $br['Unserviceable Date'],
                    $br['MTBF'],
                ];
            }
        }

        // Build workbook
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Sheet 1: Summary
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary');
        $sheet->fromArray(['Type', 'Average MTBF'], NULL, 'A1');
        if (!empty($summaryRows)) {
            $sheet->fromArray($summaryRows, NULL, 'A2');
        }

        // Sheet 2: Breakdown
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Breakdown');
        $sheet2->fromArray(['Asset Code', 'Asset Name', 'Type', 'Serviceable Date', 'Unserviceable Date', 'MTBF'], NULL, 'A1');
        if (!empty($breakdownRows)) {
            $sheet2->fromArray($breakdownRows, NULL, 'A2');
        }

        // Autosize columns (optional but nice)
        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $fname = "MTBF_Report_{$year}_" . ($month ?: 'ALL') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$fname}\"");
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function generateComponentExcel(array $reportData, string $year, ?string $month = null)
    {
        // Flatten data
        $summaryRows   = [];
        $breakdownRows = [];

        foreach ($reportData as $item) {
            $summaryRows[] = [
                $item['Type'],
                $item['Average MTBF'],
            ];
            foreach ($item['Breakdowns'] as $br) {
                $breakdownRows[] = [
                    $br['Component Code'],
                    $br['Component Name'],
                    $br['Type'],
                    $br['Serviceable Date'],
                    $br['Unserviceable Date'],
                    $br['MTBF'],
                ];
            }
        }

        // Build workbook
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Sheet 1: Summary
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary');
        $sheet->fromArray(['Component Type', 'Average MTBF'], NULL, 'A1');
        if (!empty($summaryRows)) {
            $sheet->fromArray($summaryRows, NULL, 'A2');
        }

        // Sheet 2: Breakdown
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Breakdown');
        $sheet2->fromArray(
            ['Component Code', 'Component Name', 'Type', 'Serviceable Date', 'Unserviceable Date', 'MTBF'],
            NULL,
            'A1'
        );
        if (!empty($breakdownRows)) {
            $sheet2->fromArray($breakdownRows, NULL, 'A2');
        }

        // Autosize columns (optional)
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $fname = "MTBF_Component_Report_{$year}_" . ($month ?: 'ALL') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$fname}\"");
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }




    public function getBreakdownByType()
    {
        $type = $this->input->get('type');
        $year = $this->input->get('year') ?: date('Y');
        $month_param = $this->input->get('month');

        // Date range
        if (!empty($month_param)) {
            $startDate = sprintf('%04d-%02d-01 00:00:00', $year, (int)$month_param);
            $endDate   = date('Y-m-t 23:59:59', strtotime("$year-$month_param-01"));
        } else {
            $startDate = "$year-01-01 00:00:00";
            $endDate   = "$year-12-31 23:59:59";
        }

        // Pair each Serviceable with the NEXT Unserviceable for the selected type
        $sql = <<<SQL
            SELECT
                ea.equipment_registration AS asset_code,
                ea.equipment_name,
                ats.name                  AS type_name,
                s.timestamp               AS service_ts,
                u.timestamp               AS unservice_ts,
                TIMESTAMPDIFF(SECOND, s.timestamp, u.timestamp) AS diff_seconds
            FROM asset_logs s
            JOIN equipments_asset ea
                ON ea.equipment_id = s.log_item_id
            JOIN asset_types ats
                ON ats.asset_id = ea.equipment_type
            JOIN asset_logs u
                ON u.log_item_id = s.log_item_id
            AND u.log_code = 'Asset_Updated'
            AND u.log_description LIKE "%→ 'UNSERVICEABLE'%"
            AND u.timestamp = (
                SELECT MIN(u2.timestamp)
                FROM asset_logs u2
                WHERE u2.log_item_id = s.log_item_id
                    AND u2.log_code = 'Asset_Updated'
                    AND u2.log_description LIKE "%→ 'UNSERVICEABLE'%"
                    AND u2.timestamp > s.timestamp
            )
            WHERE s.log_code = 'Asset_Updated'
            AND s.log_description LIKE "%→ 'SERVICEABLE'%"
            AND u.timestamp BETWEEN ? AND ?
            AND ats.name = ?
            ORDER BY ea.equipment_registration, s.timestamp
            SQL;


        $query = $this->db->query($sql, [$startDate, $endDate, $type]);
        $rows  = $query->result_array();

        $data = [];
        foreach ($rows as $r) {
            $diffSecs = (int) $r['diff_seconds'];
            if ($diffSecs <= 0) continue; // skip invalid cycles

            $days    = intdiv($diffSecs, 86400);
            $hours   = intdiv(($diffSecs % 86400), 3600);
            $minutes = intdiv(($diffSecs % 3600), 60);


            $data[] = [
                'Asset Code'         => $r['asset_code'],
                'Asset Name'         => $r['equipment_name'],
                'Type'               => $r['type_name'],
                'Serviceable Date'   => date('d-M-Y H:i', strtotime($r['service_ts'])),
                'Unserviceable Date' => date('d-M-Y H:i', strtotime($r['unservice_ts'])),
                'MTBF (Days)' => "{$days}d {$hours}h {$minutes}m"

            ];
        }

        die(json_encode([
            "data" => $data
        ]));
    }

    public function getSummaryForAll()
    {
        $currentTab = null;
        $is_filter_active = false;

        // get specific selected orders based on status
        if ((empty($_GET['summary']) || $_GET['summary'] == 'assets')) {

            $currentTab = 'assets';
        } elseif (isset($_GET['summary']) && $_GET['summary'] == 'components') {

            $currentTab = 'components';
        }

        if ($currentTab == 'assets') {
            $year        = $this->input->get('year') ?: date('Y');
            $month_param = $this->input->get('month');

            // Date range (year, or a specific month within that year)
            if (!empty($month_param)) {
                $startDate = sprintf('%04d-%02d-01 00:00:00', $year, (int)$month_param);
                $endDate   = date('Y-m-t 23:59:59', strtotime("$year-$month_param-01"));
            } else {
                $startDate = "$year-01-01 00:00:00";
                if ($year < date('Y')) {
                    $endDate = "$year-12-31 23:59:59";
                } elseif ($year == date('Y')) {
                    // up to current month end
                    $endDate = date('Y-m-t 23:59:59', strtotime(date('Y-m-01')));
                } else {
                    $endDate = "$year-12-31 23:59:59";
                }
            }

            // Raw SQL with correlated subquery: for each SERVICEABLE (s) find the next MIN Unserviceable (u) after it
            $sql = <<<SQL
                SELECT ats.name AS type_name,
                    AVG(TIMESTAMPDIFF(SECOND, s.timestamp, u.timestamp)) AS avg_seconds,
                    COUNT(*) AS cycle_count
                FROM asset_logs s
                JOIN equipments_asset ea ON ea.equipment_id = s.log_item_id
                JOIN asset_types ats ON ats.asset_id = ea.equipment_type
                JOIN asset_logs u ON u.log_item_id = s.log_item_id
                    AND u.log_code = 'Asset_Updated'
                    AND u.log_description LIKE "%→ 'UNSERVICEABLE'%"
                    AND u.timestamp = (
                        SELECT MIN(u2.timestamp)
                        FROM asset_logs u2
                        WHERE u2.log_item_id = s.log_item_id
                        AND u2.log_code = 'Asset_Updated'
                        AND u2.log_description LIKE "%→ 'UNSERVICEABLE'%"
                        AND u2.timestamp > s.timestamp
                    )
                WHERE s.log_code = 'Asset_Updated'
                AND s.log_description LIKE "%→ 'SERVICEABLE'%"
                AND u.timestamp BETWEEN ? AND ?
                AND NOT EXISTS (
                    SELECT 1
                    FROM asset_logs lx
                    WHERE lx.log_item_id = s.log_item_id
                        AND lx.timestamp > s.timestamp
                        AND lx.timestamp < u.timestamp
                        AND lx.log_code = 'Asset_Updated'
                        AND lx.log_description LIKE "%→ 'SERVICEABLE'%"
                )
                GROUP BY ats.name
                ORDER BY ats.name
                SQL;


            $query = $this->db->query($sql, [$startDate, $endDate]);
            $rows  = $query->result_array();

            // Format data for DataTables
            $data = [];
            foreach ($rows as $r) {
                $avgSecs = (int) round($r['avg_seconds']);

                $days    = intdiv($avgSecs, 86400);
                $remainderAfterDays = $avgSecs % 86400;

                $hours   = intdiv($remainderAfterDays, 3600);
                $remainderAfterHours = $remainderAfterDays % 3600;

                $minutes = intdiv($remainderAfterHours, 60);

                $data[] = [
                    'Type'                 => $r['type_name'],
                    'Average MTBF (Days)'         => "{$days}d {$hours}h {$minutes}m",

                ];
            }


            $count = count($data);
            $draw  = (int) ($this->input->post('draw') ?? 1);

            die(json_encode([
                "draw"            => $draw,
                "recordsTotal"    => $count,
                "recordsFiltered" => $count,
                "data"            => $data
            ]));
        } elseif ($currentTab == 'components') {

            $year       = $this->input->get('year') ?: date('Y');
            $month_param = $this->input->get('month');

            // Date range (year, or a specific month within that year)
            if (!empty($month_param)) {
                $startDate = sprintf('%04d-%02d-01 00:00:00', $year, (int)$month_param);
                $endDate   = date('Y-m-t 23:59:59', strtotime("$year-$month_param-01"));
            } else {
                $startDate = "$year-01-01 00:00:00";
                if ($year < date('Y')) {
                    $endDate = "$year-12-31 23:59:59";
                } elseif ($year == date('Y')) {
                    // up to current month end
                    $endDate = date('Y-m-t 23:59:59', strtotime(date('Y-m-01')));
                } else {
                    $endDate = "$year-12-31 23:59:59";
                }
            }

            // Raw SQL to calculate average MTBF for each item type
            $sql = <<<SQL
                SELECT
                    id,
                    item_type,
                    AVG(mtbf_seconds) AS avg_seconds
                FROM (
                    SELECT
                        it.id AS id,
                        it.name AS item_type,
                        TIMESTAMPDIFF(SECOND, l1.timestamp, MIN(l2.timestamp)) AS mtbf_seconds
                    FROM asset_logs l1
                    JOIN asset_logs l2
                        ON l1.log_item_id = l2.log_item_id
                        AND l2.timestamp > l1.timestamp
                        AND l2.log_description LIKE '%→ ''UNSERVICEABLE''%'
                        AND l2.log_code = 'Component_Updated'
                        AND l2.timestamp BETWEEN ? AND ?   -- ✅ filter applied here
                    JOIN add_asset_items items
                        ON items.id = l1.log_item_id
                    JOIN item_types it
                        ON it.id = items.item_type_id
                    WHERE l1.log_description LIKE '%→ ''SERVICEABLE''%'
                    AND l1.log_code = 'Component_Updated'
                    AND l1.timestamp BETWEEN ? AND ?     -- ✅ also filter serviceable side
                    AND NOT EXISTS (
                        SELECT 1
                        FROM asset_logs lx
                        WHERE lx.log_item_id = l1.log_item_id
                            AND lx.timestamp > l1.timestamp
                            AND lx.timestamp < l2.timestamp
                            AND lx.log_description LIKE '%→ ''SERVICEABLE''%'
                            AND lx.log_code = 'Component_Updated'
                    )
                    GROUP BY
                        l1.log_item_id,
                        l1.timestamp,
                        it.name
                ) AS item_mtbfs
                GROUP BY
                    item_type
            SQL;


            $query = $this->db->query($sql, [$startDate, $endDate, $startDate, $endDate]);
            $rows  = $query->result_array();

            // Format data for DataTables
            $data = [];
            foreach ($rows as $r) {
                $avgSecs = (int) round($r['avg_seconds']);

                $days = intdiv($avgSecs, 86400);
                $remainderAfterDays = $avgSecs % 86400;

                $hours = intdiv($remainderAfterDays, 3600);
                $remainderAfterHours = $remainderAfterDays % 3600;

                $minutes = intdiv($remainderAfterHours, 60);

                $data[] = [
                    'id'       => $r['id'],
                    'Type'          => $r['item_type'],
                    'Average MTBF'  => "{$days}d {$hours}h {$minutes}m",
                ];
            }

            $count = count($data);
            $draw  = (int) ($this->input->post('draw') ?? 1);

            die(json_encode([
                "draw"            => $draw,
                "recordsTotal"    => $count,
                "recordsFiltered" => $count,
                "data"            => $data
            ]));
        }
    }


    public function getBreakdownByTypeComponents()
    {
        $type = $this->input->get('type');
        $year = $this->input->get('year') ?: date('Y');
        $month_param = $this->input->get('month');

        // Date range (year, or a specific month within that year)
        if (!empty($month_param)) {
            $startDate = sprintf('%04d-%02d-01 00:00:00', $year, (int)$month_param);
            $endDate   = date('Y-m-t 23:59:59', strtotime("$year-$month_param-01"));
        } else {
            $startDate = "$year-01-01 00:00:00";
            if ($year < date('Y')) {
                $endDate = "$year-12-31 23:59:59";
            } elseif ($year == date('Y')) {
                // up to current month end
                $endDate = date('Y-m-t 23:59:59', strtotime(date('Y-m-01')));
            } else {
                $endDate = "$year-12-31 23:59:59";
            }
        }

        // Pair each Serviceable with the NEXT Unserviceable for the selected component type
        $sql = "
                SELECT
                ai.serial_number,
                ai.item_name,
                it.name AS type_name,
                s.timestamp AS service_ts,
                u.timestamp AS unservice_ts,
                TIMESTAMPDIFF(SECOND, s.timestamp, u.timestamp) AS diff_seconds
            FROM asset_logs s
            JOIN add_asset_items ai
                ON ai.id = s.log_item_id
            JOIN item_types it
                ON it.id = ai.item_type_id
            JOIN asset_logs u
                ON u.log_item_id = s.log_item_id
            AND u.log_code = 'Component_Updated'
            AND u.log_description LIKE '%→ ''UNSERVICEABLE''%'
            AND u.timestamp = (
                SELECT MIN(u2.timestamp)
                FROM asset_logs u2
                WHERE u2.log_item_id = s.log_item_id
                    AND u2.log_code = 'Component_Updated'
                    AND u2.log_description LIKE '%→ ''UNSERVICEABLE''%'
                    AND u2.timestamp > s.timestamp
            )
            WHERE s.log_code = 'Component_Updated'
            AND s.log_description LIKE '%→ ''SERVICEABLE''%'
            AND u.timestamp BETWEEN ? AND ?
            AND it.id = ?
            ORDER BY ai.serial_number, s.timestamp
                ";

        $query = $this->db->query($sql, [$startDate, $endDate, $type]);
        $rows  = $query->result_array();

        $data = [];
        foreach ($rows as $r) {
            $diffSecs = (int) $r['diff_seconds'];
            if ($diffSecs <= 0 || is_null($r['unservice_ts'])) continue;

            $days    = intdiv($diffSecs, 86400);
            $hours   = intdiv(($diffSecs % 86400), 3600);
            $minutes = intdiv(($diffSecs % 3600), 60);

            $data[] = [
                'component_code'         => $r['serial_number'],
                'component_name'         => $r['item_name'],
                'Type'               => $r['type_name'],
                'Serviceable_Date'   => date('d-M-Y H:i', strtotime($r['service_ts'])),
                'Unserviceable_Date' => date('d-M-Y H:i', strtotime($r['unservice_ts'])),
                'MTBF (Days)'        => "{$days}d {$hours}h {$minutes}m"
            ];
        }

        die(json_encode([
            "data" => $data
        ]));
    }
}
