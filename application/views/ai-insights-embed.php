<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AI Insights</title>
  <link href="<?= site_url('design/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
  <link href="<?= site_url('design/css/ai-insights.css?v=3'); ?>" rel="stylesheet">
  <style>
    html, body { margin: 0; min-height: 100%; background: #071426; overflow-x: hidden; }
    body { padding: 10px; }
    .ai-insights__layout { grid-template-columns: 1fr; }
    .ai-insights__messages { max-height: 300px; }
    .ai-insights__chart-body { min-height: 220px; }
    .ai-insights__chart canvas { height: 220px !important; }
  </style>
</head>
<body>
  <?php $this->load->view('ai-insights', ['query_url' => $query_url, 'status_url' => $status_url, 'embed' => true]); ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="<?= site_url('design/js/ai-insights.js?v=3'); ?>"></script>
</body>
</html>

