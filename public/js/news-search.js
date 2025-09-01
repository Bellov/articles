let isSearching = false;
let chartManager = null;
let currentPage = 1;
let shouldRefreshArticles = false;

document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', handleSearch);
    }
    initializeChart();
    setupTabListeners();
    setupGlobalEventDelegation();
}

function setupTabListeners() {
    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-bs-target').replace('#', '');
            handleTabSwitch(targetTab);
        });
    });
}

function setupGlobalEventDelegation() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination .page-link')) {
            e.preventDefault();
            const pageLink = e.target.closest('.page-link');
            const page = pageLink.dataset.page;
            if (page) {
                loadArticlesData(parseInt(page));
            }
        }
    });
}

function handleTabSwitch(targetTab) {
    switch (targetTab) {
        case 'articles':
            if (shouldRefreshArticles) {
                currentPage = 1;
                loadArticlesData();
                shouldRefreshArticles = false;
            } else {
                loadArticlesData(currentPage);
            }
            break;
        case 'stats':
            loadChartData();
            break;
    }
}

function loadArticlesData(page = 1) {
    currentPage = page;
    showArticlesLoading();
    
    fetch(`/data?page=${page}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayArticlesTable(data.articles, data.pagination);
            } else {
                showArticlesError(data.message || 'Failed to load articles');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showArticlesError('Network error. Please try again.');
        });
}

function showArticlesLoading() {
    const resultsDiv = document.getElementById('articles-results');
    if (resultsDiv) {
        resultsDiv.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Loading articles...</p>
            </div>
        `;
    }
}

