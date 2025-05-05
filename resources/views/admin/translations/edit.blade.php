@extends('layouts.app')

@section('title', 'Edit Translations')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Admin / <a href="{{ url('admin/translations') }}">Translations</a> /</span> Edit {{ $file }}
  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header d-flex justify-content-between align-items-center">
          <span>Edit Translations: {{ $file }}.php</span>
          <div class="btn-group">
            <a href="{{ url('admin/translations') }}" class="btn btn-outline-secondary btn-sm">
              <i class="bx bx-arrow-back me-1"></i> Back
            </a>
          </div>
        </h5>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">
              {{ session('success') }}
            </div>
          @endif

          <form action="{{ url("admin/translations/{$file}") }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="30%">Key</th>
                    @foreach($languages as $language)
                      <th>{{ strtoupper($language) }}</th>
                    @endforeach
                  </tr>
                </thead>
                <tbody>
                  @foreach($translations['keys'] as $key)
                    <tr>
                      <td>{{ $key }}</td>
                      @foreach($languages as $language)
                        <td>
                          <input 
                            type="text" 
                            name="translations[{{ $language }}][{{ $key }}]" 
                            value="{{ $translations['data'][$language][$key] ?? '' }}" 
                            class="form-control"
                            @if($language === 'fa') dir="rtl" @endif
                          >
                        </td>
                      @endforeach
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            
            <div class="mt-4">
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-save me-1"></i> Save Translations
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add a click handler to copy English text to other languages
    const enInputs = document.querySelectorAll('input[name^="translations[en]"]');
    
    enInputs.forEach(input => {
      input.addEventListener('dblclick', function() {
        const key = this.name.match(/\[([^\]]+)\]$/)[1];
        const value = this.value;
        
        @foreach($languages as $language)
          @if($language !== 'en')
            document.querySelector(`input[name="translations[{{ $language }}][${key}]"]`).value = value;
          @endif
        @endforeach
      });
    });
  });
</script>
@endsection 