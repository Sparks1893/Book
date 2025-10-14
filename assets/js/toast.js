function showToast(message, type = "info") {
    // Create toast container if it doesn't exist
    let container = document.getElementById("plm-toast-container");
    if (!container) {
        container = document.createElement("div");
        container.id = "plm-toast-container";
        document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement("div");
    toast.className = `plm-toast plm-toast-${type}`;
    toast.textContent = message;

    // Add and animate
    container.appendChild(toast);
    setTimeout(() => toast.classList.add("show"), 100);

    // Remove toast after 4s
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
