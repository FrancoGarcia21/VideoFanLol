document.addEventListener("DOMContentLoaded", function () {
  const mapa = L.map('mapa').setView([-40, -63], 4);

  // 🌍 OpenTopoMap con nombres geográficos (incluye Malvinas)
  L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenTopoMap & contributors',
    maxZoom: 17
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
});