function showLoadingSpinner(containerId, message = 'Loading...') {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">${message}</p>
            </div>
        `;
    }
}

function displayArticlesTable(articles, pagination) {
    const resultsDiv = document.getElementById('articles-results');
    
    if (!resultsDiv) return;
    
    if (!articles || articles.length === 0) {
        resultsDiv.innerHTML = '<div class="alert alert-info">No articles found in database.</div>';
        return;
    }
    
    renderArticlesTable(articles, pagination);
}

function renderArticlesTable(articles, pagination) {
    const resultsDiv = document.getElementById('articles-results');
    
    let html = `
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Source</th>
                        <th>Published Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    articles.forEach(article => {
        const articleData = JSON.parse(article.response_json);
        html += `
            <tr>
                <td><strong>${articleData.title || 'No title'}</strong></td>
                <td><span class="badge bg-secondary">${articleData.source?.name || 'Unknown'}</span></td>
                <td><small class="text-muted">${formatPublishedDate(articleData.publishedAt)}</small></td>
                <td>
                    <a href="${articleData.url || '#'}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Read
                    </a>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    
    if (pagination) {
        html += `
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Showing ${pagination.from} to ${pagination.to} of ${pagination.total} articles
                </small>
            </div>
        `;
        
        if (pagination.last_page > 1) {
            html += generatePaginationHTML(pagination);
        }
    }
    
    resultsDiv.innerHTML = html;
}

function formatPublishedDate(publishedAt) {
    return publishedAt ? new Date(publishedAt).toLocaleDateString() : 'Unknown';
}

function generatePaginationHTML(pagination) {
    let html = '<nav aria-label="Articles pagination"><ul class="pagination justify-content-center">';
    
    if (pagination.current_page > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
            </li>
        `;
    } else {
        html += `
            <li class="page-item disabled">
                <span class="page-link">
                    <i class="bi bi-chevron-left"></i> Previous
                </span>
            </li>
        `;
    }
    
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
    }
    
    if (pagination.current_page < pagination.last_page) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `;
    } else {
        html += `
            <li class="page-item disabled">
                <span class="page-link">
                    Next <i class="bi bi-chevron-right"></i>
                </span>
            </li>
        `;
    }
    
    html += '</ul></nav>';
    return html;
}
function showArticlesError(message) {
    const resultsDiv = document.getElementById('articles-results');
    if (resultsDiv) {
        resultsDiv.innerHTML = `<div class="alert alert-danger">${message}</div>`;
    }
}

function handleSearch(e) {
    e.preventDefault();
    
    if (isSearching) return;
    
    const keyword = document.getElementById('keyword').value.trim();
    if (!keyword) return;
    
    performSearch(keyword);
}

function performSearch(keyword) {
    showLoading(true);
    clearResults();
    clearError();
    hideSuccess();
    
    fetch('/api/search-news', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ keyword: keyword })
    })
    .then(response => response.json())
    .then(data => {
        showLoading(false);
        if (data.success) {
            handleSearchSuccess(data, keyword);
        } else {
            showError(data.message || 'Something happening. Please try again later.');
        }
    })
    .catch(error => {
        showLoading(false);
        showError('Network error. Please try again.');
        console.error('Error:', error);
    })
    .finally(() => {
        isSearching = false;
    });
}

function handleSearchSuccess(data, keyword) {
    if (data.articles && data.articles.length > 0) {
        showSuccess(data.message);
    }
    
    // this show all the 20 records when you search ( and hide after refresh the page )
    // the results can be see it into the articles tab
    
    // displayResults(data.articles);

    updateStats(data.articles);
    shouldRefreshArticles = true;
}

function switchToTab(tabName) {
    document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(content => content.classList.remove('show', 'active'));
    
    const targetTab = document.getElementById(`${tabName}-tab`);
    const targetContent = document.getElementById(tabName);
    
    if (targetTab && targetContent) {
        targetTab.classList.add('active');
        targetContent.classList.add('show', 'active');
    }
}

function updateStats(articles) {
    updateTotalArticles(articles.length);
    updateLastSearchTime();
    updateChartWithMockData(articles.length);
}

function updateTotalArticles(count) {
    const totalArticlesElement = document.getElementById('totalArticles');
    if (totalArticlesElement) {
        totalArticlesElement.textContent = count;
    }
}

function updateLastSearchTime() {
    const lastSearchElement = document.getElementById('lastSearch');
    if (lastSearchElement) {
        lastSearchElement.textContent = new Date().toLocaleString();
    }
}

function updateChartWithMockData(articlesCount) {
    if (chartManager) {
        chartManager.updateWithMockData(articlesCount);
    }
}

function initializeChart() {
    chartManager = new ChartManager();
    chartManager.initialize('articlesChart');
}

function loadChartData() {
    if (chartManager) {
        chartManager.refresh();
    }
}

function updateChartData(labels, data) {
    if (chartManager) {
        chartManager.updateData(labels, data);
    }
}

function showLoading(show) {
    const loading = document.getElementById('loading');
    const searchBtn = document.getElementById('searchBtn');
    const spinner = document.getElementById('spinner');
    const btnText = document.getElementById('btnText');
    
    if (show) {
        loading.classList.remove('d-none');
        searchBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Searching...';
    } else {
        loading.classList.add('d-none');
        searchBtn.disabled = false;
        spinner.classList.add('d-none');
        btnText.textContent = 'Search';
    }
}

function clearResults() {
    const resultsDiv = document.getElementById('results');
    if (resultsDiv) {
        resultsDiv.innerHTML = '';
    }
}

function clearError() {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
        errorDiv.textContent = '';
        errorDiv.classList.remove('d-block');
    }
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.add('d-block');
    }
}

function showSuccess(message) {
    const successAlert = document.getElementById('successAlert');
    const successMessage = document.getElementById('successMessage');
    
    if (successAlert && successMessage) {
        successMessage.textContent = message;
        successAlert.classList.remove('d-none');
        
        setTimeout(() => {
            hideSuccess();
        }, 5000);
    }
}

function hideSuccess() {
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        successAlert.classList.add('d-none');
    }
}

function displayResults(articles) {
    const resultsDiv = document.getElementById('results');
    if (!resultsDiv) return;
    
    if (!articles || articles.length === 0) {
        resultsDiv.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No articles found for this keyword. Try searching with different terms.
            </div>
        `;
        return;
    }
    
    loadSearchResults(articles);
}

function loadSearchResults(articles) {
    const resultsDiv = document.getElementById('results');
    
    showLoadingSpinner('results', 'Loading results...');
    
    // ajax call
    fetch('/api/render-search-results', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ articles: articles })
    })
    .then(response => response.text())
    .then(html => {
        resultsDiv.innerHTML = html;
    })
    .catch(error => {
        console.error('Error loading results:', error);
        resultsDiv.innerHTML = '<div class="alert alert-danger">Error loading results. Please try again.</div>';
    });
}