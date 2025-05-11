document.addEventListener("DOMContentLoaded", function () {
  const mapa = L.map('mapa').setView([-40, -63], 4);

  L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenTopoMap & contributors',
    maxZoom: 17
  }).addTo(mapa);

  const latInput = document.getElementById("latitud");
  const lngInput = document.getElementById("longitud");

  let lat = parseFloat(latInput?.value);
  let lng = parseFloat(lngInput?.value);
  let marcador = null;

  // Si ya hay coordenadas (editar.php), marcarlas
  if (!isNaN(lat) && !isNaN(lng)) {
    marcador = L.marker([lat, lng]).addTo(mapa);
    mapa.setView([lat, lng], 7);
  }

  mapa.on("click", function (e) {
    const { lat, lng } = e.latlng;

    if (marcador) {
      marcador.setLatLng([lat, lng]);
    } else {
      marcador = L.marker([lat, lng]).addTo(mapa);
    }

    latInput.value = lat;
    lngInput.value = lng;
  });
});
