document.addEventListener("DOMContentLoaded", () => {
    const roomCode = window.ROOM_CODE;
    const roomId = window.ROOM_ID;
    const baseUrl = window.BASE_URL;
    let pollInterval = null;

    function pollRoomStatus() {
        fetch(`${baseUrl}/api/room/${roomCode}/status`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
        .then((res) => res.json())
        .then((data) => {
            if (data.status === "in_game" && data.match_id) {
                clearInterval(pollInterval);
                window.location.href = `${baseUrl}/game/match/${data.match_id}`;
                return;
            }

            if (data.players) {
                renderPlayers(data.players);
            }
        })
        .catch((err) => {
            console.error("Error al actualizar sala:", err);
        });
    }

    function renderPlayers(players) {
        const container = document.getElementById("playerSlotsContainer");
        const countEl = document.getElementById("playerCount");
        if (!container) return;

        if (countEl) {
            countEl.innerText = players.length;
        }

        let html = "";
        players.forEach((p) => {
            const isHost = Number(p.is_host) === 1;
            const isReady = Number(p.is_ready) === 1;
            const initial = p.username.charAt(0).toUpperCase();

            html += `
                <div class="col-md-6" id="slot-user-${p.user_id}">
                    <div class="p-3 bg-dark rounded border border-secondary d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="avatar-circle me-3">${initial}</span>
                            <div>
                                <div class="fw-bold text-white">
                                    ${escapeHtml(p.username)}
                                    ${isHost ? '<span class="badge bg-warning text-dark ms-1" style="font-size: 0.7rem;">Anfitrión</span>' : ""}
                                </div>
                                <div class="small text-muted">
                                    Nivel ${p.level} &bull; ${p.elo_rating || 1200} ELO
                                    ${p.team ? `&bull; <strong class="${p.team === "A" ? "text-primary" : "text-danger"}">Equipo ${p.team}</strong>` : ""}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            ${isReady ? '<span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Listo</span>' : '<span class="badge bg-secondary">Esperando...</span>'}
                            ${window.IS_HOST && Number(p.user_id) !== window.CURRENT_USER_ID ? `
                                <button class="btn btn-sm btn-outline-danger" onclick="kickPlayer(${p.user_id})" title="Expulsar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            ` : ""}
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function escapeHtml(string) {
        const div = document.createElement("div");
        div.innerText = string;
        return div.innerHTML;
    }

    // Iniciar sondeo cada 1.5 segundos
    pollInterval = setInterval(pollRoomStatus, 1500);

    // Funciones globales de sala
    window.toggleMyReady = function() {
        fetch(`${baseUrl}/api/room/ready`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ room_id: roomId }),
        }).then(() => pollRoomStatus());
    };

    window.changeMyTeam = function(team) {
        fetch(`${baseUrl}/api/room/team`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ room_id: roomId, team: team }),
        }).then(() => pollRoomStatus());
    };

    window.kickPlayer = function(targetUserId) {
        if (!confirm("¿Deseas expulsar a este jugador de la sala?")) return;
        fetch(`${baseUrl}/api/room/kick`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ room_id: roomId, user_id: targetUserId }),
        }).then(() => pollRoomStatus());
    };

    window.startHostGame = function() {
        const btn = document.getElementById("btnStartGame");
        if (btn) btn.disabled = true;

        fetch(`${baseUrl}/api/room/start`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ room_id: roomId }),
        })
        .then((res) => res.json())
        .then((data) => {
            if (data.success && data.match_id) {
                window.location.href = `${baseUrl}/game/match/${data.match_id}`;
            } else {
                alert(data.message || "No se pudo iniciar la partida.");
                if (btn) btn.disabled = false;
            }
        })
        .catch((err) => {
            console.error("Error al iniciar partida:", err);
            if (btn) btn.disabled = false;
        });
    };
});

