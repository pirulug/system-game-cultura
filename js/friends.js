// Logica del sistema de amigos y desafios en tiempo real

function sendFriendRequest(targetUserId) {
    const baseUrl = window.BASE_URL || "";
    fetch(`${baseUrl}/api/friends/request`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ user_id: targetUserId }),
    })
    .then((res) => res.json())
    .then((data) => {
        alert(data.message || (data.success ? "Solicitud enviada." : "Error al enviar solicitud."));
    })
    .catch((err) => {
        console.error("Error al enviar solicitud:", err);
    });
}

function respondFriendRequest(requestId, action) {
    const baseUrl = window.BASE_URL || "";
    fetch(`${baseUrl}/api/friends/respond`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ request_id: requestId, action: action }),
    })
    .then((res) => res.json())
    .then((data) => {
        const card = document.getElementById(`req-card-${requestId}`);
        if (card) {
            card.remove();
        }
        alert(data.message);
        window.location.reload();
    })
    .catch((err) => {
        console.error("Error al responder solicitud:", err);
    });
}

function removeFriend(friendId) {
    if (!confirm("¿Seguro que deseas eliminar a este usuario de tus amigos?")) return;
    const baseUrl = window.BASE_URL || "";
    fetch(`${baseUrl}/api/friends/remove`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ friend_id: friendId }),
    })
    .then((res) => res.json())
    .then((data) => {
        if (data.success) {
            const el = document.getElementById(`friend-box-${friendId}`);
            if (el) el.remove();
        }
    })
    .catch((err) => {
        console.error("Error al eliminar amigo:", err);
    });
}

