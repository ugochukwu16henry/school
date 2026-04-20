@if (session('status'))
    <p class="text-success">
        <i class="bi bi-exclamation-diamond-fill me-2"></i> {{ session('status') }}
    </p>
@endif
@if (session('error'))
    <p class="text-danger">
        <i class="bi bi-exclamation-diamond-fill me-2"></i> {{ session('error') }}
    </p>
@endif

@if (session('claim_link'))
    <div class="alert alert-info" role="alert">
        <div class="fw-semibold mb-1">Parent claim link generated</div>
        <div class="small mb-2">Share this link with the parent to complete account setup for the child.</div>
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" id="parentClaimLinkInput" readonly value="{{ session('claim_link') }}">
            <button class="btn btn-outline-primary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('parentClaimLinkInput').value)">Copy</button>
        </div>
        @if (session('claim_code'))
            <div class="small text-muted mt-2">Claim code: {{ session('claim_code') }}</div>
        @endif
    </div>
@endif

@if (isset($errors) && $errors->any())
    @foreach ($errors->all() as $error)
        <p class="text-danger">
            <i class="bi bi-exclamation-diamond-fill me-2"></i>
            {{ $error }}
        </p>
    @endforeach
@endif