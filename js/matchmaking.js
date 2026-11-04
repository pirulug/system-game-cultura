document.addEventListener("DOMContentLoaded", () => {
    const timerDisplay = document.getElementById("searchTimer");
    const btnCancel = document.getElementById("btnCancelQueue");
    const mode = window.MATCHMAKING_MODE || "1vs1";
    const baseUrl = window.BASE_URL || "";

    let secondsElapsed = 0;
    let pollInterval = null;
    let timerInterval = null;

    // Iniciar temporizador visual
    timerInterval = setInterval(() => {
        secondsElapsed++;
        const mins = String(Math.floor(secondsElapsed / 60)).padStart(2, "0");
        const secs = String(secondsElapsed % 60).padStart(2, "0");
        if (timerDisplay) {
            timerDisplay.innerText = `${mins}:${secs}`;
        }
    }, 1000);

    // Unirse a la cola
    fetch(`${baseUrl}/api/matchmaking/join`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ mode: mode }),
    })
    .then((res) => res.json())
    .then((data) => {
        if (data.status === "matched" && data.match_id) {
            window.location.href = `${baseUrl}/game/match/${data.match_id}`;
        } else {
            // Empezar sondeo
            startPolling();
        }
    })
    .catch((err) => {
        console.error("Error al unirse a la cola:", err);
    });

    function startPolling() {
        pollInterval = setInterval(() => {
            fetch(`${baseUrl}/api/matchmaking/status`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            })
            .then((res) => res.json())
            .then((data) => {
                if (data.status === "matched" && data.match_id) {
                    clearInterval(pollInterval);
                    clearInterval(timerInterval);
                    window.location.href = `${baseUrl}/game/match/${data.match_id}`;
                }
            })
            .catch((err) => {
                console.error("Error en sondeo de cola:", err);
            });
        }, 1500);
    }

    // Cancelar busqueda
    if (btnCancel) {
        btnCancel.addEventListener("click", () => {
            clearInterval(pollInterval);
            clearInterval(timerInterval);

            fetch(`${baseUrl}/api/matchmaking/cancel`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            }).finally(() => {
                window.location.href = `${baseUrl}/game/modes`;
            });
        });
    }
});

