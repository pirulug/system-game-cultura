<div class="row justify-content-center py-4">
    <div class="col-lg-8">
        <div class="card-custom p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary">
                <div>
                    <h3 class="fw-bold text-white mb-1">Crear Sala Personalizada</h3>
                    <p class="text-muted small mb-0">Configura las reglas de la partida para jugar con tus amigos</p>
                </div>
                <a href="<?= htmlspecialchars($baseUrl) ?>/game/modes" class="btn btn-outline-custom">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>

            <form action="<?= htmlspecialchars($baseUrl) ?>/game/room/create" method="POST">
                <?= \App\Core\CSRF::field() ?>

                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label for="name" class="form-label text-white small fw-bold">Nombre de la Sala</label>
                        <input type="text" class="form-control bg-dark border-secondary text-white" id="name" name="name" required value="Sala de <?= htmlspecialchars($currentUser["username"]) ?>" maxlength="80">
                    </div>
                    <div class="col-md-4">
                        <label for="mode" class="form-label text-white small fw-bold">Modalidad</label>
                        <select class="form-select bg-dark border-secondary text-white" id="mode" name="mode">
                            <option value="1vs1">Duelo 1 vs 1 (2 Jugadores)</option>
                            <option value="2vs2">Equipos 2 vs 2 (4 Jugadores)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="questions_count" class="form-label text-white small fw-bold">Número de Preguntas</label>
                        <select class="form-select bg-dark border-secondary text-white" id="questions_count" name="questions_count">
                            <option value="5">5 Preguntas (Rápida)</option>
                            <option value="10" selected>10 Preguntas (Estándar)</option>
                            <option value="15">15 Preguntas (Extendida)</option>
                            <option value="20">20 Preguntas (Maratón)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="seconds_per_question" class="form-label text-white small fw-bold">Tiempo por Pregunta</label>
                        <select class="form-select bg-dark border-secondary text-white" id="seconds_per_question" name="seconds_per_question">
                            <option value="10">10 Segundos (Ultra Rápido)</option>
                            <option value="15" selected>15 Segundos (Estándar)</option>
                            <option value="20">20 Segundos (Moderado)</option>
                            <option value="30">30 Segundos (Reflexivo)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label text-white small fw-bold">Categoría de Preguntas</label>
                        <select class="form-select bg-dark border-secondary text-white" id="category_id" name="category_id">
                            <option value="0">Todas las Categorías (Aleatorio Mixto)</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat["id"] ?>"><?= htmlspecialchars($cat["name"]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="difficulty" class="form-label text-white small fw-bold">Nivel de Dificultad</label>
                        <select class="form-select bg-dark border-secondary text-white" id="difficulty" name="difficulty">
                            <option value="all">Todas las Dificultades</option>
                            <option value="facil">Fácil</option>
                            <option value="intermedio" selected>Intermedio</option>
                            <option value="dificil">Difícil</option>
                            <option value="experto">Experto</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-check form-switch p-3 bg-dark rounded border border-secondary">
                            <input class="form-check-input ms-0 me-3" type="checkbox" id="is_private" name="is_private" value="1">
                            <label class="form-check-label text-white fw-bold small" for="is_private">
                                Sala Privada (Solo accesible con código)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch p-3 bg-dark rounded border border-secondary">
                            <input class="form-check-input ms-0 me-3" type="checkbox" id="allow_spectators" name="allow_spectators" value="1">
                            <label class="form-check-label text-white fw-bold small" for="allow_spectators">
                                Permitir Espectadores
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold w-100 py-3 fw-bold fs-6">
                    <i class="bi bi-door-open-fill me-2"></i> Crear Sala y Abrir Lobby
                </button>
            </form>
        </div>
    </div>
</div>

