document.addEventListener("DOMContentLoaded", () => {
  const botonesVoto = document.querySelectorAll(".votar-btn");

  botonesVoto.forEach(boton => {
    boton.addEventListener("click", async () => {
      const videoId = boton.dataset.video;
      const tipo = boton.dataset.tipo;

      try {
        const respuesta = await fetch("../backend/votar.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `video_id=${videoId}&tipo=${tipo}`
        });

        const data = await respuesta.json();

        if (data.success) {
          document.getElementById("contador-likes").textContent = data.likes;
          document.getElementById("contador-dislikes").textContent = data.dislikes;
        } else {
          alert(data.message || "Ocurrió un error al votar.");
        }
      } catch (error) {
        console.error("Error en la petición AJAX:", error);
        alert("Error de red al votar.");
      }
    });
  });
});
