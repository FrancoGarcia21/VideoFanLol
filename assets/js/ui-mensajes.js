
  function mostrarMensaje(texto, tipo = "error") {
    const mensaje = document.createElement("div");
    mensaje.className = "mensaje-flotante";
    if (tipo === "ok") mensaje.classList.add("ok");
    mensaje.textContent = texto;
  
    document.body.appendChild(mensaje);
  
    setTimeout(() => {
      mensaje.remove();
    }, 4000);
  }
  