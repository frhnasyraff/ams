(function ($) {
    'use strict';

    var pages = {
        assettypescolors: {
            title: 'Asset Type Colors', category: 'Asset & Component', icon: 'fa-palette',
            description: 'Keep asset categories visually consistent across dashboards and reports.',
            listTitle: 'Configured Asset Colors', listDescription: 'Review and maintain the color assigned to each asset type.', action: 'Add Color'
        },
        task: {
            title: 'Task Types', category: 'Maintenance & Vendor', icon: 'fa-clipboard-check',
            description: 'Create the standard task names used by maintenance workflows.',
            listTitle: 'Task Type Directory', listDescription: 'Reusable task labels available throughout the system.', action: 'Add Task Type'
        },
        task_list: {
            title: 'Maintenance Tasks', category: 'Maintenance & Vendor', icon: 'fa-calendar-check',
            description: 'Define recurring maintenance tasks and their service frequency.',
            listTitle: 'Scheduled Task Library', listDescription: 'Manage task names and their default frequency in days.', action: 'Add Task'
        },
        vendorpartnumber: {
            title: 'Vendor Part Numbers', category: 'Maintenance & Vendor', icon: 'fa-barcode',
            description: 'Maintain the vendor references used to identify parts accurately.',
            listTitle: 'Part Number Directory', listDescription: 'Search, edit or remove registered vendor part numbers.', action: 'Add Part Number'
        },
        vendormanufacturingnumber: {
            title: 'Manufacturer Numbers', category: 'Maintenance & Vendor', icon: 'fa-industry',
            description: 'Manage manufacturer names and reference numbers used in asset records.',
            listTitle: 'Manufacturer Directory', listDescription: 'Standard manufacturer references for consistent data entry.', action: 'Add Manufacturer'
        },
        vendormanufacturingdrawingnumber: {
            title: 'Drawing Numbers', category: 'Maintenance & Vendor', icon: 'fa-drafting-compass',
            description: 'Organise technical drawing references for assets and components.',
            listTitle: 'Drawing Reference Directory', listDescription: 'Maintain searchable manufacturer drawing numbers.', action: 'Add Drawing Number'
        },
        managedbyadddata: {
            title: 'Managed By', category: 'Maintenance & Vendor', icon: 'fa-user-cog',
            description: 'Define the teams or parties responsible for managing equipment.',
            listTitle: 'Management Responsibility', listDescription: 'Reusable ownership labels for assets and maintenance records.', action: 'Add Managed By'
        },
        maintenancetypecolorcode: {
            title: 'Maintenance Colors', category: 'Maintenance & Vendor', icon: 'fa-tools',
            description: 'Use distinct colors to make maintenance types easier to recognise.',
            listTitle: 'Maintenance Type Colors', listDescription: 'Review the label and color used for each maintenance category.', action: 'Add Maintenance Color'
        },
        faulttypecolorcode: {
            title: 'Fault Colors', category: 'Maintenance & Vendor', icon: 'fa-exclamation-triangle',
            description: 'Apply clear visual indicators to different fault categories.',
            listTitle: 'Fault Type Colors', listDescription: 'Maintain the label and color assigned to each fault type.', action: 'Add Fault Color'
        },
        states: {
            title: 'States', category: 'Location & Lifecycle', icon: 'fa-map',
            description: 'Maintain the states used when organising physical asset locations.',
            listTitle: 'State Directory', listDescription: 'Add or update the states available to location records.', action: 'Add State'
        },
        locations: {
            title: 'Locations', category: 'Location & Lifecycle', icon: 'fa-map-marker-alt',
            description: 'Manage operational locations, map coordinates and state assignments.',
            listTitle: 'Location Directory', listDescription: 'Filter by state or search for a specific operating location.', action: 'Add Location'
        },
        item_type: {
            title: 'Component Types', category: 'Asset & Component', icon: 'fa-cubes',
            description: 'Define reusable component categories and their technical references.',
            listTitle: 'Component Type Directory', listDescription: 'Manage component classifications used by inventory records.', action: 'Add Component Type'
        },
        dashboardstatuscolor: {
            title: 'Dashboard Colors', category: 'System Appearance', icon: 'fa-fill-drip',
            description: 'Control the status colors used across dashboard visualisations.',
            listTitle: 'Status Color Palette', listDescription: 'Keep operational statuses easy to distinguish at a glance.', action: 'Add Status Color'
        },
        logoimage: {
            title: 'Logo Image', category: 'System Appearance', icon: 'fa-image',
            description: 'Manage the organisation image displayed in the application header.',
            listTitle: 'Current Logo', listDescription: 'Preview the active image or upload a replacement.', action: 'Upload Image', hint: false
        },
        asset_groups: {
            title: 'Asset Groups', category: 'Asset & Component', icon: 'fa-layer-group',
            description: 'Group related assets under clear codes and names.',
            listTitle: 'Asset Group Directory', listDescription: 'Open a group to review or update its configuration.', action: 'Add Asset Group'
        },
        assettypes: {
            title: 'Asset Types', category: 'Asset & Component', icon: 'fa-shapes',
            description: 'Configure asset categories, vendor references and service requirements.',
            listTitle: 'Asset Type Directory', listDescription: 'Review calibration and maintenance settings by asset type.', action: 'Add Asset Type'
        },
        assetstatus: {
            title: 'Asset Statuses', category: 'Asset & Component', icon: 'fa-shield-alt',
            description: 'Maintain the standard lifecycle statuses available to assets.',
            listTitle: 'Asset Status Directory', listDescription: 'Create clear status labels for consistent asset tracking.', action: 'Add Asset Status'
        },
        itemstatus: {
            title: 'Component Statuses', category: 'Asset & Component', icon: 'fa-box-open',
            description: 'Maintain the condition statuses available to inventory components.',
            listTitle: 'Component Status Directory', listDescription: 'Create clear status labels for component tracking.', action: 'Add Component Status'
        },
        storelocation: {
            title: 'Store Locations', category: 'Location & Lifecycle', icon: 'fa-warehouse',
            description: 'Define the storage areas used when assets or components are held in store.',
            listTitle: 'Store Location Directory', listDescription: 'Manage the available storage destination names.', action: 'Add Store Location'
        },
        disposalmethod: {
            title: 'Disposal Methods', category: 'Location & Lifecycle', icon: 'fa-recycle',
            description: 'Define the approved methods used during asset disposal.',
            listTitle: 'Disposal Method Directory', listDescription: 'Review existing methods while adding or editing a record.', action: '', hint: false
        },
        write_off_reasons: {
            title: 'Write-Off Reasons', category: 'Location & Lifecycle', icon: 'fa-file-signature',
            description: 'Maintain the approved reasons used to support asset write-off requests.',
            listTitle: 'Write-Off Reason Directory', listDescription: 'Search by reason or status and keep descriptions up to date.', action: 'Add Write-Off Reason'
        },
        user_groups: {
            title: 'User Groups', category: 'Users & Permissions', icon: 'fa-users-cog',
            description: 'Organise users into clear operational groups for consistent access management.',
            listTitle: 'User Group Directory', listDescription: 'Review group purpose and availability before assigning users.', action: 'Add User Group'
        },
        designations: {
            title: 'Designations', category: 'Users & Permissions', icon: 'fa-id-badge',
            description: 'Maintain the job titles available for users and operational contacts.',
            listTitle: 'Designation Directory', listDescription: 'Keep role titles clear, searchable and ready for assignment.', action: 'Add Designation'
        },
        worker_locations: {
            title: 'Worker Locations', category: 'Users & Permissions', icon: 'fa-map-marked-alt',
            description: 'Define the attendance locations workers can select in the mobile application.',
            listTitle: 'Worker Location Directory', listDescription: 'Manage location names, descriptions and availability.', action: 'Add Worker Location'
        },
        permissions: {
            title: 'Permissions', category: 'Users & Permissions', icon: 'fa-user-shield',
            description: 'Review the system capabilities that can be granted through user roles and groups.',
            listTitle: 'Permission Rule Directory', listDescription: 'System rules are protected; custom rules can be reviewed or removed carefully.', action: 'Add Permission Rule'
        }
    };

    function makeHero(meta) {
        return $('<header class="master-page-hero" aria-labelledby="master-page-title">' +
            '<div class="master-page-hero__copy">' +
                '<span class="master-page-hero__icon"><i class="fas ' + meta.icon + '"></i></span>' +
                '<div><span class="master-page-eyebrow"><i class="fas fa-database"></i> Master Data <b>/</b> ' + meta.category + '</span>' +
                '<h1 id="master-page-title">' + meta.title + '</h1>' +
                '<p>' + meta.description + '</p></div>' +
            '</div>' +
            '<div class="master-page-hero__actions"></div>' +
            '<div class="master-page-hero__meta"><span><i class="fas fa-circle"></i> Live configuration</span><span class="master-record-count"><strong>—</strong> records</span></div>' +
        '</header>');
    }

    function panelHeading(meta) {
        var hint = meta.hint === false ? '' : '<span class="master-panel-heading__hint"><i class="fas fa-search"></i> Use search to find a record</span>';
        return $('<div class="master-panel-heading"><div><span class="master-page-eyebrow">Reference Directory</span><h2>' +
            meta.listTitle + '</h2><p>' + meta.listDescription + '</p></div>' + hint + '</div>');
    }

    function enhanceActionButtons($scope) {
        $scope.find('table button, table a').each(function () {
            var $button = $(this);
            var signature = (($button.attr('class') || '') + ' ' + ($button.attr('title') || '') + ' ' + $button.text()).toLowerCase();
            var type = '';
            var label = '';

            if (/delete|trash|remove/.test(signature)) {
                type = 'delete';
                label = 'Delete';
            } else if (/edit|update/.test(signature)) {
                type = 'edit';
                label = 'Edit';
            } else if (/view|manage/.test(signature)) {
                type = 'view';
                label = 'Manage';
            }

            if (!type) return;

            $button.addClass('master-row-action master-row-action--' + type).attr('aria-label', label);
            if (!$button.attr('title')) $button.attr('title', label);
            if (!$.trim($button.clone().children().remove().end().text())) {
                $button.append('<span>' + label + '</span>');
            }
        });
    }

    function updateRecordCount($workspace, tableNode) {
        var count = null;

        try {
            if ($.fn.DataTable && $.fn.DataTable.isDataTable(tableNode)) {
                count = $(tableNode).DataTable().page.info().recordsDisplay;
            }
        } catch (ignore) {}

        if (count === null) {
            var $rows = $(tableNode).find('tbody tr');
            count = $rows.filter(function () {
                return !$(this).find('td.dataTables_empty, td[colspan]').length;
            }).length;
        }

        $workspace.find('.master-record-count strong').text(count);
    }

    function enhanceStateToggles($scope) {
        $scope.find('.toggle').each(function () {
            var $toggle = $(this);
            var isActive = !$toggle.hasClass('off');

            $toggle.addClass('master-state-toggle')
                .attr('aria-label', isActive ? 'Active' : 'Inactive')
                .attr('title', isActive ? 'Click to make inactive' : 'Click to make active');
            $toggle.find('.toggle-on').text('Active');
            $toggle.find('.toggle-off').text('Inactive');
        });
    }

    $(function () {
        var $body = $('body.master-module-page');
        if (!$body.length) return;

        var controller = String($body.data('controller') || '').toLowerCase();
        var meta = pages[controller];
        if (!meta) return;

        var $workspace = $('#content > .container-fluid').first().addClass('master-workspace');
        if (!$workspace.length) return;

        $('#content > h5').first().addClass('master-legacy-page-title').attr('aria-hidden', 'true');

        var $action = $workspace.find('a[data-toggle="modal"][data-target="#addModal"], button#addReasonBtn').first();
        var $hero = makeHero(meta);

        if ($action.length) {
            $action.detach()
                .removeAttr('style')
                .removeClass('float-right text_successo btn_border btn-default btn-primary btn-sm')
                .addClass('master-primary-action')
                .html('<i class="fas fa-plus"></i><span>' + (meta.action || 'Add Record') + '</span>');
            $hero.find('.master-page-hero__actions').append($action);
        } else {
            $hero.find('.master-page-hero__actions').append('<span class="master-context-badge"><i class="fas fa-lock"></i> Controlled reference data</span>');
        }

        $workspace.prepend($hero);

        $workspace.children('h1, h2, h3, h4').first().addClass('master-legacy-heading');
        $workspace.find('.write-off-reasons-container > .d-flex.justify-content-between').first().addClass('master-legacy-heading');

        var $primaryPanel = $workspace.children('.card, .write-off-reasons-container, .container-fluid').first();
        if (!$primaryPanel.length) $primaryPanel = $workspace;
        $primaryPanel.addClass('master-panel');

        var $legacyHeader = $primaryPanel.children('.card-header')
            .add($primaryPanel.children('.card-body').children('.card-header'))
            .first();
        if ($legacyHeader.length) {
            $legacyHeader.addClass('master-legacy-card-header');
            $legacyHeader.before(panelHeading(meta));
        } else {
            var $table = $primaryPanel.find('table').first();
            if ($table.length) {
                var $responsive = $table.closest('.table-responsive');
                ($responsive.length ? $responsive : $table).before(panelHeading(meta));
            }
        }

        if (controller === 'disposalmethod') {
            $primaryPanel.find('.master-panel-heading').first().prependTo($primaryPanel);
            $primaryPanel.children('.row').addClass('master-split-layout');
            $primaryPanel.children('.row').children('[class*="col-md-"]').addClass('master-split-card');
            $primaryPanel.children('h4').addClass('master-legacy-heading');
        }

        if (controller === 'write_off_reasons') {
            $primaryPanel.find('.master-panel-heading').first().prependTo($primaryPanel);
        }

        $workspace.find('.card').addClass('master-surface-card');
        $workspace.find('.table-responsive').addClass('master-table-shell');
        $workspace.find('table').addClass('master-data-table');
        $workspace.find('.btn-group').addClass('master-filter-group');
        $workspace.find('.modal').addClass('master-modal');
        $workspace.find('.modal-title').each(function () {
            var $title = $(this);
            if (!$title.children('.master-modal-title-icon').length) {
                $title.prepend('<span class="master-modal-title-icon"><i class="fas ' + meta.icon + '"></i></span>');
            }
        });
        $workspace.find('.modal-footer .btn-secondary').each(function () {
            if ($.trim($(this).text()).toLowerCase() === 'close') $(this).text('Cancel');
        });

        enhanceActionButtons($workspace);
        enhanceStateToggles($workspace);

        $workspace.find('table').each(function () {
            updateRecordCount($workspace, this);
        });

        $workspace.on('draw.dt', 'table', function () {
            enhanceActionButtons($workspace);
            enhanceStateToggles($workspace);
            updateRecordCount($workspace, this);
        });

        setTimeout(function () {
            enhanceActionButtons($workspace);
            enhanceStateToggles($workspace);
            $workspace.find('table').each(function () { updateRecordCount($workspace, this); });
        }, 250);
    });
})(jQuery);
