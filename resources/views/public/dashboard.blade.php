@extends('layouts.public')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="text-center mb-5">
            <h1 class="display-4">SMA ABBS Surakarta</h1>
            <p class="lead">Sistem Administrasi Sekolah Internal</p>
            <div class="mt-4">
                <div class="alert alert-info">
                    <h5>Informasi untuk Murid/Wali Murid</h5>
                    <p class="mb-0">Anda dapat melihat jurnal kegiatan sekolah secara publik.</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h6 class="card-title">Jurnal KBM</h6>
                        <h3 class="text-primary">{{ \App\Models\JurnalKBM::where('status', 'selesai')->count() }}</h3>
                        <a href="{{ route('public.jurnal.kbm') }}" class="btn btn-outline-primary btn-sm">
                            Lihat Semua
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h6 class="card-title">Jurnal Ekskul</h6>
                        <h3 class="text-success">{{ \App\Models\JurnalEkskul::where('status', 'selesai')->count() }}</h3>
                        <a href="{{ route('public.jurnal.ekskul') }}" class="btn btn-outline-success btn-sm">
                            Lihat Semua
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h6 class="card-title">Jurnal Piket</h6>
                        <h3 class="text-warning">{{ \App\Models\JurnalPiket::where('status', 'selesai')->count() }}</h3>
                        <a href="{{ route('public.jurnal.piket') }}" class="btn btn-outline-warning btn-sm">
                            Lihat Semua
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h6 class="card-title">Jurnal Kegiatan</h6>
                        <h3 class="text-info">{{ \App\Models\JurnalKegiatan::where('status', 'selesai')->count() }}</h3>
                        <a href="{{ route('public.jurnal.kegiatan') }}" class="btn btn-outline-info btn-sm">
                            Lihat Semua
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Kegiatan Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @php
                                $recentActivities = collect();
                                
                                // Get recent KBM
                                $kbm = \App\Models\JurnalKBM::with('kelas', 'mapel', 'guru')
                                    ->where('status', 'selesai')
                                    ->latest()
                                    ->take(3)
                                    ->get()
                                    ->map(function($item) {
                                        $item->type = 'KBM';
                                        $item->icon = 'bi-journal-text';
                                        $item->color = 'primary';
                                        $item->title = $item->mapel->nama_mapel . ' - ' . $item->kelas->nama_kelas;
                                        $item->showDate = $item->tanggal;
                                        return $item;
                                    });
                                
                                // Get recent Kegiatan
                                $kegiatan = \App\Models\JurnalKegiatan::with('guru')
                                    ->where('status', 'selesai')
                                    ->latest()
                                    ->take(3)
                                    ->get()
                                    ->map(function($item) {
                                        $item->type = 'Kegiatan';
                                        $item->icon = 'bi-calendar-event';
                                        $item->color = 'info';
                                        $item->title = $item->nama_kegiatan;
                                        $item->showDate = $item->tanggal;
                                        return $item;
                                    });
                                
                                $recentActivities = $kbm->merge($kegiatan)
                                    ->sortByDesc('created_at')
                                    ->take(5);
                            @endphp
                            
                            @forelse($recentActivities as $activity)
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="bi {{ $activity->icon }} text-{{ $activity->color }} me-3" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <h6 class="mb-1">{{ $activity->title }}</h6>
                                                <p class="mb-1 text-muted">
                                                    {{ $activity->type }} • 
                                                    {{ $activity->guru->name }}
                                                </p>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ ($activity->showDate instanceof \DateTime) ? $activity->showDate->format('d/m/Y') : \Carbon\Carbon::parse($activity->showDate)->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Belum ada kegiatan terbaru</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Kalender Kegiatan</h5>
                    </div>
                    <div class="card-body">
                        <div id="calendar" class="calendar-public"></div>
                        <div class="mt-3">
                            <h6>Kegiatan Mendatang</h6>
                            @php
                                $upcoming = \App\Models\JurnalKegiatan::where('status', 'selesai')
                                    ->whereDate('tanggal', '>=', today())
                                    ->orderBy('tanggal')
                                    ->take(3)
                                    ->get();
                            @endphp
                            @foreach($upcoming as $event)
                                <div class="alert alert-light border mb-2">
                                    <small class="d-block">{{ \Carbon\Carbon::parse($event->tanggal)->format('d M') }}</small>
                                    <strong>{{ $event->nama_kegiatan }}</strong>
                                    <small class="d-block text-muted">{{ $event->lokasi }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.calendar-public {
    font-size: 0.9rem;
}
.calendar-public .month {
    text-align: center;
    font-weight: bold;
    margin-bottom: 10px;
}
.calendar-public .days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}
.calendar-public .day {
    padding: 5px;
    text-align: center;
    border-radius: 3px;
}
.calendar-public .day.header {
    font-weight: bold;
    background-color: #f8f9fa;
}
.calendar-public .day.today {
    background-color: #007bff;
    color: white;
}
.calendar-public .day.has-event {
    background-color: #28a745;
    color: white;
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple calendar
    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();
    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDay = new Date(year, month, 1).getDay();
    
    const calendarEl = document.getElementById('calendar');
    let calendarHTML = `
        <div class="month">${monthNames[month]} ${year}</div>
        <div class="days">
            <div class="day header">M</div>
            <div class="day header">S</div>
            <div class="day header">S</div>
            <div class="day header">R</div>
            <div class="day header">K</div>
            <div class="day header">J</div>
            <div class="day header">S</div>
    `;
    
    // Empty days before first day
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += '<div class="day"></div>';
    }
    
    // Days of month
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = day === today.getDate() && month === today.getMonth();
        const hasEvent = Math.random() > 0.8; // Demo only
        let dayClass = 'day';
        if (isToday) dayClass += ' today';
        if (hasEvent) dayClass += ' has-event';
        
        calendarHTML += `<div class="${dayClass}">${day}</div>`;
    }
    
    calendarHTML += '</div>';
    calendarEl.innerHTML = calendarHTML;
});
</script>
@endpush
@endsection