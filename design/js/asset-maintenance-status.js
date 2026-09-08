(function (root, factory) {
    var render = factory();
    if (typeof module === 'object' && module.exports) module.exports = render;
    if (root) root.amsMaintenanceStatus = render;
})(typeof window !== 'undefined' ? window : null, function () {
    return function (value, type) {
        var key = value == null ? '' : String(value).trim().toLowerCase().replace(/\s+/g, '_');
        var states = {
            complete: ['Complete', 'complete'],
            in_progress: ['In Progress', 'progress'],
            pending: ['Pending', 'pending']
        };
        var state = Object.prototype.hasOwnProperty.call(states, key) ? states[key] :
            (!key || key === 'n/a' ? ['Not set', 'unset'] : ['Unknown status', 'unset']);
        if (type && type !== 'display') return state[0];
        return '<span class="ams-maintenance-status ams-maintenance-status--' + state[1] + '">' + state[0] + '</span>';
    };
});
