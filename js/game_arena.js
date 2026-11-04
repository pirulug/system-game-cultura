document.addEventListener("DOMContentLoaded", () => {
    const matchId = window.MATCH_ID;
    const currentUserId = window.CURRENT_USER_ID;
    const baseUrl = window.BASE_URL;

    let pollInterval = null;
    let localTimerInterval = null;
    let currentRoundOrder = 0;
    let hasAnsweredCurrentRound = false;
    let selectedOptionIdCurrent = null;
    let currentMatchQuestionId = 0;
    let isShowingResultPhase = false;

    // Elementos DOM
    const categoryBadge = document.getElementById("arenaCategoryBadge");
    const categoryName = document.getElementById("arenaCategoryName");
    const categoryIcon = document.getElementById("arenaCategoryIcon");
    const difficultyBadge = document.getElementById("arenaDifficultyBadge");
    const questionCounter = document.getElementById("arenaQuestionCounter");
    const timerDisplay = document.getElementById("arenaTimerDisplay");
    const questionText = document.getElementById("arenaQuestionText");
    const revealBox = document.getElementById("revealBox");
    const revealExplanationText = document.getElementById("revealExplanationText");
    const revealSourceText = document.getElementById("revealSourceText");
    const revealCountdownBadge = document.getElementById("revealCountdownBadge");
    const submissionFeedback = document.getElementById("submissionFeedback");

    function fetchMatchState() {
        fetch(`${baseUrl}/api/match/${matchId}/status`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
        .then((res) => res.json())
        .then((state) => {
            if (state.error) {
                console.error("Error devuelto por el servidor:", state.error);
                return;
            }

            if (state.status === "finished") {
                clearInterval(pollInterval);
                clearInterval(localTimerInterval);
                window.location.href = `${baseUrl}/game/match/${matchId}/results`;
                return;
            }

            processServerState(state);
        })
        .catch((err) => {
            console.error("Error en comunicación con la arena:", err);
        });
    }

    function processServerState(state) {
        // 1. Actualizar jugadores y puntuaciones
        if (state.players) {
            updatePlayerScores(state.players);
        }

        const q = state.question;
        if (!q) return;

        // Comprobar si cambio de ronda / pregunta
        if (q.order !== currentRoundOrder) {
            currentRoundOrder = q.order;
            currentMatchQuestionId = q.match_question_id;
            hasAnsweredCurrentRound = false;
            selectedOptionIdCurrent = null;
            isShowingResultPhase = false;

            // Ocultar caja de revelado
            if (revealBox) revealBox.classList.add("d-none");
            if (submissionFeedback) submissionFeedback.innerText = "";

            renderNewQuestion(q, state.total_questions);
        }

        // Actualizar temporizador visual
        const isRevealing = (state.status === "showing_result" || state.is_revealing);
        isShowingResultPhase = isRevealing;

        if (isRevealing) {
            // Fase de revelado
            const secsLeft = state.reveal_remaining_seconds !== undefined ? state.reveal_remaining_seconds : 0;
            if (timerDisplay) {
                timerDisplay.innerText = secsLeft;
                timerDisplay.className = "arena-timer text-warning";
            }
            if (revealCountdownBadge) {
                revealCountdownBadge.innerText = `Siguiente ronda en ${secsLeft}s`;
            }

            // Revelar alternativas correcta e incorrectas
            highlightAnswers(q.options, q.explanation, q.source_reference);
        } else {
            // Fase de respuesta activa
            const remaining = state.remaining_seconds !== undefined ? state.remaining_seconds : 0;
            if (timerDisplay) {
                timerDisplay.innerText = remaining;
                if (remaining <= 5) {
                    timerDisplay.className = "arena-timer text-danger";
                } else if (remaining <= 9) {
                    timerDisplay.className = "arena-timer text-warning";
                } else {
                    timerDisplay.className = "arena-timer text-success";
                }
            }

            // Si el jugador ya respondio pero aun no estamos en fase de revelado
            if (state.has_answered) {
                hasAnsweredCurrentRound = true;
                disableAllOptionButtons();
                if (submissionFeedback) {
                    submissionFeedback.innerHTML = '<span class="text-info"><i class="bi bi-clock me-1"></i> Respuesta registrada. Esperando a los demás participantes...</span>';
                }
            }
        }
    }

    function renderNewQuestion(q, totalQuestions) {
        if (categoryName) categoryName.innerText = q.category_name;
        if (categoryIcon && q.category_icon) {
            categoryIcon.className = `bi ${q.category_icon} me-1`;
        }
        if (difficultyBadge) {
            difficultyBadge.innerText = q.difficulty.toUpperCase();
            difficultyBadge.className = `badge px-3 py-2 rounded-pill bg-${q.difficulty === "facil" ? "success" : (q.difficulty === "intermedio" ? "warning text-dark" : "danger")}`;
        }
        if (questionCounter) {
            questionCounter.innerText = `Pregunta ${q.order} de ${totalQuestions}`;
        }
        if (questionText) {
            questionText.innerText = q.text;
        }

        // Renderizar opciones
        for (let i = 1; i <= 4; i++) {
            const btn = document.getElementById(`opt-btn-${i}`);
            const opt = q.options[i - 1];
            if (btn && opt) {
                btn.className = "option-button";
                btn.disabled = false;
                btn.setAttribute("data-option-id", opt.id);
                const textSpan = btn.querySelector(".opt-text");
                if (textSpan) {
                    textSpan.innerText = opt.option_text;
                }
            }
        }
    }

    function highlightAnswers(options, explanation, source) {
        disableAllOptionButtons();

        options.forEach((opt, index) => {
            const btn = document.getElementById(`opt-btn-${index + 1}`);
            if (!btn) return;

            btn.className = "option-button";

            if (Number(opt.is_correct) === 1) {
                btn.classList.add("correct");
            } else if (selectedOptionIdCurrent && Number(opt.id) === Number(selectedOptionIdCurrent)) {
                btn.classList.add("incorrect");
            }
        });

        // Mostrar caja de explicacion
        if (revealBox) {
            revealBox.classList.remove("d-none");
            if (revealExplanationText) {
                revealExplanationText.innerText = explanation || "Sin explicación adicional.";
            }
            if (revealSourceText) {
                revealSourceText.innerText = source ? `Fuente de consulta: ${source}` : "";
            }
        }
    }

    function updatePlayerScores(players) {
        players.forEach((p) => {
            const scoreEl = document.getElementById(`player-score-val-${p.user_id}`);
            const statusText = document.getElementById(`status-text-${p.user_id}`);

            if (scoreEl) {
                scoreEl.innerText = `${p.score} pts`;
            }

            if (statusText) {
                if (p.has_answered) {
                    statusText.innerHTML = '<span class="text-success"><i class="bi bi-check2-circle me-1"></i> Respondió</span>';
                } else {
                    statusText.innerHTML = '<span class="text-secondary"><i class="bi bi-hourglass-split me-1"></i> Pensando...</span>';
                }
            }
        });
    }

    function disableAllOptionButtons() {
        for (let i = 1; i <= 4; i++) {
            const btn = document.getElementById(`opt-btn-${i}`);
            if (btn) btn.disabled = true;
        }
    }

    window.selectOption = function(index) {
        if (hasAnsweredCurrentRound || isShowingResultPhase) return;

        const btn = document.getElementById(`opt-btn-${index}`);
        if (!btn) return;

        const optId = btn.getAttribute("data-option-id");
        selectedOptionIdCurrent = optId;
        hasAnsweredCurrentRound = true;

        btn.classList.add("selected");
        disableAllOptionButtons();

        if (submissionFeedback) {
            submissionFeedback.innerHTML = '<span class="text-muted"><i class="bi bi-arrow-repeat spin me-1"></i> Validando respuesta con el servidor...</span>';
        }

        // Enviar respuesta por Fetch API nativo
        fetch(`${baseUrl}/api/match/${matchId}/answer`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({
                match_question_id: currentMatchQuestionId,
                option_id: optId,
            }),
        })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                if (submissionFeedback) {
                    submissionFeedback.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Respuesta recibida. Aguardando a los demás jugadores...</span>';
                }
            } else {
                if (submissionFeedback) {
                    submissionFeedback.innerHTML = `<span class="text-danger">${data.message || "Error al enviar"}</span>`;
                }
            }
            // Actualizar estado de inmediato
            fetchMatchState();
        })
        .catch((err) => {
            console.error("Error al enviar respuesta:", err);
        });
    };

    // Iniciar consulta de estado de inmediato y luego cada 1 segundo
    fetchMatchState();
    pollInterval = setInterval(fetchMatchState, 1000);
});

