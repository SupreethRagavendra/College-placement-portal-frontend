<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Your Result</h2>
    </x-slot>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="mb-2">Score: {{ $result->score ?? 0 }} / {{ $result->total ?? 0 }}</h3>
                        <p class="text-muted">Peer average: {{ number_format($peerAvg ?? 0, 1) }}%</p>
                        <a href="{{ route('student.categories') }}" class="btn btn-primary">Take Another Test</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-1">Test Results</h2>
        <p class="text-muted mb-0">See your performance and compare with peers.</p>
    </x-slot>
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Score</h5>
                        <div class="display-4 fw-bold">{{ $result->score }}/{{ $result->total }}</div>
                        <div class="text-muted">{{ round(($result->score/$result->total)*100) }}%</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Peer Average</h5>
                        <div class="display-4 fw-bold">{{ round($peerAvg,1) }}%</div>
                        <div class="text-muted">All students</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Rank</h5>
                        <div class="display-4 fw-bold">#{{ $result->rank ?? '-' }}</div>
                        <div class="text-muted">Among peers</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-semibold"><i class="fa-solid fa-list-check me-2"></i>Detailed Analysis</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Question</th>
                                        <th>Your Answer</th>
                                        <th>Correct</th>
                                        <th>Peer Correct %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($analysis as $idx => $a)
                                        <tr>
                                            <td>{{ $idx+1 }}</td>
                                            <td>{{ $a['question'] }}</td>
                                            <td>{!! $a['is_correct'] ? '<span class="badge bg-success">Correct</span>' : '<span class="badge bg-danger">Wrong</span>' !!}</td>
                                            <td>{{ $a['correct_option'] }}</td>
                                            <td>{{ $a['peer_percent'] }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header fw-semibold"><i class="fa-solid fa-chart-pie me-2"></i>Score Distribution</div>
                    <div class="card-body">
                        <canvas id="scorePie" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header fw-semibold"><i class="fa-solid fa-chart-line me-2"></i>Peer Comparison</div>
                    <div class="card-body">
                        <canvas id="peerChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // Pie chart: correct vs wrong
        new Chart(document.getElementById('scorePie'), {
            type: 'pie',
            data: {
                labels: ['Correct', 'Wrong'],
                datasets: [{
                    data: [{{ $result->score }}, {{ $result->total - $result->score }}],
                    backgroundColor: ['#198754', '#dc3545']
                }]
            },
            options: {responsive:true, plugins:{legend:{position:'bottom'}}}
        });
        // Peer comparison line chart
        new Chart(document.getElementById('peerChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($peerLabels ?? ['You','Peer Avg']) !!},
                datasets: [{
                    label: 'Score %',
                    data: {!! json_encode($peerScores ?? [round(($result->score/$result->total)*100), round($peerAvg,1)]) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.2)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {responsive:true, plugins:{legend:{display:false}}}
        });
    </script>
</x-app-layout>
