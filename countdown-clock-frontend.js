
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".countdown-clock").forEach(function(clock) {
        const targetTime = new Date(clock.dataset.target);
        const countup = clock.dataset.countup === "1";
        const display = clock.querySelector(".countdown-timer");

        function update() {
            const now = new Date();
            let diff = targetTime - now;

            let label = "Remaining";
            if (diff < 0 && countup) {
                diff = now - targetTime;
                label = "Since";
            }

            if (diff < 0 && !countup) {
                display.textContent = "Expired";
                return;
            }

            const totalSeconds = Math.floor(diff / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            display.textContent = `${label}: ${days}d ${hours}h ${minutes}m ${seconds}s`;
        }

        update();
        setInterval(update, 1000);
    });
});


    // Dispatch event on countdown end
    const event = new CustomEvent("countdownEnded", { detail: { id: el.dataset.id } });
    document.dispatchEvent(event);

    const redirectUrl = clock.dataset.redirect;
    if (redirectUrl) {
        setTimeout(() => window.location.href = redirectUrl, 500);
    }

// Track countdown impressions and completion
document.addEventListener("DOMContentLoaded", function () {
    document.dispatchEvent(new CustomEvent("countdownViewed", { detail: { id: el.dataset.id } }));
});

document.addEventListener("countdownEnded", function(e) {
    console.log("Countdown ended for ID:", e.detail.id);
    fetch('/?countdown_complete=' + e.detail.id);
});
