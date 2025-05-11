document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector(".formulario");

  form.addEventListener("submit", function (e) {
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const fechaNacimiento = document.getElementById("fecha_nacimiento").value;
    const pais = document.getElementById("pais").value.trim();

    const errores = [];

    // Validación de campos vacíos
    if (!username || !email || !password || !fechaNacimiento || !pais) {
      errores.push("Todos los campos son obligatorios.");
    }

    // Validación de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      errores.push("El correo electrónico no es válido.");
    }

    // Validación de fecha de nacimiento
    const hoy = new Date();
    const fechaNac = new Date(fechaNacimiento);
    if (fechaNac > hoy) {
      errores.push("La fecha de nacimiento no puede ser futura.");
    }

    // Validación de contraseña
    if (password.length < 6) {
      errores.push("La contraseña debe tener al menos 6 caracteres.");
    }

    // Validación de longitud mínima
    if (username.length < 3) {
      errores.push("El nombre de usuario debe tener al menos 3 caracteres.");
    }

    if (pais.length < 3) {
      errores.push("El país debe tener al menos 3 caracteres.");
    }

    // Mostrar errores con alerta estilizada
    if (errores.length > 0) {
      e.preventDefault();
      mostrarAlertaPersonalizada("Errores", errores.join("\n"), "❌");
    }
  });
});

// 🔔 Alerta custom reutilizable
function mostrarAlertaPersonalizada(titulo, mensaje, icono = "⚠️") {
  const modal = document.getElementById("customAlert");
  const titleEl = document.getElementById("customAlertTitle");
  const msgEl = document.getElementById("customAlertMessage");
  const iconEl = document.getElementById("customAlertIcon");
  const closeBtn = document.getElementById("customAlertClose");

  if (!modal || !titleEl || !msgEl || !iconEl || !closeBtn) {
    console.error("❌ Error: No se encontró el modal de alerta personalizado en el HTML.");
    return;
  }

  titleEl.textContent = titulo;
  msgEl.textContent = mensaje;
  iconEl.textContent = icono;

  modal.classList.remove("hidden");

  closeBtn.onclick = () => {
    modal.classList.add("hidden");
  };
}
