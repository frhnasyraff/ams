(function () {
  var launcher = document.querySelector('[data-ai-widget-open]');
  var widget = document.querySelector('[data-ai-widget]');
  if (!launcher || !widget) return;

  var frame = widget.querySelector('[data-ai-widget-frame]');
  var closeBtn = widget.querySelector('[data-ai-widget-close]');
  var minimizeBtn = widget.querySelector('[data-ai-widget-minimize]');
  var fullscreenBtn = widget.querySelector('[data-ai-widget-fullscreen]');

  function ensureFrame() {
    if (frame && !frame.getAttribute('src')) {
      frame.setAttribute('src', frame.getAttribute('data-src'));
    }
  }

  function openWidget() {
    ensureFrame();
    widget.classList.add('is-open');
    widget.classList.remove('is-minimized');
    launcher.style.display = 'none';
  }

  function closeWidget() {
    widget.classList.remove('is-open', 'is-minimized', 'is-fullscreen');
    launcher.style.display = 'inline-flex';
  }

  launcher.addEventListener('click', openWidget);
  closeBtn.addEventListener('click', closeWidget);
  minimizeBtn.addEventListener('click', function () {
    widget.classList.toggle('is-minimized');
  });
  fullscreenBtn.addEventListener('click', function () {
    widget.classList.toggle('is-fullscreen');
  });
})();
