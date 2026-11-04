<div class="row align-items-center py-5">
    <div class="col-lg-7 text-center text-lg-start mb-5 mb-lg-0">
        <div class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill mb-3 fw-bold">
            <i class="bi bi-shield-check me-1"></i> Trivia Competitiva Bicentenario
        </div>
        <h1 class="display-3 fw-extrabold text-white mb-3" style="letter-spacing: -1px;">
            Pon a prueba tu conocimiento sobre el <span class="text-danger">Perú</span>
        </h1>
        <p class="lead text-muted mb-4 fs-5">
            Compite en tiempo real en duelos 1 vs 1, batallas estratégicas por equipos 2 vs 2 y salas privadas personalizadas. Domina la historia, geografía, gastronomía, tradiciones y patrimonio de nuestra patria.
        </p>
        <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
            <?php if (!empty($currentUser)): ?>
                <a href="<?= htmlspecialchars($baseUrl) ?>/game/modes" class="btn btn-peru btn-lg px-4 py-3">
                    <i class="bi bi-play-fill me-2 fs-5"></i> Iniciar Batalla
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/dashboard" class="btn btn-outline-custom btn-lg px-4 py-3">
                    <i class="bi bi-speedometer2 me-2"></i> Mi Dashboard
                </a>
            <?php else: ?>
                <a href="<?= htmlspecialchars($baseUrl) ?>/register" class="btn btn-peru btn-lg px-4 py-3">
                    <i class="bi bi-lightning-charge-fill me-2"></i> Comenzar a Jugar Gratis
                </a>
                <a href="<?= htmlspecialchars($baseUrl) ?>/login" class="btn btn-outline-custom btn-lg px-4 py-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                </a>
            <?php endif; ?>
            <a href="<?= htmlspecialchars($baseUrl) ?>/ranking" class="btn btn-gold btn-lg px-4 py-3">
                <i class="bi bi-trophy-fill me-2"></i> Ver Ranking
            </a>
        </div>

        <div class="row g-4 mt-4 pt-3 border-top border-secondary">
            <div class="col-4">
                <div class="fs-3 fw-bold text-white"><?= number_format($totalUsers) ?>+</div>
                <div class="small text-muted">Jugadores Registrados</div>
            </div>
            <div class="col-4">
                <div class="fs-3 fw-bold text-danger"><?= number_format($totalQuestions) ?>+</div>
                <div class="small text-muted">Preguntas Verificadas</div>
            </div>
            <div class="col-4">
                <div class="fs-3 fw-bold text-warning">18</div>
                <div class="small text-muted">Categorías Oficiales</div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary">
                <h5 class="fw-bold mb-0 text-white">
                    <i class="bi bi-award-fill text-warning me-2"></i> Mejores Jugadores ELO
                </h5>
                <span class="badge bg-secondary">Global 1 vs 1</span>
            </div>
            <?php if (!empty($topPlayers)): ?>
                <div class="list-group list-group-flush bg-transparent">
                    <?php foreach ($topPlayers as $idx => $player): ?>
                        <div class="list-group-item bg-transparent text-white px-0 py-3 border-secondary d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold fs-5 me-3 text-<?= $idx === 0 ? "warning" : ($idx === 1 ? "secondary" : ($idx === 2 ? "danger" : "muted")) ?>">
                                    #<?= $idx + 1 ?>
                                </span>
                                <span class="avatar-circle me-3">
                                    <?= strtoupper(substr($player["username"], 0, 1)) ?>
                                </span>
                                <div>
                                    <div class="fw-bold"><?= htmlspecialchars($player["username"]) ?></div>
                                    <div class="small text-muted">Nivel <?= (int)$player["level"] ?> &bull; <?= (int)$player["matches_won"] ?> victorias</div>
                                </div>
                            </div>
                            <span class="badge-elo"><?= (int)$player["elo_rating"] ?> ELO</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-4">No hay puntuaciones registradas aún.</p>
            <?php endif; ?>
            <div class="text-center mt-3 pt-2">
                <a href="<?= htmlspecialchars($baseUrl) ?>/ranking" class="small text-decoration-none text-warning fw-bold">
                    Ver tabla de clasificación completa <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-white mb-2">Modalidades de Competencia</h2>
        <p class="text-muted">Desarrollado para medir conocimiento y velocidad con autoridad de servidor</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-custom p-4 h-100 text-center">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle p-3 fs-3">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-2">Duelo 1 vs 1</h4>
                <p class="text-muted mb-4">
                    Emparejamiento competitivo por sistema ELO. 10 preguntas cronometradas con bonificación por velocidad de respuesta y desempate.
                </p>
                <div class="mt-auto">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/game/modes" class="btn btn-outline-custom w-100">Explorar Duelos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom p-4 h-100 text-center">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle p-3 fs-3">
                        <i class="bi bi-people-fill"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-2">Equipos 2 vs 2</h4>
                <p class="text-muted mb-4">
                    Cuatro participantes en dos bandos sincronizados. Puntuación combinada por equipo en marcador dinámico en tiempo real.
                </p>
                <div class="mt-auto">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/game/modes" class="btn btn-outline-custom w-100">Jugar en Equipo</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom p-4 h-100 text-center">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle p-3 fs-3">
                        <i class="bi bi-sliders"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-2">Salas Personalizadas</h4>
                <p class="text-muted mb-4">
                    Crea salas privadas con código de acceso. Define cantidad de preguntas, segundos por ronda, categorías específicas y nivel de dificultad.
                </p>
                <div class="mt-auto">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/game/room/create" class="btn btn-outline-custom w-100">Crear Sala</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-white mb-2">18 Categorías de Conocimiento</h2>
        <p class="text-muted">Un banco exhaustivo que abarca la riqueza cultural, científica y deportiva de la nación</p>
    </div>
    <div class="row g-3">
        <?php foreach ($categories as $cat): ?>
            <div class="col-md-4 col-lg-3 col-6">
                <div class="card-custom p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="bi <?= htmlspecialchars($cat["icon_class"]) ?> fs-3 text-danger mb-2"></i>
                    <div class="fw-bold text-white small"><?= htmlspecialchars($cat["name"]) ?></div>
                    <div class="text-muted small mt-1"><?= (int)$cat["questions_count"] ?> preguntas</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

