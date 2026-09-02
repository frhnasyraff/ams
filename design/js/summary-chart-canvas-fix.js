function prepareSummaryChartCanvas(id) {
    var canvas = document.getElementById(id);
    if (!canvas) return canvas;

    var size = canvas.closest('.summary-ref-empty-chart') ? 200 : 230;
    if (window.matchMedia && window.matchMedia('(max-width: 1500px)').matches) {
        size = canvas.closest('.summary-ref-empty-chart') ? 188 : 216;
    }

    canvas.width = size;
    canvas.height = size;
    canvas.style.width = size + 'px';
    canvas.style.height = size + 'px';
    canvas.style.maxWidth = size + 'px';
    canvas.style.maxHeight = size + 'px';
    return canvas;
}
