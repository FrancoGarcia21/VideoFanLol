document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const archivoInput = document.querySelector('input[type="file"]');
    const titulo = document.querySelector('input[name="titulo"]');
    const descripcion = document.querySelector('textarea[name="descripcion"]');
    const palabrasClave = document.querySelector('input[name="palabras_clave"]');
    const lugar = document.querySelector('input[name="lugar"]');
    const fechaGrabacion = document.querySelector('input[name="fecha_grabacion"]');
  
    const LIMITE_BYTES = 314572800; // 300 MB
  
    form.addEventListener("submit", function (e) {
      let errores = [];
  
      // Verificar archivo
      const archivo = archivoInput.files[0];
      if (!archivo) {
        errores.push("📂 Debes seleccionar un archivo de video.");
      } else {
        if (!archivo.name.endsWith(".mp4")) {
          errores.push("❌ Solo se permiten archivos .mp4.");
        }
  
        if (archivo.size > LIMITE_BYTES) {
          errores.push("❌ El archivo supera los 300MB permitidos.");
        }
      }
  
      // Validar campos vacíos
      if (!titulo.value.trim()) errores.push("✏️ El título es obligatorio.");
      if (!descripcion.value.trim()) errores.push("📝 La descripción es obligatoria.");
      if (!palabrasClave.value.trim()) errores.push("🏷️ Las palabras clave son obligatorias.");
      if (!lugar.value.trim()) errores.push("📍 El lugar de grabación es obligatorio.");
      if (!fechaGrabacion.value) errores.push("📅 La fecha de grabación es obligatoria.");
  
      // Validar cantidad de palabras clave
      const claves = palabrasClave.value.split(',').map(p => p.trim()).filter(p => p !== "");
      if (claves.length > 10) {
        errores.push("🚫 Solo se permiten hasta 10 palabras clave.");
      }
  
      // Si hay errores, no envía el formulario y los muestra
      if (errores.length > 0) {
        alert(errores.join("\n"));
        e.preventDefault();
      }
    });
  });
  