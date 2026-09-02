(function () {
    function triggerDashboardUpload(button) {
        var form = button.closest('form.dropzone');
        if (!form) return;

        // Preferred path: use the Dropzone instance created by the legacy upload script.
        if (form.dropzone && form.dropzone.hiddenFileInput) {
            form.dropzone.hiddenFileInput.click();
            return;
        }

        // Fallback path retained for pages where Dropzone failed to initialise.
        var fallbackInput = form.querySelector('input[type="file"]');
        if (fallbackInput) {
            fallbackInput.click();
        }
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('.asset-upload-pill');
        if (!button) return;

        event.preventDefault();
        event.stopPropagation();
        triggerDashboardUpload(button);
    }, true);
})();
