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

    // Si hay errores, evitamos el envío y mostramos alert
    if (errores.length > 0) {
      e.preventDefault();
      alert("❌ Errores:\n\n" + errores.join("\n"));
    }
  });
});
