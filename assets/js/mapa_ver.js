document.addEventListener("DOMContentLoaded", function () {
  const mapaDiv = document.getElementById("mapa");
  if (!mapaDiv) return;

  const lat = parseFloat(mapaDiv.dataset.lat);
  const lng = parseFloat(mapaDiv.dataset.lng);

  const mapa = L.map("mapa").setView([lat, lng], 10);

  L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png", {
    attribution: '© OpenTopoMap & contributors',
    maxZoom: 17
  }).addTo(mapa);

  L.marker([lat, lng]).addTo(mapa).bindPopup("Lugar de grabación").openPopup();
});
