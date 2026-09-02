(function ($) {
   'use strict';

   if (window.Dropzone) {
      window.Dropzone.autoDiscover = false;
   }

   $(function () {
      var $roles = $('#roles');
      var $permissionOptions = $('.user-permission-option');

      function updateRoleCount() {
         var count = $roles.find('option:selected').length;
         $('#user-assigned-role-count, #user-role-tab-count, #user-role-panel-count').text(count);
      }

      function updatePermissionCount() {
         var selected = $('.user-permission-option input[type="checkbox"]:checked:not(:disabled)').length;
         var visible = $permissionOptions.filter(function () {
            return this.style.display !== 'none';
         }).length;
         $('#permission-selected-count, #user-override-count, #user-permission-tab-count').text(selected);
         $('#permission-visible-count').text(visible);
      }

      function applyPermissionSearch() {
         var query = $.trim($('#permission-search').val()).toLowerCase();
         var visibleCategories = 0;

         $('.user-permission-category').each(function () {
            var $category = $(this);
            var visibleItems = 0;

            $category.find('.user-permission-option').each(function () {
               var $option = $(this);
               var matches = !query || String($option.data('permission-name') || '').indexOf(query) !== -1;
               $option.toggle(matches);
               if (matches) visibleItems += 1;
            });

            $category.find('.user-permission-category-empty').prop('hidden', visibleItems !== 0);
            $('#' + $category.attr('id') + '-tab').toggle(visibleItems > 0);
            if (visibleItems > 0) visibleCategories += 1;
         });

         $('.user-permission-empty').prop('hidden', visibleCategories !== 0);

         var $categoryTabs = $('#user-permission-category-tabs .nav-link');
         var $activeTab = $categoryTabs.filter('.active');

         if (visibleCategories === 0) {
            $categoryTabs.removeClass('active').attr('aria-selected', 'false');
            $('.user-permission-category').removeClass('show active');
         } else if (!$activeTab.length || $activeTab.css('display') === 'none') {
            var $firstAvailableTab = $('#user-permission-category-tabs .nav-link').filter(function () {
               return $(this).css('display') !== 'none';
            }).first();
            if ($firstAvailableTab.length) $firstAvailableTab.tab('show');
         }

         updatePermissionCount();
      }

      if ($roles.length) {
         $roles.multiSelect({
            selectableHeader: '<div class="user-role-list-header"><span><i class="fas fa-layer-group"></i>Available Roles</span><small>Click to assign</small></div>',
            selectionHeader: '<div class="user-role-list-header is-assigned"><span><i class="fas fa-check-circle"></i>Assigned Roles</span><small>Click to remove</small></div>',
            afterSelect: updateRoleCount,
            afterDeselect: updateRoleCount
         });

         $('#assign-all-roles').on('click', function () {
            $roles.multiSelect('select_all');
            updateRoleCount();
         });

         $('#clear-all-roles').on('click', function () {
            $roles.multiSelect('deselect_all');
            updateRoleCount();
         });

         updateRoleCount();
      }

      $('#branches_select2').select2({
         placeholder: 'Select branches'
      });

      $('#branches').multiSelect({
         selectableHeader: 'Branches available',
         selectionHeader: 'Assigned branches'
      });

      $('.permissions').on('click', 'button[data-filter]', function () {
         var $button = $(this);
         var $inputs = $('#permission-category-' + $button.data('id')).find('input[type="checkbox"]:not(:disabled)');
         $inputs.prop('checked', $button.data('filter') === 'all').trigger('change');
      });

      $('.permissions').on('change', '.user-permission-option input[type="checkbox"]', updatePermissionCount);
      $('#permission-search').on('input', applyPermissionSearch);
      applyPermissionSearch();

      $('[data-toggle-password]').on('click', function () {
         var $button = $(this);
         var $input = $('#' + $button.attr('data-toggle-password'));
         var reveal = $input.attr('type') === 'password';
         $input.attr('type', reveal ? 'text' : 'password');
         $button.attr('aria-label', reveal ? 'Hide password' : 'Show password');
         $button.find('i').toggleClass('fa-eye', !reveal).toggleClass('fa-eye-slash', reveal);
      });

      $('#form_company_name').autocomplete({
         source: appUrl('/companies/search_ajax'),
         minLength: 2,
         change: function (event, ui) {
            if (!ui.item) {
               $('#company_id').val('');
               $('.form-control#form_company_name').addClass('is-invalid').removeClass('is-valid');
            }
         },
         select: function (event, ui) {
            $('.form-control#form_company_name').addClass('is-valid').removeClass('is-invalid');
            $('#company_id').val(ui.item.id);
         }
      });

      if ($('.user-photo-dropzone').length && window.Dropzone) {
         $('.user-photo-dropzone').dropzone({
            acceptedFiles: 'image/jpeg,image/png,image/webp',
            uploadMultiple: false,
            maxFiles: 1,
            dictDefaultMessage: '<i class="fas fa-cloud-upload-alt"></i><strong>Upload a new photo</strong><small>Click or drop an image here</small>',
            queuecomplete: function () {
               window.location.reload();
            }
         });
      }

      var hash = window.location.hash;
      if (hash && $('.user-detail-tabs a[href="' + hash + '"]').length) {
         $('.user-detail-tabs a[href="' + hash + '"]').tab('show');
      }

      $('.user-detail-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
         var target = $(event.target).attr('href');
         if (window.history && window.history.replaceState) {
            window.history.replaceState(null, document.title, window.location.pathname + window.location.search + target);
         }
      });
   });
})(jQuery);
