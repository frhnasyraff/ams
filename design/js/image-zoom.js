$(document).ready(function () {

    // Get the modal
    var modal = $('#zoom-modal');

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var modalImg = $("#img01");
    var captionText = $("#caption");

    $(document).on('click', '.zoomit', function () {
        modal.css('display', 'block');
        modalImg.attr('src', $(this).attr('src'));
        captionText.html($(this).attr('alt'));
    });

    $(document).on('click', '.close', function () {
        modal.css('display', 'none');
    });
});