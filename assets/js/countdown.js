document.addEventListener('DOMContentLoaded', function () {
    const clockDisplay = document.getElementById('clock-display');
    const endTime = new Date('2025-12-31T23:59:59').getTime(); // Customize this date/time

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance <= 0) {
            clockDisplay.innerHTML = "EXPIRED";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        clockDisplay.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }

    updateCountdown();                 // Initial call
