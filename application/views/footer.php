</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->

<!-- Footer -->
<!-- <footer class="footer mt-2 mb-4 static-top">
  <div class="container my-auto">
    <div class="copyright text-center my-auto">
      <span>Copyright &copy; Eastern <?= date("Y"); ?></span>
    </div>
  </div>
</footer> -->
<!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
  <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">Ã—</span>
        </button>
      </div>
      <div class="modal-body">Click the "Logout" button to end your current session and go back to the login screen.</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <?php if ($this->user_model->current_user()->user_group == 9) { ?>
          <a class="btn btn-primary" href="<?= site_url("user/logout"); ?>">Logout</a>

        <?php
        } else { ?>
          <a class="btn btn-primary" href="<?= site_url("user/logout"); ?>">Logout</a>
        <?php  } ?>
      </div>
    </div>
  </div>
</div>
<div class="loading d-none">Loading</div>
<!-- Bootstrap core JavaScript-->
<script src="<?= site_url('design/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?= site_url('design/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= site_url('design/js/bootstrap-toggle.min.js'); ?>"></script>
<script src="<?= site_url('design/js/jquery.bootstrap-growl.min.js'); ?>"></script>
<script src="<?= site_url('design/js/clipboard.min.js'); ?>"></script>

<!-- Core plugin JavaScript-->
<script src="<?= site_url('design/vendor/jquery-easing/jquery.easing.min.js'); ?>"></script>

<script src="<?= site_url('design/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>

<script src="<?= site_url('design/vendor/datatables/dataTables.bootstrap4.min.js'); ?>"></script>

<!-- Custom scripts for all pages-->
<script src="<?= site_url('design/js/sb-admin-2.min.js'); ?>"></script>
<script type="text/javascript">
  window.appBasePath = <?= json_encode(rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/')) ?>;

  function appUrl(path) {
    path = String(path || '');
    if (!window.appBasePath || path.charAt(0) !== '/' || path.indexOf(window.appBasePath + '/') === 0) {
      return path;
    }
    return window.appBasePath + path;
  }

  // Keep legacy root-relative AJAX URLs working when the app is hosted in /assets_IT-usman/.
  $.ajaxPrefilter(function(options) {
    if (options.url) {
      options.url = appUrl(options.url);
    }
  });

  // Keep legacy generated links like href="/items/info" inside this app subfolder.
  $(document).on('click', 'a[href^="/"]', function() {
    var href = $(this).attr('href');
    if (href && href.indexOf('//') !== 0) {
      $(this).attr('href', appUrl(href));
    }
  });
</script>
<script src="<?= site_url('design/js/scripts.js?66'); ?>"></script>

<!-- Legacy location for root-relative AJAX guard moved above scripts.js so it also protects global scripts. -->

<!--These jQuery libraries for select2 need to be included-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<?php if (is_file(FCPATH . 'design/js/mapbox-config.local.js')) { ?>
  <script src="<?= site_url('design/js/mapbox-config.local.js'); ?>"></script>
<?php } ?>

<?php if (isset($scripts)) {
  foreach ($scripts as $script) { ?>
    <!-- Shared page-script cache version: ?77 -->
    <script type="text/javascript" src='<?= (preg_match("/http/", $script) ? $script : site_url($script . "?77")); ?>'>
    </script>
<?php }
} ?>

<script type="text/javascript" src="<?= site_url('design/js/master-ui.js?3'); ?>"></script>

<script type="text/javascript">
  $(".worker_employment_types_selection #form_type").change(function() {
    if ($(this).val() === "outsourced_driver") {
      $('.outsource_company_name_input').show();
    } else {
      $('.outsource_company_name_input').hide();
    }
  });
</script>

<script>
$(document).ready(function() {
    // Automatically close the alert after 2 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 2000); // 2000 milliseconds = 2 seconds    
});


</script>

  <script>
      const base_url = "<?= base_url(); ?>";
  </script>
</body>

</html>




