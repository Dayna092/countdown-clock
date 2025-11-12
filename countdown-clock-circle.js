
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".countdown-clock-circle").forEach(function(clock) {
        const targetTime = new Date(clock.dataset.target);
        const countup = clock.dataset.countup === "1";

        
        const bgColor = clock.dataset.bg || '#eee';
        const ringColor = clock.dataset.color || '#2196f3';
        const font = clock.dataset.font || 'Roboto';
        const fontColor = clock.dataset.fontcolor || '#000';
        const thickness = parseInt(clock.dataset.thickness || '10');
        const size = parseInt(clock.dataset.size || '100');
        const glowEnabled = clock.dataset.glow === '1';
        const speed = parseInt(clock.dataset.speed || '1000');

        clock.querySelectorAll("circle.bg").forEach(c => {
            c.style.stroke = bgColor;
            c.style.strokeWidth = thickness;
        });
        clock.querySelectorAll("circle.progress").forEach(c => {
            c.style.stroke = ringColor;
            c.style.strokeWidth = thickness;
        });
        clock.querySelectorAll(".circle-unit svg").forEach(svg => {
            svg.style.width = size + "px";
            svg.style.height = size + "px";
        });
        clock.querySelectorAll(".circle-value").forEach(val => {
            val.style.fontFamily = font;
            val.style.color = fontColor;
        });

function updateCircle(value, max, el) {
            const circle = el.querySelector("circle.progress");
            const radius = circle.r.baseVal.value;
            const circumference = 2 * Math.PI * radius;
            const offset = circumference - (value / max) * circumference;
            circle.style.strokeDasharray = circumference;
            circle.style.strokeDashoffset = offset;
            el.querySelector(".value").textContent = value;
        }

        function update() {
            const now = new Date();
            let diff = targetTime - now;
            let totalSeconds = Math.floor(diff / 1000);

            if (diff < 0 && countup) {
                totalSeconds = Math.floor((now - targetTime) / 1000);
            }

            if (diff < 0 && !countup) {
                clock.dispatchEvent(new CustomEvent('countdownEnded', { detail: { id: clock.dataset.id }}));
                return;
            }

            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            const units = {
                days: days,
                hours: hours,
                minutes: minutes,
                seconds: seconds
            };

            for (const [unit, val] of Object.entries(units)) {
                const el = clock.querySelector(`.value[data-unit="${unit}"]`)?.closest(".circle-unit");
                if (el) {
                    let max = unit === "days" ? 365 : (unit === "hours" ? 24 : (unit === "minutes" ? 60 : 60));
                    updateCircle(val, max, el);
                }
            }
        }

        update();
        const interval = isNaN(speed) || speed <= 0 ? 1000 : speed;
        setInterval(update, interval);
    });
});
