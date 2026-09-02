<?php
$summary = $summary ?? ['total' => 0, 'today' => 0, 'users' => 0, 'modules' => 0];
$modules = $modules ?? [];
$activities = $activities ?? [];

$auditLabel = static function ($value) {
    return ucwords(strtolower(str_replace(['_', '-'], ' ', (string) $value)));
};
?>

<section class="audit-log-page" aria-labelledby="audit-log-title">
  <header class="audit-log-hero">
    <div class="audit-log-hero__copy">
      <span class="audit-log-hero__icon"><i class="fas fa-fingerprint"></i></span>
      <div>
        <span class="audit-log-eyebrow">System governance</span>
        <h2 id="audit-log-title">System Audit Log</h2>
        <p>Review who changed what, when it happened and which record was affected.</p>
      </div>
    </div>
    <span class="audit-log-live"><i></i> Live activity history</span>
  </header>

  <div class="audit-log-summary-grid" aria-label="Audit activity summary">
    <article class="audit-log-summary audit-log-summary--blue">
      <span><i class="fas fa-stream"></i></span>
      <div><small>Total Events</small><strong id="audit-total-events"><?= intval($summary['total']); ?></strong><p>All recorded activity</p></div>
    </article>
    <article class="audit-log-summary audit-log-summary--green">
      <span><i class="fas fa-calendar-day"></i></span>
      <div><small>Today</small><strong id="audit-today-events"><?= intval($summary['today']); ?></strong><p>Events recorded today</p></div>
    </article>
    <article class="audit-log-summary audit-log-summary--violet">
      <span><i class="fas fa-user-clock"></i></span>
      <div><small>Users Tracked</small><strong id="audit-users-count"><?= intval($summary['users']); ?></strong><p>Unique user accounts</p></div>
    </article>
    <article class="audit-log-summary audit-log-summary--amber">
      <span><i class="fas fa-cubes"></i></span>
      <div><small>Modules</small><strong id="audit-modules-count"><?= intval($summary['modules']); ?></strong><p>System areas monitored</p></div>
    </article>
  </div>

  <article class="audit-log-card">
    <div class="audit-log-card__head">
      <div>
        <span class="audit-log-eyebrow">Activity timeline</span>
        <h3>Recorded Events</h3>
        <p>Use the filters to narrow the timeline, then select View to inspect an event.</p>
      </div>
      <button type="button" class="audit-log-reset" id="audit-log-reset"><i class="fas fa-undo-alt"></i><span>Reset Filters</span></button>
    </div>

    <div class="audit-log-filters" aria-label="Audit log filters">
      <label>
        <span><i class="fas fa-layer-group"></i> Module</span>
        <select id="audit-module-filter">
          <option value="">All modules</option>
          <?php foreach ($modules as $module) { ?>
            <option value="<?= htmlspecialchars($module->log_item_table, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($auditLabel($module->log_item_table), ENT_QUOTES, 'UTF-8'); ?></option>
          <?php } ?>
        </select>
      </label>
      <label>
        <span><i class="fas fa-bolt"></i> Activity</span>
        <select id="audit-activity-filter">
          <option value="">All activities</option>
          <?php foreach ($activities as $activity) { ?>
            <option value="<?= htmlspecialchars($activity->log_code, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($auditLabel($activity->log_code), ENT_QUOTES, 'UTF-8'); ?></option>
          <?php } ?>
        </select>
      </label>
      <label>
        <span><i class="far fa-clock"></i> Period</span>
        <select id="audit-period-filter">
          <option value="">All time</option>
          <option value="today">Today</option>
          <option value="7days">Last 7 days</option>
          <option value="30days">Last 30 days</option>
        </select>
      </label>
    </div>

    <div class="audit-log-error" id="audit-log-error" hidden>
      <span><i class="fas fa-exclamation-triangle"></i></span>
      <div><strong>Unable to load activity</strong><p>The audit data could not be retrieved. Please try again.</p></div>
      <button type="button" id="audit-log-retry"><i class="fas fa-redo-alt"></i><span>Retry</span></button>
    </div>

    <div class="audit-log-table-wrap">
      <table class="table audit-log-table" id="logs" data-source="<?= site_url('log_viewer/ajax_list'); ?>" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Date &amp; Time</th>
            <th>User</th>
            <th>Record</th>
            <th>Activity</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </article>
</section>
