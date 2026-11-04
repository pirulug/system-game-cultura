<?php
$isEdit = ($question !== null);
$actionUrl = $isEdit ? $baseUrl . "/admin/questions/" . (int)$question["id"] . "/edit" : $baseUrl . "/admin/questions/create";
$options = $question["options"] ?? [
    ["option_text" => "", "is_correct" => 1],
    ["option_text" => "", "is_correct" => 0],
    ["option_text" => "", "is_correct" => 0],
    ["option_text" => "", "is_correct" => 0],
];
?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary">
                <h3 class="fw-bold text-white mb-0"><?= $isEdit ? "Editar Pregunta #" . (int)$question["id"] : "Crear Nueva Pregunta" ?></h3>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/questions" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Preguntas
                </a>
            </div>

            <form action="<?= htmlspecialchars($actionUrl) ?>" method="POST">
                <?= \App\Core\CSRF::field() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label text-white small fw-bold">Categoría</label>
                        <select class="form-select bg-dark border-secondary text-white" id="category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat["id"] ?>" <?= ($isEdit && (int)$question["category_id"] === (int)$cat["id"]) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($cat["name"]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="difficulty" class="form-label text-white small fw-bold">Nivel de Dificultad</label>
                        <select class="form-select bg-dark border-secondary text-white" id="difficulty" name="difficulty">
                            <option value="facil" <?= ($isEdit && $question["difficulty"] === "facil") ? "selected" : "" ?>>Fácil</option>
                            <option value="intermedio" <?= (!$isEdit || $question["difficulty"] === "intermedio") ? "selected" : "" ?>>Intermedio</option>
                            <option value="dificil" <?= ($isEdit && $question["difficulty"] === "dificil") ? "selected" : "" ?>>Difícil</option>
                            <option value="experto" <?= ($isEdit && $question["difficulty"] === "experto") ? "selected" : "" ?>>Experto</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="question_text" class="form-label text-white small fw-bold">Enunciado de la Pregunta</label>
                    <textarea class="form-control bg-dark border-secondary text-white" id="question_text" name="question_text" rows="3" required placeholder="Ej: ¿En qué departamento del Perú se encuentra la ciudadela sagrada de Caral?"><?= htmlspecialchars($question["question_text"] ?? "") ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white small fw-bold mb-2">Cuatro Alternativas (Marca el selector de la respuesta correcta)</label>
                    <div class="row g-3">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <?php
                            $opt = $options[$i - 1] ?? ["option_text" => "", "is_correct" => ($i === 1)];
                            $isCorr = !empty($opt["is_correct"]);
                            ?>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-white fw-bold">
                                        <input type="radio" name="correct_option" value="<?= $i ?>" <?= $isCorr ? "checked" : "" ?> class="form-check-input mt-0 me-2" title="Marcar como correcta">
                                        <?= chr(64 + $i) ?>.
                                    </span>
                                    <input type="text" name="option_<?= $i ?>" class="form-control bg-dark border-secondary text-white" placeholder="Alternativa <?= $i ?>" required value="<?= htmlspecialchars($opt["option_text"] ?? "") ?>">
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="explanation" class="form-label text-white small fw-bold">Explicación Didáctica de la Respuesta</label>
                    <textarea class="form-control bg-dark border-secondary text-white" id="explanation" name="explanation" rows="2" required placeholder="Explicación histórica, geográfica o cultural detallada"><?= htmlspecialchars($question["explanation"] ?? "") ?></textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label for="source_reference" class="form-label text-white small fw-bold">Fuente o Referencia Bibliográfica</label>
                        <input type="text" class="form-control bg-dark border-secondary text-white" id="source_reference" name="source_reference" placeholder="Ej: Instituto Geográfico Nacional / Basadre" value="<?= htmlspecialchars($question["source_reference"] ?? "") ?>">
                    </div>
                    <?php if ($isEdit): ?>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check form-switch p-3 bg-dark rounded border border-secondary w-100">
                                <input class="form-check-input ms-0 me-3" type="checkbox" id="is_active" name="is_active" value="1" <?= ((int)$question["is_active"] === 1) ? "checked" : "" ?>>
                                <label class="form-check-label text-white small fw-bold" for="is_active">Pregunta Activa</label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-peru w-100 py-3 fw-bold">
                    <i class="bi bi-save me-2"></i> <?= $isEdit ? "Guardar Modificaciones" : "Registrar Pregunta en el Banco" ?>
                </button>
            </form>
        </div>
    </div>
</div>

