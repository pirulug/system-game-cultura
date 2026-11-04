<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary">
                <h3 class="fw-bold text-white mb-0">Importación Masiva de Preguntas (CSV)</h3>
                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/questions" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Preguntas
                </a>
            </div>

            <div class="alert alert-info border-info text-white mb-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> Formato de Columnas requerido en el archivo CSV:</h6>
                <code class="text-warning small d-block">category_id,question_text,difficulty,explanation,source_reference,option_1,option_2,option_3,option_4,correct_option_index</code>
                <div class="small mt-2">
                    Donde <code>correct_option_index</code> debe ser un número del 1 al 4, y <code>difficulty</code> puede ser: facil, intermedio, dificil o experto.
                </div>
            </div>

            <form action="<?= htmlspecialchars($baseUrl) ?>/admin/questions/import" method="POST" enctype="multipart/form-data">
                <?= \App\Core\CSRF::field() ?>

                <div class="mb-4">
                    <label for="csv_file" class="form-label text-white small fw-bold">Seleccionar Archivo CSV</label>
                    <input class="form-control bg-dark border-secondary text-white" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                </div>

                <button type="submit" class="btn btn-gold w-100 py-3 fw-bold">
                    <i class="bi bi-cloud-arrow-up me-2"></i> Procesar e Importar Preguntas
                </button>
            </form>
        </div>
    </div>
</div>

