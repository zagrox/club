@extends('layouts.app')

@section('title', 'Language Management')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sortablejs/sortable.css') }}">
<style>
  .language-sortable .sortable-handle {
    cursor: move;
  }
  .progress.translation-progress {
    height: 10px;
  }
  .language-row {
    background-color: #fff;
    transition: all 0.2s;
  }
  .language-row.ui-sortable-helper {
    box-shadow: 0 0 10px rgba(0,0,0,0.15);
  }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Admin / <a href="{{ route('translations.index') }}">Translations</a> /</span> Language Management
  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header d-flex justify-content-between align-items-center">
          <span>Manage Languages</span>
          <div class="btn-group">
            <a href="{{ route('translations.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="bx bx-arrow-back me-1"></i> Back to Translations
            </a>
            <a href="{{ route('translations.create_language') }}" class="btn btn-primary btn-sm">
              <i class="bx bx-plus-circle me-1"></i> Add New Language
            </a>
          </div>
        </h5>
        
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">
              {{ session('success') }}
            </div>
          @endif
          
          @if(session('error'))
            <div class="alert alert-danger">
              {{ session('error') }}
            </div>
          @endif
          
          <div class="alert alert-info">
            <p class="mb-0">
              <i class="bx bx-info-circle"></i> 
              Drag and drop languages to reorder them. This affects the order they appear in the language selector.
              English is the default language and cannot be deleted.
            </p>
          </div>
          
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th width="5%">#</th>
                  <th width="10%">Code</th>
                  <th width="15%">Name</th>
                  <th width="15%">Native Name</th>
                  <th width="10%">Direction</th>
                  <th width="10%">Status</th>
                  <th width="20%">Translation Progress</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody class="language-sortable" id="language-sortable">
                @forelse($languages as $language)
                  <tr class="language-row" data-id="{{ $language->id }}">
                    <td class="sortable-handle text-center">
                      <i class="bx bx-move"></i>
                    </td>
                    <td>{{ $language->code }}</td>
                    <td>{{ $language->name }}</td>
                    <td>{{ $language->native }}</td>
                    <td>{{ $language->is_rtl ? 'RTL' : 'LTR' }}</td>
                    <td>
                      @if($language->is_active)
                        <span class="badge bg-label-success">Active</span>
                      @else
                        <span class="badge bg-label-secondary">Inactive</span>
                      @endif
                    </td>
                    <td>
                      @php
                        $percent = $stats[$language->code]['percent_translated'] ?? 0;
                        $statusClass = $percent < 50 ? 'danger' : ($percent < 80 ? 'warning' : 'success');
                      @endphp
                      <div class="d-flex align-items-center">
                        <div class="progress translation-progress w-100 me-2">
                          <div class="progress-bar bg-{{ $statusClass }}" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small>{{ $percent }}%</small>
                      </div>
                      <small class="text-muted">
                        {{ $stats[$language->code]['translated'] ?? 0 }}/{{ $stats[$language->code]['translations'] ?? 0 }} strings
                      </small>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="{{ route('translations.edit_language', $language->id) }}" class="btn btn-sm btn-primary">
                          <i class="bx bx-edit-alt"></i> Edit
                        </a>
                        
                        <form action="{{ route('translations.toggle_language', $language->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $language->is_active ? 'btn-warning' : 'btn-success' }}">
                            <i class="bx {{ $language->is_active ? 'bx-power-off' : 'bx-check' }}"></i> {{ $language->is_active ? 'Deactivate' : 'Activate' }}
                          </button>
                        </form>
                        
                        @if($language->code !== 'en')
                          <form action="{{ route('translations.delete_language', $language->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this language? All translations will be lost. This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                              <i class="bx bx-trash"></i> Delete
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center">No languages found.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          
          <div class="row mt-4">
            <div class="col-md-6">
              <div class="card mb-4 shadow-none border">
                <div class="card-header">
                  <h5 class="mb-0">Translation Stats</h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Language</th>
                          <th>Files</th>
                          <th>Strings</th>
                          <th>Translated</th>
                          <th>Untranslated</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($languages as $language)
                          <tr>
                            <td>{{ $language->code }}</td>
                            <td>{{ $stats[$language->code]['files'] ?? 0 }}</td>
                            <td>{{ $stats[$language->code]['translations'] ?? 0 }}</td>
                            <td class="text-success">{{ $stats[$language->code]['translated'] ?? 0 }}</td>
                            <td class="text-danger">{{ $stats[$language->code]['untranslated'] ?? 0 }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="card mb-4 shadow-none border">
                <div class="card-header">
                  <h5 class="mb-0">Quick Tips</h5>
                </div>
                <div class="card-body">
                  <ul class="ps-3 mb-0">
                    <li class="mb-2">English is the default language and cannot be deleted.</li>
                    <li class="mb-2">To add a new language, click the "Add New Language" button.</li>
                    <li class="mb-2">Drag languages to reorder them in the language selector.</li>
                    <li class="mb-2">Deactivate languages you don't need to hide them from users.</li>
                    <li class="mb-2">The progress bar shows the percentage of translated strings.</li>
                    <li class="mb-2">To translate strings, go to <a href="{{ route('translations.index') }}">Translation Files</a>.</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js')
<script src="{{ asset('assets/vendor/libs/sortablejs/sortable.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const languageSortable = document.getElementById('language-sortable');
    
    if (languageSortable) {
      new Sortable(languageSortable, {
        handle: '.sortable-handle',
        animation: 150,
        onEnd: function() {
          const rows = document.querySelectorAll('.language-row');
          const ids = Array.from(rows).map(row => row.dataset.id);
          
          // Send the new order to the server
          fetch('{{ route('translations.reorder_languages') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              console.log('Languages reordered successfully');
            }
          })
          .catch(error => {
            console.error('Error reordering languages:', error);
          });
        }
      });
    }
  });
</script>
@endsection 