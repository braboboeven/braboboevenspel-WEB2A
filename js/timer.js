function startLiveTimer(startTimestamp) {
    const startTime = startTimestamp * 1000;

    function updateTimer() {
        const now = Date.now();
        const diff = Math.floor((now - startTime) / 1000);

        // Voorkom negatieve getallen bij kleine tijdsverschillen
        const totalSeconds = diff > 0 ? diff : 0;

        const mins = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
        const secs = (totalSeconds % 60).toString().padStart(2, '0');

        const display = document.getElementById('display-timer');
        if (display) {
            display.innerText = mins + ":" + secs;
        }
    }

    // Update elke seconde
    setInterval(updateTimer, 1000);
    updateTimer(); // Directe aanroep voor eerste weergave
}
   