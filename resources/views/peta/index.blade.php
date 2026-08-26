@extends('layouts.admin')

@section('title', 'Peta Kandang - SIKAP')
@section('page-title', 'Peta Sebaran Kandang')

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div id="map" style="height: 600px;"></div>
</div>

@if($kandangs->isEmpty())
<div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-700">
    Belum ada kandang dengan data koordinat. Silakan tambahkan latitude/longitude di data kandang.
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof L === 'undefined') return;

    var map = L.map('map').setView([-6.2, 106.8], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var markers = [];
    @foreach($kandangs as $kandang)
        @if($kandang->latitude && $kandang->longitude)
            markers.push({
                lat: {{ $kandang->latitude }},
                lng: {{ $kandang->longitude }},
                name: "{{ $kandang->nama_kandang }}",
                address: "{{ $kandang->kecamatan }}, {{ $kandang->kabupaten_kota }}",
                capacity: "{{ number_format($kandang->kapasitas) }} ekor"
            });
        @endif
    @endforeach

    markers.forEach(function(m) {
        L.marker([m.lat, m.lng]).addTo(map)
            .bindPopup('<b>' + m.name + '</b><br>' + m.address + '<br>Kapasitas: ' + m.capacity);
    });

    if (markers.length > 0) {
        var bounds = L.latLngBounds(markers.map(function(m) { return [m.lat, m.lng]; }));
        map.fitBounds(bounds, { padding: [50, 50] });
    }
});
</script>
@endsection
