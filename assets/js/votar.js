document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".votar-btn").forEach(btn => {
      btn.addEventListener("click", function(e) {
        e.preventDefault();
  
        const tipo = this.dataset.tipo;
        const videoId = this.dataset.video;
  
        fetch("../backend/me_gusta.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `video_id=${videoId}&tipo=${tipo}`
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById("contador-likes").textContent = data.likes;
            document.getElementById("contador-dislikes").textContent = data.dislikes;
          } else {
            alert(data.message || "Error al votar");
          }
        });
      });
    });
  });
  