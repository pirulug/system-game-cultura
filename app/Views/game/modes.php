<div class="py-4">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary">
        <div>
            <h2 class="fw-bold text-white mb-1">Modalidades de Juego</h2>
            <p class="text-muted small mb-0">Selecciona el formato en el que deseas poner a prueba tu conocimiento</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl) ?>/dashboard" class="btn btn-outline-custom">
            <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
        </a>
    </div>

    <div class="row g-4">
        <!-- 1 vs 1 Duelo Individual -->
        <div class="col-lg-4 col-md-6">
            <div class="card-custom p-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="p-3 bg-danger-subtle text-danger rounded-circle fs-3">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </span>
                    <span class="badge bg-danger">Competitivo ELO</span>
                </div>
                <h4 class="fw-bold text-white mb-2">Duelo 1 vs 1</h4>
                <p class="text-muted small mb-3">
                    Enfréntate cara a cara a otro jugador seleccionado por nivel de habilidad. Ambos responderán 10 preguntas idénticas con tiempo límite y bonificación por rapidez.
                </p>
                <ul class="list-unstyled small text-muted mb-4 border-top border-secondary pt-3">
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>10 preguntas por partida</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>15 segundos por pregunta</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Puntos + bono de velocidad</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Clasificación oficial ELO</li>
                </ul>
                <div class="mt-auto">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/game/matchmaking?mode=1vs1" class="btn btn-peru w-100 py-2">
                        <i class="bi bi-search me-2"></i> Buscar Oponente 1 vs 1
                    </a>
                </div>
            </div>
        </div>

        <!-- 2 vs 2 Competencia por Equipos -->
        <div class="col-lg-4 col-md-6">
            <div class="card-custom p-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="p-3 bg-primary-subtle text-primary rounded-circle fs-3">
                        <i class="bi bi-people-fill"></i>
                    </span>
                    <span class="badge bg-primary">Modo Cooperativo</span>
                </div>
                <h4 class="fw-bold text-white mb-2">Equipos 2 vs 2</h4>
                <p class="text-muted small mb-3">
                    Cuatro jugadores divididos en Equipo A y Equipo B. Los puntos de ambos integrantes se acumulan en un marcador conjunto en tiempo real.
                </p>
                <ul class="list-unstyled small text-muted mb-4 border-top border-secondary pt-3">
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Equipo A (2) vs Equipo B (2)</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Puntuación acumulativa de equipo</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Marcador en vivo sincronizado</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Ajuste de rating ponderado</li>
                </ul>
                <div class="mt-auto">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/game/matchmaking?mode=2vs2" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-people me-2"></i> Buscar Partida 2 vs 2
                    </a>
                </div>
            </div>
        </div>

        <!-- Salas Privadas Personalizadas -->
        <div class="col-lg-4 col-md-12">
            <div class="card-custom p-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="p-3 bg-warning-subtle text-warning rounded-circle fs-3">
                        <i class="bi bi-sliders"></i>
                    </span>
                    <span class="badge bg-warning text-dark">Personalizado</span>
                </div>
                <h4 class="fw-bold text-white mb-2">Salas Privadas</h4>
                <p class="text-muted small mb-3">
                    Crea una sala personalizada e invita a tus amigos mediante un código de acceso exclusivo. Tú tienes el control total de las reglas y la dificultad.
                </p>
                <ul class="list-unstyled small text-muted mb-4 border-top border-secondary pt-3">
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Código único de acceso</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Selección de categorías y dificultad</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Configuración de tiempo y preguntas</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Administración y expulsión por anfitrión</li>
                </ul>
                <div class="mt-auto">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/game/room/create" class="btn btn-gold w-100 py-2">
                        <i class="bi bi-plus-circle me-2"></i> Configurar y Crear Sala
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

