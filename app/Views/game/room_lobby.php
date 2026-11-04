<div class="row justify-content-center py-4">
    <div class="col-lg-9">
        <div class="card-custom p-4 p-md-5">
            <!-- Encabezado de la Sala -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="fw-bold text-white mb-0"><?= htmlspecialchars($room["name"]) ?></h3>
                        <span class="badge bg-secondary"><?= htmlspecialchars($room["mode"]) ?></span>
                    </div>
                    <div class="text-muted small mt-1">
                        Reglas: <?= (int)$room["questions_count"] ?> preguntas &bull; <?= (int)$room["seconds_per_question"] ?>s por pregunta &bull; Dificultad: <?= htmlspecialchars($room["difficulty"]) ?>
                    </div>
                </div>

                <div class="text-center p-3 bg-dark rounded border border-warning">
                    <div class="small text-muted mb-1">CÓDIGO DE ACCESO</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-4 fw-extrabold text-warning tracking-wide" id="roomCodeText"><?= htmlspecialchars($room["room_code"]) ?></span>
                        <button class="btn btn-sm btn-outline-warning" onclick="copyRoomCode()" title="Copiar código">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Jugadores en Sala -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-white mb-0">
                        <i class="bi bi-people-fill text-danger me-2"></i> Participantes (<span id="playerCount"><?= count($players) ?></span>/<?= (int)$room["max_players"] ?>)
                    </h5>
                    <?php if ($room["mode"] === "2vs2"): ?>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="changeMyTeam('A')">Pasar a Equipo A</button>
                            <button type="button" class="btn btn-outline-danger" onclick="changeMyTeam('B')">Pasar a Equipo B</button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row g-3" id="playerSlotsContainer">
                    <?php foreach ($players as $p): ?>
                        <div class="col-md-6" id="slot-user-<?= (int)$p["user_id"] ?>">
                            <div class="p-3 bg-dark rounded border border-secondary d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="avatar-circle me-3">
                                        <?= strtoupper(substr($p["username"], 0, 1)) ?>
                                    </span>
                                    <div>
                                        <div class="fw-bold text-white">
                                            <?= htmlspecialchars($p["username"]) ?>
                                            <?php if ((int)$p["is_host"] === 1): ?>
                                                <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7rem;">Anfitrión</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="small text-muted">
                                            Nivel <?= (int)$p["level"] ?> &bull; <?= (int)($p["elo_rating"] ?? 1200) ?> ELO
                                            <?php if ($room["mode"] === "2vs2"): ?>
                                                &bull; <strong class="<?= $p["team"] === "A" ? "text-primary" : "text-danger" ?>">Equipo <?= htmlspecialchars($p["team"]) ?></strong>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <?php if ((int)$p["is_ready"] === 1): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Listo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Esperando...</span>
                                    <?php endif; ?>

                                    <?php if ($isHost && (int)$p["user_id"] !== $currentUserId): ?>
                                        <button class="btn btn-sm btn-outline-danger" onclick="kickPlayer(<?= (int)$p['user_id'] ?>)" title="Expulsar">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Botones de Accion del Lobby -->
            <div class="pt-3 border-top border-secondary d-flex flex-wrap justify-content-between align-items-center gap-3">
                <a href="<?= htmlspecialchars($baseUrl) ?>/dashboard" class="btn btn-outline-custom">
                    <i class="bi bi-door-closed me-1"></i> Salir de la Sala
                </a>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success px-4" id="btnToggleReady" onclick="toggleMyReady()">
                        <i class="bi bi-check2-circle me-1"></i> Cambiar Estado Listo
                    </button>

                    <?php if ($isHost): ?>
                        <button type="button" class="btn btn-peru px-4 fw-bold" id="btnStartGame" onclick="startHostGame()">
                            <i class="bi bi-play-fill me-1 fs-5"></i> Iniciar Batalla
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.ROOM_CODE = "<?= htmlspecialchars($room["room_code"]) ?>";
    window.ROOM_ID = <?= (int)$room["id"] ?>;
    window.CURRENT_USER_ID = <?= (int)$currentUserId ?>;
    window.IS_HOST = <?= $isHost ? "true" : "false" ?>;
    window.BASE_URL = "<?= htmlspecialchars($baseUrl) ?>";
</script>
<script src="<?= htmlspecialchars($baseUrl) ?>/js/game_lobby.js"></script>

