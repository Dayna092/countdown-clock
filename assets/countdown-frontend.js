document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".countdown-clock-frontend").forEach(function (clockEl) {
    const dateStr = clockEl.getAttribute("data-date");
    const format = clockEl.getAttribute("data-format");
    const output = clockEl.querySelector(".countdown-output");
    const targetTime = new Date(dateStr);
    if (!targetTime.getTime()) {
      output.innerHTML = "Invalid countdown date";
      return;
    }

    function updateClock() {
      const now = new Date();
      const diff = targetTime - now;
      if (diff <= 0) {
        output.innerHTML = "Countdown Complete!";
        return;
      }
      const d = Math.floor(diff / (1000 * 60 * 60 * 24));
      const h = Math.floor((diff / (1000 * 60 * 60)) % 24);
      const m = Math.floor((diff / 1000 / 60) % 60);
      const s = Math.floor((diff / 1000) % 60);
      let html = '';
      if (format === "digital") {
        html = `${d}d ${h}h ${m}m ${s}s`;
      } else if (format === "donut" || format === "circle") {
        html = `<div style='border:3px solid #333;border-radius:50%;padding:20px;width:100px;height:100px;line-height:60px;text-align:center;'>${d}:${h}:${m}</div>`;
      } else if (format === "gutenberg") {
        html = `<pre style="font-family:monospace;">${d} Days\n${h} Hours\n${m} Minutes\n${s} Seconds</pre>`;
      } else {
        html = `${d}d ${h}h ${m}m ${s}s`;
      }
      output.innerHTML = html;
    }

    updateClock();
    setInterval(updateClock, 1000);
  });
});
