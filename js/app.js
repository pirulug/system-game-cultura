// Utilidades globales de JavaScript Vanilla

function getCSRFToken() {
    const metaTag = document.querySelector('input[name="csrf_token"]');
    return metaTag ? metaTag.value : "";
}

function copyRoomCode() {
    const codeEl = document.getElementById("roomCodeText");
    if (!codeEl) return;
    const text = codeEl.innerText.trim();
    navigator.clipboard.writeText(text).then(() => {
        alert("Código de sala copiado al portapapeles: " + text);
    }).catch(() => {
        prompt("Copia el código manualmente:", text);
    });
}

