<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Test History</h2>
    </x-slot>
    <div class="container py-4">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Score</th>
                            <th>Time Taken</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($history ?? []) as $row)
                        <tr>
                            <td>{{ $row['attempted_at'] ?? '' }}</td>
                            <td>{{ $row['category'] ?? '' }}</td>
                            <td>{{ $row['score'] ?? 0 }}</td>
                            <td>{{ $row['time_taken'] ?? 0 }}s</td>
                            <td><a class="btn btn-sm btn-outline-primary" href="{{ route('student.results', ['id' => $row['id'] ?? 0]) }}">View</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-4">No attempts yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <style>
        .hero-dashboard { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 36px 0; position: relative; overflow: hidden; }
        .hero-dashboard .hero-icon { position: absolute; right: -40px; bottom: -20px; font-size: 10rem; opacity: 0.12; }
        .stat-card { border: 0; box-shadow: 0 8px 20px rgba(0,0,0,0.06); border-radius: 14px; }
    </style>

    <section class="hero-dashboard">
        <div class="container d-flex align-items-center justify-content-between">
            <div>
                <h1 class="display-6 fw-bold mb-1">Test History</h1>
                <p class="mb-0 opacity-75">Review your past test attempts.</p>
            </div>
            <i class="fa-solid fa-clock-rotate-left hero-icon"></i>
        </div>
    </section>

    <div class="container py-4">
        <div class="card stat-card">
            <div class="card-header fw-semibold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Past Tests</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Score</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($history as $h)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($h->created_at)->format('d M Y, H:i') }}</td>
                                    <td>{{ $h->category_name }}</td>
                                    <td>{{ $h->score }}/{{ $h->total }} ({{ round(($h->score/$h->total)*100) }}%)</td>
                                    <td>{{ $h->duration ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('student.results', $h->test_id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No test history found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</x-app-layout>
