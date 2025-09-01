<form id="searchForm">
    <div class="mb-4">
        <div class="input-group input-group-lg">
            <input type="text" class="form-control" id="keyword" name="keyword"
                placeholder="(e.g.. Laravel, AI, etc.)" required>
            <button class="btn btn-primary" type="submit" id="searchBtn">
                <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                <span id="btnText">Search</span>
            </button>
        </div>
        <div class="invalid-feedback d-block mt-2" id="errorMessage"></div>
    </div>
</form>