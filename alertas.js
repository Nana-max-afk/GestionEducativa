// Función para confirmar antes de eliminar cualquier registro
function confirmarEliminar(id, archivoDestino) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer y podría afectar otros registros.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: '#fff',
        customClass: {
            popup: 'animated fadeInDown'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirecciona al archivo correspondiente pasando el ID por GET
            window.location.href = archivoDestino + "?eliminar=" + id;
        }
    });
}

// Función opcional por si querés mostrar alertas de éxito al recargar
document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        Swal.fire({
            icon: 'success',
            title: '¡Operación Exitosa!',
            text: 'Los datos se procesaron correctamente.',
            timer: 2000,
            showConfirmButton: false
        });
    } else if (urlParams.get('error') === 'SedeConAlumnos') {
        Swal.fire({
            icon: 'error',
            title: 'No se puede eliminar',
            text: 'Esta sede tiene alumnos o cursos vinculados actualmente.',
        });
    }
});