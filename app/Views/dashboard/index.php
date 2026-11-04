<div class="row g-4 mb-4">
    <!-- Banner de Perfil y Nivel -->
    <div class="col-12">
        <div class="card-custom p-4">
            <div class="row align-items-center">
                <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
                    <span class="avatar-circle avatar-lg">
                        <?= strtoupper(substr($profile["username"], 0, 1)) ?>
                    </span>
                </div>
                <div class="col-md">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1 justify-content-center justify-content-md-start">
                        <h3 class="fw-bold text-white mb-0"><?= htmlspecialchars($profile["full_name"] ?? $profile["username"]) ?></h3>
                        <span class="text-muted">(@<?= htmlspecialchars($profile["username"]) ?>)</span>
                        <span class="badge-level">Nivel <?= (int)$profile["level"] ?></span>
                        <span class="badge bg-secondary"><?= htmlspecialchars($profile["role"]) ?></span>
                    </div>
                    <div class="small text-muted mb-3 text-center text-md-start">
                        <?= !empty($profile["bio"]) ? htmlspecialchars($profile["bio"]) : "Estudiante y entusiasta de la historia peruana." ?>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Experiencia: <strong class="text-white"><?= number_format((int)$profile["experience_points"]) ?> XP</strong></span>
                            <span>Siguiente Nivel: <?= number_format($xpForNextLevel) ?> XP</span>
                        </div>
                        <div class="progress-xp">
                            <div class="progress-xp-bar" style="width: <?= $xpProgress ?>%;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-auto text-center text-md-end mt-3 mt-md-0 border-start-md border-secondary ps-md-4">
                    <div class="row g-2 text-center">
                        <div class="col-6 col-md-12 mb-md-2">
                            <div class="small text-muted">ELO 1 vs 1</div>
                            <span class="badge-elo fs-6"><?= (int)$profile["elo_1vs1"] ?> ELO</span>
                        </div>
                        <div class="col-6 col-md-12">
                            <div class="small text-muted">Puntos Totales</div>
                            <span class="badge bg-warning text-dark fw-bold fs-6"><?= number_format((int)$profile["total_points"]) ?> pts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 6 Accesos Destacados Obligatorios -->
