<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-12">
                <div class="card shadow">
                    <div class="card-body p-4">

                        {{-- Header --}}
                        <div class="text-center mb-4">
                            <h1 class="display-4 text-primary mb-3">---- Articles ----</h1>
                            <p class="text-muted">Search for the latest news articles</p>
                        </div>
                        {{-- Header --}}


                        {{-- Tabs Navigation  --}}
                        <ul class="nav nav-tabs mb-4" id="newsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="search-tab" data-bs-toggle="tab"
                                    data-bs-target="#search" type="button" role="tab">
                                    <i class="bi bi-search me-2"></i>Find Articles
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="articles-tab" data-bs-toggle="tab"
                                    data-bs-target="#articles" type="button" role="tab">
                                    <i class="bi bi-newspaper me-2"></i>Articles
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats"
                                    type="button" role="tab">
                                    <i class="bi bi-graph-up me-2"></i>Statistics
                                </button>
                            </li>
                        </ul>
                        {{-- Tabs Navigation  --}}


                        {{-- Tab Content  --}}
                        <div class="tab-content" id="newsTabsContent">

                            {{-- Search Tab  --}}
                            <div class="tab-pane fade show active" id="search" role="tabpanel">
                                @include('components.search-form')
                                @include('components.loading-indicator')
                                @include('components.success-alert')
                                <div id="results" class="mt-4">
                                    {{-- Search results will be loaded here via AJAX --}}
                                </div>
                            </div>
                            {{-- Search Tab  --}}

                            {{-- Articles Tab  --}}
                            <div class="tab-pane fade" id="articles" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Latest Articles</h5>
                                    <button class="btn btn-sm btn-outline-primary" onclick="switchToTab('search')">
                                        <i class="bi bi-search me-1"></i>New Search
                                    </button>
                                </div>
                                <div id="articles-results">
                                    {{-- Articles will be loaded here via AJAX  --}}
                                    <div class="text-center text-muted">
                                        <i class="bi bi-newspaper" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Click on Articles tab to load data</p>
                                    </div>
                                </div>
                            </div>
                            {{-- Articles Tab  --}}


                            {{-- Stats Tab  --}}
                            <div class="tab-pane fade" id="stats" role="tabpanel">
                                <h5 class="mb-3 text-center">Articles Statistics</h5>
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0 text-center">Articles per Day (Last 7 Days)</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="articlesChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            {{-- Stats Tab  --}}

                        </div>
                        {{-- Tab Content  --}}

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="{{ asset('js/chart-config.js') }}"></script>
    <script src="{{ asset('js/chart-utils.js') }}"></script>
    <script src="{{ asset('js/chart-manager.js') }}"></script>

    <script src="{{ asset('js/news-search.js') }}"></script>
</body>

</html>
