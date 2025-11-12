document.addEventListener('DOMContentLoaded', function () {
  const targetInput = document.querySelector('[name="target_date"]');
  const preview = document.getElementById('countdown-preview');
  const titleInput = document.querySelector('[name="title"]');
  const formatSelect = document.querySelector('[name="format"]');

  function updatePreview() {
    const title = titleInput?.value || '';
    const format = formatSelect?.value || 'digital';
    const targetTime = new Date(targetInput?.value || '');

    if (!preview || !targetTime.getTime()) {
      preview.innerHTML = "<em>Please select a target date.</em>";
      return;
    }

    function renderCountdown() {
      const now = new Date();
      const diff = targetTime - now;

      if (diff <= 0) {
        preview.innerHTML = "<strong>Countdown Complete!</strong>";
        return;
      }

      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
      const minutes = Math.floor((diff / 1000 / 60) % 60);
      const seconds = Math.floor((diff / 1000) % 60);

      let visual = '';
      if (format === "digital") {
        visual = `${days}d ${hours}h ${minutes}m ${seconds}s`;
      } else if (format === "donut" || format === "circle") {
        visual = '<div style="border: 4px solid #222; border-radius: 50%; padding: 20px; width: 100px; height: 100px; line-height: 60px; text-align: center;">'
               + `${days}:${hours}:${minutes}` + '</div>';
      } else if (format === "gutenberg") {
        visual = `<pre style="font-family:monospace;">${days} Days\n${hours} Hours\n${minutes} Minutes\n${seconds} Seconds</pre>`;
      }

      preview.innerHTML = `
        <div class="preview-box format-${format}">
          <div class="preview-title">${title}</div>
          <div class="preview-timer">${visual}</div>
        </div>`;
    }

    clearInterval(window._livePreviewTimer);
    renderCountdown();
    window._livePreviewTimer = setInterval(renderCountdown, 1000);
  }

  [targetInput, titleInput, formatSelect].forEach(el => {
    if (el) el.addEventListener('input', updatePreview);
  });

  updatePreview();
});
