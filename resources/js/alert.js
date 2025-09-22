document.addEventListener("DOMContentLoaded", function () {
    if (window.laravelFlash) {
        const { type, message } = window.laravelFlash;

        Swal.fire({
            icon: type,
            title: message,
            showConfirmButton: type === "success" ? false : true,
            timer: type === "success" ? 2000 : undefined,
        });
    }
});
