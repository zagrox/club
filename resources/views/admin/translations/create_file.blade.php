@extends('layouts.app')

@section('title', 'Create Translation File')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Admin / <a href="{{ route('translations.index') }}">Translations</a> /</span> Create File
  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header d-flex justify-content-between align-items-center">
          <span>Create New Translation File</span>
          <a href="{{ route('translations.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back to Translations
          </a>
        </h5>
        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          
          @if(session('error'))
            <div class="alert alert-danger">
              {{ session('error') }}
            </div>
          @endif
        
          <form action="{{ route('translations.store_file') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="filename" class="form-label">File Name <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="text" class="form-control @error('filename') is-invalid @enderror" id="filename" name="filename" value="{{ old('filename') }}" placeholder="e.g. custom_messages" required>
                    <span class="input-group-text">.php</span>
                  </div>
                  <div class="form-text">Use only lowercase letters, numbers, and underscores. This file will be created for all languages.</div>
                  @error('filename')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="card mb-4 shadow-none border">
                  <div class="card-header">
                    <h5 class="mb-0">Available Languages</h5>
                  </div>
                  <div class="card-body">
                    <p>Translation file will be created for these languages:</p>
                    <div class="row">
                      @foreach($languages->chunk(ceil($languages->count() / 2)) as $chunk)
                        <div class="col-6">
                          <ul>
                            @foreach($chunk as $language)
                              <li>{{ $language->name }} ({{ $language->code }})</li>
                            @endforeach
                          </ul>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="alert alert-info">
              <p class="mb-0"><strong>Common Translation File Names:</strong></p>
              <p class="mb-0">
                <code>messages</code> - General messages<br>
                <code>validation</code> - Form validation messages<br>
                <code>auth</code> - Authentication messages<br>
                <code>pagination</code> - Pagination texts<br>
                <code>passwords</code> - Password reset messages
              </p>
            </div>
            
            <div class="mt-4">
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-plus-circle me-1"></i> Create File
              </button>
              <a href="{{ route('translations.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 