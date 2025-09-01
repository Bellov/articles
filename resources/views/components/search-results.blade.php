@if(isset($articles) && count($articles) > 0)
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Source</th>
                    <th>Published Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td><strong>{{ $article['title'] ?? 'No title' }}</strong></td>
                        <td>{{ Str::limit($article['description'] ?? 'No description', 100) }}</td>
                        <td><span class="badge bg-secondary">{{ $article['source']['name'] ?? 'Unknown' }}</span></td>
                        <td><small class="text-muted">{{ $article['publishedAt'] ? \Carbon\Carbon::parse($article['publishedAt'])->format('M d, Y') : 'Unknown' }}</small></td>
                        <td>
                            <a href="{{ $article['url'] ?? '#' }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Read
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        No articles found for this keyword.
    </div>
@endif