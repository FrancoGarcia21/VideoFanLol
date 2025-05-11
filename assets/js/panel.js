document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalConfirmacion');
    const btnConfirmar = document.getElementById('btnConfirmar');
    const btnCancelar = document.getElementById('btnCancelar');
    let urlEliminar = null;

    document.querySelectorAll('.btn-eliminar').forEach(boton => {
        boton.addEventListener('click', function (e) {
            e.preventDefault();
            urlEliminar = this.getAttribute('href');
            modal.classList.remove('hidden');
        });
    });

    btnConfirmar.addEventListener('click', function () {
        if (urlEliminar) {
            window.location.href = urlEliminar;
        }
    });

    btnCancelar.addEventListener('click', function () {
        modal.classList.add('hidden');
        urlEliminar = null;
    });
});
