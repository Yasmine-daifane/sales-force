@extends("layouts.app")

@section("content")
<div class="container mt-4">
    <h1 class="mb-4">Tableau de Bord Administrateur</h1>

    {{-- Key Metrics Row --}}
    <div class="row mb-4">
        {{-- Commercials Count Card --}}
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card text-white bg-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Commerciaux</h5>
                            <p class="card-text fs-4 fw-bold">{{ $commercialCount ?? 0 }}</p>
                        </div>
                        <i class="bi bi-people-fill fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Visits Count Card --}}
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card text-white bg-success shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Visites Totales</h5>
                            <p class="card-text fs-4 fw-bold">{{ $totalVisitsCount ?? 0 }}</p>
                        </div>
                        <i class="bi bi-calendar-check-fill fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visits Last 7 Days Card --}}
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card text-dark bg-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Visites (7j)</h5>
                            <p class="card-text fs-4 fw-bold">{{ $visitsLast7Days ?? 0 }}</p>
                        </div>
                        <i class="bi bi-calendar-event-fill fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Average Visits Card --}}
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card text-white bg-info shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Moy. Visites/Comm.</h5>
                            <p class="card-text fs-4 fw-bold">{{ $averageVisits ?? 0 }}</p>
                        </div>
                        <i class="bi bi-graph-up fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row mb-4">
        {{-- Daily Visits Trend (Line Chart) --}}
        <div class="col-lg-12 mb-4"> {{-- Take full width for the trend chart --}}
             <div class="card shadow-sm">
                <div class="card-header">Tendance des Visites (30 derniers jours)</div>
                <div class="card-body">
                    <canvas id="dailyVisitsChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Details Row --}}
    <div class="row">
        {{-- Visits per Commercial (Bar Chart) --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">Visites par Commercial (Total)</div>
                <div class="card-body">
                    <canvas id="visitsPerCommercialChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Visits per Commercial Table --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">Détail Visites par Commercial</div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                    @if(isset($visitsPerCommercial) && $visitsPerCommercial->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($visitsPerCommercial as $commercial)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $commercial->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $commercial->commercial_visits_count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aucune donnée de visite par commercial.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push("scripts")
<script src="https://cdn.jsdelivr.net/npm/chart.js@^4"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // --- Daily Visits Line Chart ---
        const dailyCtx = document.getElementById("dailyVisitsChart");
        if (dailyCtx) {
            const dailyLabels = @json($lineChartLabels ?? []);
            const dailyData = @json($lineChartData ?? []);
            new Chart(dailyCtx, {
                type: "line",
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: "Visites par Jour",
                        data: dailyData,
                        fill: true,
                        borderColor: "rgb(75, 192, 192)",
                        backgroundColor: "rgba(75, 192, 192, 0.2)",
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        title: { display: false } // Title is in card header
                    }
                }
            });
        } else {
            console.error("Canvas element #dailyVisitsChart not found.");
        }

        // --- Visits per Commercial Bar Chart ---
        const barCtx = document.getElementById("visitsPerCommercialChart");
        if (barCtx) {
            const barLabels = @json($barChartLabels ?? []);
            const barData = @json($barChartData ?? []);
            new Chart(barCtx, {
                type: "bar",
                data: {
                    labels: barLabels,
                    datasets: [{
                        label: "Nombre de Visites",
                        data: barData,
                        backgroundColor: "rgba(54, 162, 235, 0.6)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: "y", // Horizontal bar chart might be better for names
                    scales: {
                        x: { // Note: x-axis for horizontal bar
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        title: { display: false } // Title is in card header
                    }
                }
            });
        } else {
            console.error("Canvas element #visitsPerCommercialChart not found.");
        }
    });
</script>
@endpush

