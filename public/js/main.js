/* ED.RENT - Daftar Kendaraan
   - Toggle navbar ditangani Bootstrap Collapse (data-bs-toggle="collapse"),
     termasuk pembaruan aria-expanded dan transisi tinggi (lihat CSS).
   - Kartu kendaraan dirender dari data di bawah. Ganti dengan data API bila sudah tersedia. */
(function () {
  'use strict';

  var assetsBase = (document.body.dataset.assetsBase || '').replace(/\/$/, '');

  var vehicles = [];
  for (var i = 0; i < 12; i++) {
    vehicles.push({
      name: 'Vega Force',
      type: 'Matic',
      plate: 'AB 9001 DE',
      status: 'Tersedia',
      price: 75000,
      image: '/vehicles/vega-force.png',
      alt: 'Motor Vega Force matic berwarna hitam dengan aksen hijau, tampak samping'
    });
  }

  function formatRupiah(value) {
    return 'Rp' + value.toLocaleString('id-ID') + '/';
  }

  function renderVehicles() {
    var grid = document.getElementById('vehicleGrid');
    var tpl = document.getElementById('vehicleCardTemplate');
    if (!grid || !tpl) return;

    var frag = document.createDocumentFragment();
    vehicles.forEach(function (v) {
      var node = tpl.content.cloneNode(true);
      node.querySelector('.vehicle-card__name').textContent = v.name;
      node.querySelector('.vehicle-card__type').textContent = v.type;
      node.querySelector('.vehicle-card__plate-text').textContent = v.plate;
      node.querySelector('.vehicle-card__status').textContent = v.status;
      node.querySelector('.vehicle-card__price-value').textContent = formatRupiah(v.price);

      var img = node.querySelector('.vehicle-card__img');
      img.src = assetsBase + v.image;
      img.alt = v.alt;

      node.querySelector('.btn-ed').setAttribute('aria-label', 'Booking ' + v.name + ' ' + v.plate);
      frag.appendChild(node);
    });
    grid.appendChild(frag);
  }

  document.addEventListener('DOMContentLoaded', renderVehicles);
})();
