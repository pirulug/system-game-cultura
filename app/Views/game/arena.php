<div class="py-3">
    <!-- Marcador de Jugadores en Vivo -->
    <div class="row g-2 mb-4" id="arenaPlayersRow">
        <?php foreach ($players as $p): ?>
            <div class="col" id="player-score-card-<?= (int)$p["user_id"] ?>">
                <div class="card-custom p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="avatar-circle me-2">
                            <?= strtoupper(substr($p["username"], 0, 1)) ?>
                        </span>
                        <div>
                            <div class="fw-bold small text-white">
                                <?= htmlspecialchars($p["username"]) ?>
                                <?php if ($match["mode"] === "2vs2"): ?>
                                    <span class="badge <?= $p["team"] === "A" ? "bg-primary" : "bg-danger" ?>" style="font-size: 0.65rem;">Eq <?= htmlspecialchars($p["team"]) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="small text-muted" id="status-text-<?= (int)$p["user_id"] ?>">
                                Esperando...
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fs-5 fw-extrabold text-warning" id="player-score-val-<?= (int)$p["user_id"] ?>">
                            <?= (int)$p["score"] ?> pts
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Arena Principal de la Pregunta -->
    <div class="card-custom p-4 p-md-5">
        <!-- Barra Superior de la Pregunta: Categoria, Orden y Temporizador -->
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fs-6" id="arenaCategoryBadge">
                    <i class="bi bi-book me-1" id="arenaCategoryIcon"></i> <span id="arenaCategoryName">Cargando...</span>
                </span>
                <span class="badge bg-secondary px-3 py-2 rounded-pill" id="arenaDifficultyBadge">Dificultad</span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="text-muted fw-bold" id="arenaQuestionCounter">Pregunta -- / --</span>
                <div class="p-2 px-3 bg-dark rounded border border-warning d-flex align-items-center gap-2">
                    <i class="bi bi-stopwatch text-warning fs-4"></i>
                    <span class="arena-timer" id="arenaTimerDisplay">15</span>
                </div>
            </div>
        </div>

        <!-- Enunciado de la Pregunta -->
        <div class="mb-4 text-center text-md-start">
            <h3 class="arena-question-text" id="arenaQuestionText">
                Cargando pregunta de cultura peruana...
            </h3>
        </div>

        <!-- 4 Alternativas -->
        <div class="row g-3 mb-4" id="optionsContainer">
            <div class="col-md-6">
                <button type="button" class="option-button" id="opt-btn-1" onclick="selectOption(1)" disabled>
                    <span class="fw-bold text-muted me-2">A.</span> <span class="opt-text">--</span>
                </button>
            </div>
            <div class="col-md-6">
                <button type="button" class="option-button" id="opt-btn-2" onclick="selectOption(2)" disabled>
                    <span class="fw-bold text-muted me-2">B.</span> <span class="opt-text">--</span>
                </button>
            </div>
            <div class="col-md-6">
                <button type="button" class="option-button" id="opt-btn-3" onclick="selectOption(3)" disabled>
                    <span class="fw-bold text-muted me-2">C.</span> <span class="opt-text">--</span>
                </button>
            </div>
            <div class="col-md-6">
                <button type="button" class="option-button" id="opt-btn-4" onclick="selectOption(4)" disabled>
                    <span class="fw-bold text-muted me-2">D.</span> <span class="opt-text">--</span>
                </button>
            </div>
        </div>

        <!-- Caja de Revelado de Respuesta y Explicacion -->
        <div id="revealBox" class="p-4 rounded bg-dark border border-secondary d-none">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="fw-bold text-white mb-0" id="revealTitle">
                    <i class="bi bi-info-circle-fill text-warning me-2"></i> Explicación Histórica y Cultural
                </h5>
                <span class="badge bg-warning text-dark" id="revealCountdownBadge">Siguiente en 4s</span>
            </div>
            <p class="text-light mb-2 fs-6" id="revealExplanationText">--</p>
            <div class="small text-muted" id="revealSourceText">--</div>
        </div>

        <!-- Mensaje de estado de envio -->
        <div class="text-center mt-3 text-muted small" id="submissionFeedback"></div>
    </div>
</div>

<script>
    window.MATCH_ID = <?= (int)$matchId ?>;
    window.CURRENT_USER_ID = <?= (int)$userId ?>;
    window.BASE_URL = "<?= htmlspecialchars($baseUrl) ?>";
</script>
<script src="<?= htmlspecialchars($baseUrl) ?>/js/game_arena.js"></script>

