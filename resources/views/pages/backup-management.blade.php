@extends('layouts.app')

@section('title', 'Backup Management')

@section('page-css')
<style>
  .backup-card {
    transition: transform 0.3s;
  }
  .backup-card:hover {
    transform: translateY(-5px);
  }
  .backup-size {
    font-size: 0.875rem;
    color: #697a8d;
  }
  .backup-date {
    font-size: 0.75rem;
    color: #a1acb8;
  }
  .backup-note {
    font-size: 0.875rem;
    font-style: italic;
    color: #697a8d;
    margin-top: 5px;
    border-left: 3px solid #e7e7e8;
    padding-left: 10px;
  }
  .backup-title {
    font-weight: 600;
    color: #566a7f;
    margin-bottom: 2px;
  }
  .edit-note-btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    margin-top: -2px;
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Backup Management</h5>
        <div>
          <form action="{{ route('backup.cleanup') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning me-2" onclick="return confirm('Are you sure you want to run the backup cleanup? This will delete old backups according to your retention policy.');">
              <i class="bx bx-trash me-1"></i>Run Cleanup
            </button>
          </form>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBackupModal">
            <i class="bx bx-plus me-1"></i>Create New Backup
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="alert alert-primary">
          <div class="d-flex">
            <i class="bx bx-info-circle bx-sm me-2"></i>
            <div>
              <h6 class="alert-heading mb-1">About Backups</h6>
              <p class="mb-0">
                Manage your application backups. You can create new backups, download existing ones, or delete old backups.
                Backups contain essential data and configurations for system recovery.
              </p>
            </div>
          </div>
        </div>
        
        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <div class="row mb-4">
          <div class="col-12 col-md-8">
            <div class="card bg-light border-0">
              <div class="card-body">
                <h6 class="mb-2">Backup Settings</h6>
                <form action="{{ route('backup.update-path') }}" method="POST">
                  @csrf
                  <div class="mb-3">
                    <label for="backup_path" class="form-label">Backup Directory Path</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="backup_path" name="backup_path" 
                          value="{{ $backupPath ?? '/Applications/MAMP/htdocs/backups/club' }}" 
                          aria-describedby="backup-path-help">
                      <button class="btn btn-primary" type="submit">Update Path</button>
                    </div>
                    <div id="backup-path-help" class="form-text">
                      Enter the full path to the directory where backups should be stored.
                    </div>
                  </div>
                </form>
                <div class="d-flex mt-3">
                  <form action="{{ route('backup.cleanup-old') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to clean up old backups from storage/app/private? This action cannot be undone.');">
                      <i class="bx bx-trash me-1"></i>Clean Up Old Backups
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row mb-3">
          <div class="col-12">
            <div class="card bg-light border-0">
              <div class="card-body">
                <h6 class="mb-2">Backup Information</h6>
                <ul class="mb-0">
                  <li>Current backup path: <code>{{ $backupPath ?? '/Applications/MAMP/htdocs/backups/club' }}</code></li>
                  <li>Backups are automatically named with date and time</li>
                  <li>You can add notes and titles to backup files for better tracking</li>
                  <li>Previous backup files are automatically excluded to prevent recursive backups</li>
                  <li>Backup cleanup retains: all backups for 7 days, daily backups for 16 days, weekly backups for 8 weeks, monthly backups for 4 months</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        
        @if(empty($backups))
          <div class="text-center p-5">
            <i class="bx bx-archive bx-lg text-muted mb-3"></i>
            <h5>No Backups Found</h5>
            <p class="text-muted">No backups are currently available. Create your first backup!</p>
          </div>
        @else
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Filename</th>
                  <th>Size</th>
                  <th>Created</th>
                  <th>Notes/Title</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($backups as $backup)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bx bx-archive me-2 text-primary"></i>
                        <span>{{ $backup['file_name'] }}</span>
                      </div>
                    </td>
                    <td>{{ $backup['file_size'] }}</td>
                    <td>{{ $backup['last_modified'] }}</td>
                    <td>
                      @if($backup['has_note'])
                        <div>
                          @if($backup['note_title'])
                            <span class="backup-title">{{ $backup['note_title'] }}</span>
                          @endif
                          @if($backup['note'])
                            <div class="backup-note">{{ Str::limit($backup['note'], 50) }}</div>
                          @endif
                        </div>
                      @endif
                      <button type="button" class="btn btn-outline-secondary btn-xs edit-note-btn" 
                         data-bs-toggle="modal" 
                         data-bs-target="#editNoteModal" 
                         data-filename="{{ $backup['file_name'] }}" 
                         data-title="{{ $backup['note_title'] ?? '' }}" 
                         data-note="{{ $backup['note'] ?? '' }}">
                         <i class="bx bx-pencil"></i> {{ $backup['has_note'] ? 'Edit' : 'Add' }} Note
                      </button>
                    </td>
                    <td>
                      <div class="d-flex">
                        <a href="{{ route('backup.download', ['fileName' => $backup['file_name']]) }}" class="btn btn-outline-primary btn-sm me-2">
                          <i class="bx bx-download me-1"></i>Download
                        </a>
                        <form action="{{ route('backup.delete') }}" method="POST">
                          @csrf
                          <input type="hidden" name="file_path" value="{{ $backup['file_path'] }}">
                          <input type="hidden" name="disk" value="{{ $backup['disk'] }}">
                          <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this backup?');">
                            <i class="bx bx-trash me-1"></i>Delete
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          
          <div class="mt-4">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
              @foreach($backups as $backup)
                <div class="col">
                  <div class="card h-100 backup-card">
                    <div class="card-body">
                      <div class="d-flex align-items-start justify-content-between">
                        <div>
                          <h5 class="card-title text-truncate mb-0" title="{{ $backup['file_name'] }}">
                            <i class="bx bx-archive me-2 text-primary"></i>{{ $backup['file_name'] }}
                          </h5>
                          <p class="backup-size mb-1">Size: {{ $backup['file_size'] }}</p>
                          <p class="backup-date mb-3">Created: {{ $backup['last_modified'] }}</p>
                          
                          @if($backup['has_note'])
                            <div class="mt-2 mb-3">
                              @if($backup['note_title'])
                                <div class="backup-title">{{ $backup['note_title'] }}</div>
                              @endif
                              @if($backup['note'])
                                <div class="backup-note">{{ $backup['note'] }}</div>
                              @endif
                            </div>
                          @endif
                        </div>
                      </div>
                      <div class="d-flex mt-3">
                        <a href="{{ route('backup.download', ['fileName' => $backup['file_name']]) }}" class="btn btn-outline-primary btn-sm me-2">
                          <i class="bx bx-download me-1"></i>Download
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" 
                           data-bs-toggle="modal" 
                           data-bs-target="#editNoteModal" 
                           data-filename="{{ $backup['file_name'] }}" 
                           data-title="{{ $backup['note_title'] ?? '' }}" 
                           data-note="{{ $backup['note'] ?? '' }}">
                           <i class="bx bx-pencil me-1"></i>{{ $backup['has_note'] ? 'Edit' : 'Add' }} Note
                        </button>
                        <form action="{{ route('backup.delete') }}" method="POST">
                          @csrf
                          <input type="hidden" name="file_path" value="{{ $backup['file_path'] }}">
                          <input type="hidden" name="disk" value="{{ $backup['disk'] }}">
                          <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this backup?');">
                            <i class="bx bx-trash me-1"></i>Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1" aria-labelledby="createBackupModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('backup.start') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="createBackupModalLabel">Create New Backup</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="backup_title" class="form-label">Backup Title</label>
            <input type="text" class="form-control" id="backup_title" name="backup_title" placeholder="Enter a title for this backup">
            <div class="form-text">A short title to identify this backup (optional)</div>
          </div>
          <div class="mb-3">
            <label for="backup_note" class="form-label">Backup Note</label>
            <textarea class="form-control" id="backup_note" name="backup_note" rows="3" placeholder="Enter notes about this backup"></textarea>
            <div class="form-text">Additional information about this backup (optional)</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Backup</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Note Modal -->
<div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('backup.update-note') }}" method="POST">
        @csrf
        <input type="hidden" name="filename" id="edit_filename">
        <input type="hidden" name="disk" value="backup">
        <div class="modal-header">
          <h5 class="modal-title" id="editNoteModalLabel">Update Backup Notes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="edit_title" name="title" placeholder="Enter a title for this backup">
          </div>
          <div class="mb-3">
            <label for="note" class="form-label">Note</label>
            <textarea class="form-control" id="edit_note" name="note" rows="4" placeholder="Enter notes about this backup"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page-js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Handle edit note modal
    const editNoteModal = document.getElementById('editNoteModal');
    if (editNoteModal) {
      editNoteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const filename = button.getAttribute('data-filename');
        const title = button.getAttribute('data-title');
        const note = button.getAttribute('data-note');
        
        document.getElementById('edit_filename').value = filename;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_note').value = note;
        
        document.getElementById('editNoteModalLabel').textContent = 
          'Update Notes for ' + filename;
      });
    }
  });
</script>
@endsection 