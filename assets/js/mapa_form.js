document.addEventListener("DOMContentLoaded", function () {
    // Asegurar que exista el contenedor del mapa
    const contenedorMapa = document.getElementById("mapa");
    if (!contenedorMapa) return;
  
    // Cargar Leaflet
    const leafletCss = document.createElement("link");
    leafletCss.rel = "stylesheet";
    leafletCss.href = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
    document.head.appendChild(leafletCss);
  
    const leafletScript = document.createElement("script");
    leafletScript.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
    leafletScript.onload = inicializarMapa;
    document.body.appendChild(leafletScript);
  
    function inicializarMapa() {
      const mapa = L.map("mapa").setView([-40, -64], 4); // vista general de Argentina
  
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
      }).addTo(mapa);
  
      let marcador;
  
      mapa.on("click", function (e) {
        const { lat, lng } = e.latlng;
  
        if (marcador) {
          marcador.setLatLng([lat, lng]);
        } else {
          marcador = L.marker([lat, lng]).addTo(mapa);
        }
  
        document.getElementById("latitud").value = lat;
        document.getElementById("longitud").value = lng;
      });
    }
  });
  