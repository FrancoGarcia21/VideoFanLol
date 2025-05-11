document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const archivoInput = document.querySelector('input[type="file"]');
  const titulo = document.querySelector('input[name="titulo"]');
  const descripcion = document.querySelector('textarea[name="descripcion"]');
  const fechaGrabacion = document.querySelector('input[name="fecha_grabacion"]');
  const checkboxesClaves = document.querySelectorAll('input[name="palabras_clave[]"]');

  const LIMITE_BYTES_NORMAL = 314572800; // 300 MB
  const LIMITE_SEGUNDOS_NORMAL = 300;    // 5 minutos

  const LIMITE_BYTES_SUPER = 524288000;  // 500 MB
  const LIMITE_SEGUNDOS_SUPER = 600;     // 10 minutos

  async function esSuperPop() {
    try {
      const res = await fetch("../backend/es_super_pop.php");
      const data = await res.json();
      return data.super_pop === true;
    } catch (e) {
      console.error("Error verificando super_pop:", e);
      return false; // por defecto, no es super_pop si falla
    }
  }

  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    const errores = [];

    const archivo = archivoInput.files[0];
    const esSuper = await esSuperPop();
    const limiteBytes = esSuper ? LIMITE_BYTES_SUPER : LIMITE_BYTES_NORMAL;
    const limiteSegundos = esSuper ? LIMITE_SEGUNDOS_SUPER : LIMITE_SEGUNDOS_NORMAL;

    if (!archivo) {
      errores.push("📂 Debes seleccionar un archivo de video.");
    } else {
      if (!archivo.name.toLowerCase().endsWith(".mp4")) {
        errores.push("❌ Solo se permiten archivos .mp4.");
      }

      if (archivo.size > limiteBytes) {
        errores.push(`❌ El archivo supera el tamaño permitido (${limiteBytes / 1048576}MB).`);
      }
    }

    if (!titulo.value.trim()) errores.push("✏️ El título es obligatorio.");
    if (!descripcion.value.trim()) errores.push("📝 La descripción es obligatoria.");
    if (!fechaGrabacion.value) errores.push("📅 La fecha de grabación es obligatoria.");

    // Palabras clave seleccionadas
    const seleccionadas = Array.from(checkboxesClaves).filter(cb => cb.checked);
    if (seleccionadas.length === 0) {
      errores.push("🏷️ Debes seleccionar al menos una palabra clave.");
    } else if (seleccionadas.length > 10) {
      errores.push("🚫 Solo se permiten hasta 10 palabras clave.");
    }

    if (errores.length > 0) {
      mostrarMensaje(errores.join("\n"));
      return;
    }

    // ⏱ Verificar duración del video con <video>
    const video = document.createElement("video");
    video.preload = "metadata";

    video.onloadedmetadata = function () {
      URL.revokeObjectURL(video.src);
      const duracion = video.duration;

      if (duracion > limiteSegundos) {
        mostrarMensaje(`⏱ El video dura ${Math.round(duracion)} segundos. Solo se permiten hasta ${limiteSegundos / 60} minutos.`);
      } else {
        form.submit(); // ✅ Todo OK
      }
    };

    video.onerror = function () {
      mostrarMensaje("⚠️ No se pudo leer la duración del video. Asegurate de que sea un archivo .mp4 válido.");
    };

    video.src = URL.createObjectURL(archivo);
  });
});