<div class="mb-4">
    <h4 class="fw-bold text-white mb-3">Acciones Rápidas de Juego</h4>
    <div class="row g-3">
        <!-- 1 vs 1 -->
        <div class="col-md-4 col-sm-6">
            <a href="<?= htmlspecialchars($baseUrl) ?>/game/matchmaking?mode=1vs1" class="text-decoration-none">
                <div class="game-mode-card h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-danger-subtle text-danger rounded me-3 fs-4">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Jugar 1 vs 1</h5>
                            <span class="badge bg-danger">Competitivo ELO</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Emparejamiento automático rápido contra otro jugador disponible.</p>
                </div>
            </a>
        </div>

        <!-- 2 vs 2 -->
        <div class="col-md-4 col-sm-6">
            <a href="<?= htmlspecialchars($baseUrl) ?>/game/matchmaking?mode=2vs2" class="text-decoration-none">
                <div class="game-mode-card h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded me-3 fs-4">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Jugar 2 vs 2</h5>
                            <span class="badge bg-primary">Por Equipos</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Cuatro jugadores en dos bandos compitiendo por puntaje acumulado.</p>
                </div>
            </a>
        </div>

        <!-- Crear Sala -->
        <div class="col-md-4 col-sm-6">
            <a href="<?= htmlspecialchars($baseUrl) ?>/game/room/create" class="text-decoration-none">
                <div class="game-mode-card h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-warning-subtle text-warning rounded me-3 fs-4">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Crear Sala</h5>
                            <span class="badge bg-warning text-dark">Personalizada</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Configura tiempo, dificultad, categorías y genera un código de acceso.</p>
                </div>
            </a>
        </div>

        <!-- Unirse a Sala -->
        <div class="col-md-4 col-sm-6">
            <div class="card-custom p-3 h-100">
                <div class="d-flex align-items-center mb-2">
                    <div class="p-2 bg-success-subtle text-success rounded me-3 fs-4">
                        <i class="bi bi-door-open-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-white mb-0">Unirse a Sala</h5>
                        <span class="text-muted small">Por Código</span>
                    </div>
                </div>
                <form action="<?= htmlspecialchars($baseUrl) ?>/game/room/join" method="POST" class="mt-2">
                    <?= \App\Core\CSRF::field() ?>
                    <div class="input-group">
                        <input type="text" name="room_code" class="form-control form-control-sm bg-dark border-secondary text-white text-uppercase" placeholder="CÓDIGO (ej: A1B2)" required maxlength="8">
                        <button class="btn btn-sm btn-outline-success" type="submit">Entrar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ranking Global -->
        <div class="col-md-4 col-sm-6">
            <a href="<?= htmlspecialchars($baseUrl) ?>/ranking" class="text-decoration-none">
                <div class="game-mode-card h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-info-subtle text-info rounded me-3 fs-4">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Ranking Global</h5>
                            <span class="badge bg-info text-dark">Clasificación</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Consulta la tabla de honor nacional, rankings semanales y por categorías.</p>
                </div>
            </a>
        </div>

        <!-- Desafiar Amigos -->
        <div class="col-md-4 col-sm-6">
            <a href="<?= htmlspecialchars($baseUrl) ?>/friends" class="text-decoration-none">
                <div class="game-mode-card h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-secondary-subtle text-white rounded me-3 fs-4">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-0">Desafiar Amigos</h5>
                            <span class="badge bg-secondary">Social</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Revisa qué amigos están conectados y rétalos a un duelo directo.</p>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Estadisticas del Jugador -->
    <div class="col-lg-4">
        <div class="card-custom p-4 mb-4">
            <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                <i class="bi bi-bar-chart-fill text-danger me-2"></i> Mis Estadísticas
            </h5>
            <div class="row g-3 text-center">
                <div class="col-6">
                    <div class="p-3 bg-dark rounded border border-secondary">
                        <div class="fs-4 fw-bold text-white"><?= (int)$profile["matches_played"] ?></div>
                        <div class="small text-muted">Partidas Jugadas</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-dark rounded border border-secondary">
                        <div class="fs-4 fw-bold text-success"><?= (int)$profile["matches_won"] ?></div>
                        <div class="small text-muted">Victorias</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-dark rounded border border-secondary">
                        <div class="fs-4 fw-bold text-danger"><?= (int)$profile["matches_lost"] ?></div>
                        <div class="small text-muted">Derrotas</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-dark rounded border border-secondary">
                        <div class="fs-4 fw-bold text-warning"><?= (float)$profile["win_ratio"] ?>%</div>
                        <div class="small text-muted">Efectividad</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 bg-dark rounded border border-secondary d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Racha de Victorias:</span>
                        <span class="badge bg-danger fs-6"><i class="bi bi-fire me-1"></i><?= (int)$profile["win_streak"] ?> (Máx: <?= (int)$profile["max_win_streak"] ?>)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salas Publicas Disponibles -->
        <div class="card-custom p-4">
            <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                <i class="bi bi-broadcast text-warning me-2"></i> Salas en Espera
            </h5>
            <?php if (!empty($publicRooms)): ?>
                <div class="list-group list-group-flush bg-transparent">
                    <?php foreach ($publicRooms as $room): ?>
                        <div class="list-group-item bg-transparent text-white px-0 py-2 border-secondary d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small"><?= htmlspecialchars($room["name"]) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    Modo: <?= htmlspecialchars($room["mode"]) ?> &bull; Por: <?= htmlspecialchars($room["creator_username"]) ?>
                                </div>
                            </div>
                            <a href="<?= htmlspecialchars($baseUrl) ?>/game/room/<?= htmlspecialchars($room["room_code"]) ?>" class="btn btn-sm btn-outline-custom">
                                Unirse (<?= (int)$room["current_players"] ?>/<?= (int)$room["max_players"] ?>)
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted small text-center py-3 mb-0">No hay salas públicas abiertas en este momento.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Historial de Partidas Recientes -->
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <h5 class="fw-bold text-white mb-3 pb-2 border-bottom border-secondary">
                <i class="bi bi-clock-history text-info me-2"></i> Historial Reciente de Partidas
            </h5>
            <?php if (!empty($recentMatches)): ?>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Modalidad</th>
                                <th>Puntaje</th>
                                <th>Aciertos</th>
                                <th>Cambio ELO</th>
                                <th>XP</th>
                                <th>Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMatches as $m): ?>
                                <?php
                                $isWin = ($m["mode"] === "2vs2") ? ($m["team"] === $m["winner_team"]) : ($m["winner_user_id"] == $userId);
                                $isTie = ($m["winner_user_id"] === null && $m["winner_team"] === "tie");
                                ?>
                                <tr>
                                    <td class="small text-muted"><?= date("d/m/Y H:i", strtotime($m["finished_at"] ?? $m["created_at"])) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($m["mode"]) ?></span></td>
                                    <td class="fw-bold text-white"><?= (int)$m["score"] ?> pts</td>
                                    <td><?= (int)$m["correct_answers"] ?>/<?= (int)$m["total_questions"] ?></td>
                                    <td>
                                        <?php if ((int)$m["rating_change"] > 0): ?>
                                            <span class="text-success fw-bold">+<?= (int)$m["rating_change"] ?></span>
                                        <?php elseif ((int)$m["rating_change"] < 0): ?>
                                            <span class="text-danger fw-bold"><?= (int)$m["rating_change"] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-warning small">+<?= (int)$m["experience_gained"] ?> XP</td>
                                    <td>
                                        <?php if ($isWin): ?>
                                            <span class="badge bg-success">Victoria</span>
                                        <?php elseif ($isTie): ?>
                                            <span class="badge bg-warning text-dark">Empate</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Derrota</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-joystick text-secondary fs-1 mb-2"></i>
                    <p class="text-muted mb-3">Aún no has jugado ninguna partida en la plataforma.</p>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/game/modes" class="btn btn-peru">¡Jugar Mi Primera Partida!</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

