document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const archivoInput = document.querySelector('input[type="file"]');
  const titulo = document.querySelector('input[name="titulo"]');
  const descripcion = document.querySelector('textarea[name="descripcion"]');
  const palabrasClave = document.querySelector('input[name="palabras_clave"]');
  const lugar = document.querySelector('input[name="lugar"]');
  const fechaGrabacion = document.querySelector('input[name="fecha_grabacion"]');

  const LIMITE_BYTES = 314572800; // 300 MB
  const LIMITE_SEGUNDOS = 300;    // 5 minutos

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // Evitamos envío hasta validar todo
    const errores = [];

    const archivo = archivoInput.files[0];
    if (!archivo) {
      errores.push("📂 Debes seleccionar un archivo de video.");
    } else {
      if (!archivo.name.toLowerCase().endsWith(".mp4")) {
        errores.push("❌ Solo se permiten archivos .mp4.");
      }

      if (archivo.size > LIMITE_BYTES) {
        errores.push("❌ El archivo supera los 300MB permitidos.");
      }
    }

    if (!titulo.value.trim()) errores.push("✏️ El título es obligatorio.");
    if (!descripcion.value.trim()) errores.push("📝 La descripción es obligatoria.");
    if (!palabrasClave.value.trim()) errores.push("🏷️ Las palabras clave son obligatorias.");
    if (!lugar.value.trim()) errores.push("📍 El lugar de grabación es obligatorio.");
    if (!fechaGrabacion.value) errores.push("📅 La fecha de grabación es obligatoria.");

    const claves = palabrasClave.value.split(',').map(p => p.trim()).filter(p => p !== "");
    if (claves.length > 10) {
      errores.push("🚫 Solo se permiten hasta 10 palabras clave.");
    }

    if (errores.length > 0) {
      mostrarMensaje(errores.join("\n")); // ✅ CAMBIO
      return;
    }

    // ⏱ Verificar duración del video con <video>
    const video = document.createElement("video");
    video.preload = "metadata";

    video.onloadedmetadata = function () {
      URL.revokeObjectURL(video.src);
      const duracion = video.duration;

      if (duracion > LIMITE_SEGUNDOS) {
        mostrarMensaje(`⏱ El video dura ${Math.round(duracion)} segundos. Solo se permiten hasta 5 minutos.`);
      } else {
        form.submit(); // ✅ Todo ok, enviar formulario
      }
    };

    video.onerror = function () {
      mostrarMensaje("⚠️ No se pudo leer la duración del video. Asegurate de que sea un archivo .mp4 válido.");
    };

    video.src = URL.createObjectURL(archivo);
  });
});
