(function ($) {
    'use strict';

    var alerts = [];
    var total = 0;
    var loading = false;
    var $button;
    var $badge;
    var $dropdown;
    var $list;
    var $empty;
    var $modal;

    function endpoint(path) {
        return typeof window.appUrl === 'function' ? window.appUrl(path) : path;
    }

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    }

    function setText(selector, value) {
        $modal.find(selector).text(value || 'N/A');
    }

    function renderAlerts() {
        $list.empty();

        if (!alerts.length) {
            $badge.addClass('is-hidden').text('0');
            $empty.text('No maintenance alerts right now.').show();
            return;
        }

        $badge.removeClass('is-hidden').text(total > 99 ? '99+' : total);
        $empty.hide();

        alerts.forEach(function (alert, index) {
            var item = [
                '<button type="button" class="maintenance-alert-item" data-alert-index="' + index + '">',
                '  <span class="maintenance-alert-item-title">',
                '    <span>' + escapeHtml(alert.title) + '</span>',
                '    <span class="maintenance-alert-pill ' + escapeHtml(alert.type) + '">' + escapeHtml(alert.status_label) + '</span>',
                '  </span>',
                '  <span class="maintenance-alert-item-meta">' + escapeHtml(alert.subtitle) + ' · ' + escapeHtml(alert.days_text) + '</span>',
                '</button>'
            ].join('');

            $list.append(item);
        });
    }

    function loadAlerts() {
        if (loading) { return; }
        loading = true;
        $.getJSON(endpoint('/maintenance_alerts/list'))
            .done(function (response) {
                if (!response || response.success !== true || !Array.isArray(response.alerts)) {
                    showError();
                    return;
                }
                alerts = response.alerts;
                total = response.count;
                renderAlerts();
                $button.attr('aria-label', 'Maintenance alerts: ' + total);
                $button.toggleClass('has-overdue', alerts.some(function (alert) { return alert.type === 'overdue'; }));
            })
            .fail(showError)
            .always(function () { loading = false; });
    }

    function showError() {
        alerts = [];
        $list.empty();
        $badge.addClass('is-hidden');
        $button.removeClass('has-overdue').attr('aria-label', 'Maintenance alerts unavailable');
        $empty.text('Unable to load alerts. Reopen to retry.').show();
    }

    function closeDropdown() {
        $dropdown.removeClass('is-open');
        $button.attr('aria-expanded', 'false');
    }

    function openAlert(index) {
        var alert = alerts[index];
        if (!alert) {
            return;
        }

        setText('[data-alert-field="title"]', alert.title);
        setText('[data-alert-field="status"]', alert.status_label);
        setText('[data-alert-field="due_date"]', alert.due_date);
        setText('[data-alert-field="days_text"]', alert.days_text);
        setText('[data-alert-field="asset_type"]', alert.asset_type);
        setText('[data-alert-field="location"]', alert.location);
        setText('[data-alert-field="subtitle"]', alert.subtitle);
        setText('[data-alert-field="maintenance_type"]', alert.maintenance_type);
        setText('[data-alert-field="work_status"]', alert.work_status);
        setText('[data-alert-field="remarks"]', alert.remarks || 'No remarks recorded.');
        var $tasks = $modal.find('[data-alert-field="tasks"]').empty();
        (alert.tasks || []).forEach(function (task) {
            var $item = $('<li>');
            $('<strong>').text(task.name).appendTo($item);
            if (task.status) { $('<div>').text(task.status).appendTo($item); }
            if (task.remarks) { $('<div>').text(task.remarks).appendTo($item); }
            $tasks.append($item);
        });
        if (!$tasks.children().length) { $('<li>').text('No tasks recorded.').appendTo($tasks); }
        $modal.find('[data-alert-action="details"]').attr('href', alert.detail_url || '#')
            .text(alert.entity_type === 'component' ? 'Go To Component Detail' : 'Go To Asset Detail');
        closeDropdown();
        $modal.modal('show');
    }

    $(function () {
        $button = $('#maintenanceAlertButton');
        $badge = $('#maintenanceAlertBadge');
        $dropdown = $('#maintenanceAlertDropdown');
        $list = $('#maintenanceAlertList');
        $empty = $('#maintenanceAlertEmpty');
        $modal = $('#maintenanceAlertModal');

        if (!$button.length) {
            return;
        }

        $button.on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $dropdown.toggleClass('is-open');
            $button.attr('aria-expanded', String($dropdown.hasClass('is-open')));
            if ($dropdown.hasClass('is-open')) { loadAlerts(); }
        });

        $list.on('click', '.maintenance-alert-item', function () {
            openAlert($(this).data('alert-index'));
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest('.maintenance-alert-nav').length) {
                closeDropdown();
            }
        });

        $(document).on('keydown', function (event) {
            if (event.key === 'Escape' && $dropdown.hasClass('is-open')) {
                closeDropdown();
                $button.trigger('focus');
            }
        });
        $modal.on('hidden.bs.modal', function () { $button.trigger('focus'); });

        loadAlerts();
        window.setInterval(loadAlerts, 300000);
    });
})(jQuery);
