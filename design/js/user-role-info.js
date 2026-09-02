$(document).ready(function () {
   var $users = $("#users");

   function updateUserCounts() {
      var total = $users.find("option").length;
      var assigned = $users.find("option:selected").length;
      $("#role-assigned-count, #role-assigned-list-count").text(assigned);
      $("#role-available-count, #role-available-list-count").text(Math.max(0, total - assigned));
   }

   function updatePermissionCount() {
      var selected = $('.role-permission-grid input[type="checkbox"]:checked').length;
      $("#role-permission-count, #role-permission-footer-count").text(selected);
   }

   if ($users.length && $.fn.multiSelect) {
      $users.multiSelect({
         selectableHeader: '<div class="role-user-list-header"><span><i class="fas fa-user-plus"></i> Available Users</span><small><strong id="role-available-list-count">0</strong> available</small></div>',
         selectionHeader: '<div class="role-user-list-header is-assigned"><span><i class="fas fa-user-check"></i> Assigned to Role</span><small><strong id="role-assigned-list-count">0</strong> assigned</small></div>',
         afterSelect: updateUserCounts,
         afterDeselect: updateUserCounts
      });
      updateUserCounts();
   }

   $(".permissions .role-permission-bulk-actions button[data-filter]").click(function () {
      var categoryId = $(this).data("id");
      var shouldSelect = $(this).data("filter") === "all";
      $(".permissions .role-permission-grid#permission_" + categoryId + ' input[type="checkbox"]:visible').prop("checked", shouldSelect).trigger("change");
   });

   $('.role-permission-grid input[type="checkbox"]').on('change', updatePermissionCount);

   $('#role-permission-search').on('input', function () {
      var search = $.trim($(this).val()).toLowerCase();

      $('.role-permission-content .tab-pane').each(function () {
         var $pane = $(this);
         var visible = 0;

         $pane.find('.role-permission-option').each(function () {
            var matches = !search || $(this).text().toLowerCase().indexOf(search) !== -1;
            $(this).toggle(matches);
            if (matches) visible++;
         });

         $pane.find('.role-permission-empty').prop('hidden', visible !== 0);
      });
   });

   updatePermissionCount();
});
