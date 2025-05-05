<?php $__env->startSection('title', 'Backup Management'); ?>

<?php $__env->startSection('page-css'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Backup Management</h5>
        <div>
          <form action="<?php echo e(route('backup.cleanup')); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
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
        
        <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
          <?php echo e(session('success')); ?>

          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
          <?php echo e(session('error')); ?>

          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row mb-4">
          <div class="col-12 col-md-8">
            <div class="card bg-light border-0">
              <div class="card-body">
                <h6 class="mb-2">Backup Settings</h6>
                <form action="<?php echo e(route('backup.update-path')); ?>" method="POST">
                  <?php echo csrf_field(); ?>
                  <div class="mb-3">
                    <label for="backup_path" class="form-label">Backup Directory Path</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="backup_path" name="backup_path" 
                          value="<?php echo e($backupPath ?? '/Applications/MAMP/htdocs/backups/club'); ?>" 
                          aria-describedby="backup-path-help">
                      <button class="btn btn-primary" type="submit">Update Path</button>
                    </div>
                    <div id="backup-path-help" class="form-text">
                      Enter the full path to the directory where backups should be stored.
                    </div>
                  </div>
                </form>
                <div class="d-flex mt-3">
                  <form action="<?php echo e(route('backup.cleanup-old')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
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
                  <li>Current backup path: <code><?php echo e($backupPath ?? '/Applications/MAMP/htdocs/backups/club'); ?></code></li>
                  <li>Backups are automatically named with date and time</li>
                  <li>You can add notes and titles to backup files for better tracking</li>
                  <li>Previous backup files are automatically excluded to prevent recursive backups</li>
                  <li>Backup cleanup retains: all backups for 7 days, daily backups for 16 days, weekly backups for 8 weeks, monthly backups for 4 months</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        
        <?php if(empty($backups)): ?>
          <div class="text-center p-5">
            <i class="bx bx-archive bx-lg text-muted mb-3"></i>
            <h5>No Backups Found</h5>
            <p class="text-muted">No backups are currently available. Create your first backup!</p>
          </div>
        <?php else: ?>
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
                <?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bx bx-archive me-2 text-primary"></i>
                        <span><?php echo e($backup['file_name']); ?></span>
                      </div>
                    </td>
                    <td><?php echo e($backup['file_size']); ?></td>
                    <td><?php echo e($backup['last_modified']); ?></td>
                    <td>
                      <?php if($backup['has_note']): ?>
                        <div>
                          <?php if($backup['note_title']): ?>
                            <span class="backup-title"><?php echo e($backup['note_title']); ?></span>
                          <?php endif; ?>
                          <?php if($backup['note']): ?>
                            <div class="backup-note"><?php echo e(Str::limit($backup['note'], 50)); ?></div>
                          <?php endif; ?>
                        </div>
                      <?php endif; ?>
                      <button type="button" class="btn btn-outline-secondary btn-xs edit-note-btn" 
                         data-bs-toggle="modal" 
                         data-bs-target="#editNoteModal" 
                         data-filename="<?php echo e($backup['file_name']); ?>" 
                         data-title="<?php echo e($backup['note_title'] ?? ''); ?>" 
                         data-note="<?php echo e($backup['note'] ?? ''); ?>">
                         <i class="bx bx-pencil"></i> <?php echo e($backup['has_note'] ? 'Edit' : 'Add'); ?> Note
                      </button>
                    </td>
                    <td>
                      <div class="d-flex">
                        <a href="<?php echo e(route('backup.download', ['fileName' => $backup['file_name']])); ?>" class="btn btn-outline-primary btn-sm me-2">
                          <i class="bx bx-download me-1"></i>Download
                        </a>
                        <form action="<?php echo e(route('backup.delete')); ?>" method="POST">
                          <?php echo csrf_field(); ?>
                          <input type="hidden" name="file_path" value="<?php echo e($backup['file_path']); ?>">
                          <input type="hidden" name="disk" value="<?php echo e($backup['disk']); ?>">
                          <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this backup?');">
                            <i class="bx bx-trash me-1"></i>Delete
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
          
          <div class="mt-4">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
              <?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col">
                  <div class="card h-100 backup-card">
                    <div class="card-body">
                      <div class="d-flex align-items-start justify-content-between">
                        <div>
                          <h5 class="card-title text-truncate mb-0" title="<?php echo e($backup['file_name']); ?>">
                            <i class="bx bx-archive me-2 text-primary"></i><?php echo e($backup['file_name']); ?>

                          </h5>
                          <p class="backup-size mb-1">Size: <?php echo e($backup['file_size']); ?></p>
                          <p class="backup-date mb-3">Created: <?php echo e($backup['last_modified']); ?></p>
                          
                          <?php if($backup['has_note']): ?>
                            <div class="mt-2 mb-3">
                              <?php if($backup['note_title']): ?>
                                <div class="backup-title"><?php echo e($backup['note_title']); ?></div>
                              <?php endif; ?>
                              <?php if($backup['note']): ?>
                                <div class="backup-note"><?php echo e($backup['note']); ?></div>
                              <?php endif; ?>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="d-flex mt-3">
                        <a href="<?php echo e(route('backup.download', ['fileName' => $backup['file_name']])); ?>" class="btn btn-outline-primary btn-sm me-2">
                          <i class="bx bx-download me-1"></i>Download
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" 
                           data-bs-toggle="modal" 
                           data-bs-target="#editNoteModal" 
                           data-filename="<?php echo e($backup['file_name']); ?>" 
                           data-title="<?php echo e($backup['note_title'] ?? ''); ?>" 
                           data-note="<?php echo e($backup['note'] ?? ''); ?>">
                           <i class="bx bx-pencil me-1"></i><?php echo e($backup['has_note'] ? 'Edit' : 'Add'); ?> Note
                        </button>
                        <form action="<?php echo e(route('backup.delete')); ?>" method="POST">
                          <?php echo csrf_field(); ?>
                          <input type="hidden" name="file_path" value="<?php echo e($backup['file_path']); ?>">
                          <input type="hidden" name="disk" value="<?php echo e($backup['disk']); ?>">
                          <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this backup?');">
                            <i class="bx bx-trash me-1"></i>Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1" aria-labelledby="createBackupModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo e(route('backup.start')); ?>" method="POST">
        <?php echo csrf_field(); ?>
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
      <form action="<?php echo e(route('backup.update-note')); ?>" method="POST">
        <?php echo csrf_field(); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
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
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/pages/backup-management.blade.php ENDPATH**/ ?>