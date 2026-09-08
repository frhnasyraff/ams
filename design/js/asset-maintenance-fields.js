/* Asset maintenance fields have one owner; type lookups never erase saved/user-entered values. */
(function (root, factory) {
    const api = factory();
    if (typeof module === 'object' && module.exports) module.exports = api;
    if (root) {
        root.amsMaintenance = api;
        if (root.jQuery) root.jQuery(function () { api.init(root.jQuery); });
    }
})(typeof window !== 'undefined' ? window : null, function () {
    function stateFor(settings, values, hasType) {
        settings = settings || {};
        const flag = settings.maintenance == null || settings.maintenance === '' ? null : Number(settings.maintenance);
        const savedDate = !!values.date;
        return {
            show: savedDate || (hasType && flag !== 0),
            message: !hasType ? (savedDate ? 'Saved maintenance date retained. Select an asset type.' : '') :
                flag === null ? 'Maintenance is not configured for this Asset Type. Set Check For Maintenance in Asset Types. Existing dates are retained.' :
                flag === 0 && savedDate ? 'Maintenance is disabled for this Asset Type. The saved date is retained for review.' : '',
            frequency: values.frequency === '' && flag === 1 ? (settings.maintenance_frequency_year ?? '') : values.frequency,
            reminder: values.reminder === '' && flag === 1 ? (settings.maintenance_reminder_days ?? '') : values.reminder
        };
    }
    function matches(response, selectedId) {
        return response && typeof response === 'object' && !Array.isArray(response) &&
            response.status !== false && String(response.asset_id) === String(selectedId);
    }
    let $;
    function selectElement(select) { return $(select); }
    function fields(select) {
        const form = select.closest('form');
        return {
            form: form,
            date: form.find('input[name="maintenance_date"]'),
            frequency: form.find('input[name="frequency_year"]'),
            reminder: form.find('input[name="maintenance_reminder_day"]')
        };
    }
    function message(select, className) {
        let box = select.closest('.form-group').find('.' + className);
        if (!box.length) {
            box = $('<div>').addClass(className + ' small mt-2').attr('role', 'status');
            select.closest('.form-group').append(box);
        }
        return box;
    }
    function apply(select, response) {
        if (!$) return;
        select = selectElement(select);
        if (!select.length) return;
        if (response && !matches(response, select.val())) return;
        const option = select.find(':selected');
        const settings = response || {
            maintenance: option.attr('data-maintenance'),
            maintenance_frequency_year: option.attr('data-maintenance-frequency'),
            maintenance_reminder_days: option.attr('data-maintenance-reminder')
        };
        const f = fields(select);
        const state = stateFor(settings, {
            date: f.date.val() || '',
            frequency: f.frequency.val() ?? '',
            reminder: f.reminder.val() ?? ''
        }, !!select.val());
        f.date.add(f.frequency).add(f.reminder).closest('.form-group').toggle(state.show);
        if (f.frequency.val() === '') f.frequency.val(state.frequency);
        if (f.reminder.val() === '') f.reminder.val(state.reminder);
        const warnings = response && Array.isArray(response.warnings) ? response.warnings.join(' ') : '';
        const note = [state.message, warnings].filter(Boolean).join(' ');
        message(select, 'ams-maintenance-note').text(note).toggle(!!note);
        if (response) message(select, 'ams-maintenance-error').empty().hide();
    }
    function failed(select, xhr) {
        if (!$) return;
        select = selectElement(select);
        const box = message(select, 'ams-maintenance-error').empty().show().attr('role', 'alert');
        const error = xhr && xhr.responseJSON && xhr.responseJSON.error;
        $('<span>').text(error || 'Unable to load Asset Type settings. Existing maintenance values are retained.').appendTo(box);
        $('<button type="button">').addClass('btn btn-sm btn-outline-info mt-2').text('Retry settings')
            .on('click', function () {
                const id = select.val();
                const button = $(this).prop('disabled', true);
                $.ajax({
                    url: window.amsUrl('/assettypes/asset_calibration'),
                    method: 'POST', dataType: 'json', timeout: 12000, data: {asset_id: id}
                }).done(function (response) {
                    if (String(select.val()) !== String(id)) return;
                    if (matches(response, id)) apply(select, response);
                    else failed(select);
                }).fail(function (xhr) {
                    if (String(select.val()) === String(id)) failed(select, xhr);
                }).always(function () { button.prop('disabled', false); });
            }).appendTo(box);
    }
    function init(jquery) {
        $ = jquery;
        const selector = '#equipment_type_calibration, #equipment_type_calibration_edit';
        $(selector).each(function () { apply(this); });
        $(document).on('change.amsMaintenance', selector, function () {
            message($(this), 'ams-maintenance-error').empty().hide();
            apply(this);
        });
    }
    return {stateFor: stateFor, matches: matches, apply: apply, failed: failed, init: init};
});
