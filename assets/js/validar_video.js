document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const archivoInput = document.querySelector('input[type="file"]');
  const titulo = document.querySelector('input[name="titulo"]');
  const descripcion = document.querySelector('textarea[name="descripcion"]');
  const fechaGrabacion = document.querySelector('input[name="fecha_grabacion"]');
  const checkboxesClaves = document.querySelectorAll('input[name="palabras_clave[]"]');

  // Límites de usuario normal y super_pop
  const LIMITES = {
    normal: { bytes: 314572800, segundos: 300 },    // 300MB, 5min
    super: { bytes: 524288000, segundos: 600 }       // 500MB, 10min
  };

  // ✅ Consulta si el usuario es super_pop (asincrónica)
  async function esSuperPop() {
    try {
      const response = await fetch("/VideoFanLol/backend/es_super_pop.php");
      const data = await response.json();
      return data.super_pop === true;
    } catch (e) {
      console.error("Error consultando super_pop:", e);
      return false; // por defecto, no super_pop
    }
  }

  // ✅ Evento de envío del formulario
  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    const errores = [];

    const archivo = archivoInput.files[0];
    const superPop = await esSuperPop();
    const limite = superPop ? LIMITES.super : LIMITES.normal;

    console.log(superPop ? "🌟 Sos super pop" : "👤 Usuario normal");

    // 🧪 Validaciones generales
    if (!archivo) {
      errores.push("📂 Debes seleccionar un archivo de video.");
    } else {
      const nombreArchivo = archivo.name.toLowerCase();
      if (!nombreArchivo.endsWith(".mp4")) {
        errores.push("❌ Solo se permiten archivos .mp4.");
      }
      if (archivo.size > limite.bytes) {
        errores.push(`❌ El archivo pesa ${(archivo.size / 1048576).toFixed(1)}MB. El máximo permitido es ${limite.bytes / 1048576}MB.`);
      }
    }

    if (!titulo.value.trim()) errores.push("✏️ El título es obligatorio.");
    if (!descripcion.value.trim()) errores.push("📝 La descripción es obligatoria.");
    if (!fechaGrabacion.value) errores.push("📅 La fecha de grabación es obligatoria.");

    const seleccionadas = Array.from(checkboxesClaves).filter(cb => cb.checked);
    if (seleccionadas.length === 0) errores.push("🏷️ Debes seleccionar al menos una palabra clave.");
    if (seleccionadas.length > 10) errores.push("🚫 Máximo 10 palabras clave.");

    if (errores.length > 0) {
      mostrarMensaje(errores.join("\n"));
      return;
    }

    // ✅ Verificar duración del video (usando <video>)
    const video = document.createElement("video");
    video.preload = "metadata";

    video.onloadedmetadata = function () {
      URL.revokeObjectURL(video.src);
      const duracion = video.duration;

      if (duracion > limite.segundos) {
        const minutos = Math.floor(duracion / 60);
        const segundos = Math.round(duracion % 60);
        mostrarMensaje(`⏱ El video dura ${minutos}m ${segundos}s. Máximo permitido: ${limite.segundos / 60} minutos.`);
      } else {
        form.submit(); // ✅ Todo validado
      }
    };

    video.onerror = function () {
      mostrarMensaje("⚠️ No se pudo leer la duración del video. Asegurate de que sea un archivo .mp4 válido.");
    };

    video.src = URL.createObjectURL(archivo);
  });
});

// Mostrar mensaje visual si es super pop
esSuperPop().then((es) => {
  if (es) {
    const contenedor = document.getElementById("estado-superpop");
    contenedor.textContent = "🌟 ¡Sos un usuario super pop! Podés subir videos de hasta 10 minutos y 500MB.";
    contenedor.classList.add("ok");
  }
});
