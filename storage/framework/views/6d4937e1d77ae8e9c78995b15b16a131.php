<?php $__env->startSection('title', trans('messages.Wallet')); ?>

<?php $__env->startSection('page-css'); ?>
<style>
    .wallet-balance {
        font-size: 2.5rem;
        font-weight: bold;
        color: #5d87ff;
    }
    .wallet-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .wallet-icon {
        font-size: 1.5rem;
        padding: 12px;
        background-color: rgba(93, 135, 255, 0.1);
        border-radius: 50%;
        margin-bottom: 15px;
    }
    .transaction-badge {
        padding: 8px 12px;
        font-size: 0.8rem;
        border-radius: 30px;
    }
    .action-button {
        margin-right: 10px;
        border-radius: 8px;
        padding: 10px 20px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"><?php echo e(trans('messages.Account')); ?> /</span> <?php echo e(trans('messages.my_wallet')); ?>

    </h4>

    <div class="row">
        <!-- Wallet Balance Card -->
        <div class="col-md-6 mb-4">
            <div class="card wallet-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h5 class="card-title"><?php echo e(trans('messages.Wallet Balance')); ?></h5>
                            <div class="wallet-balance mt-3"><?php echo e(number_format($balance)); ?> <small class="text-muted"><?php echo e(trans('messages.currency_unit')); ?></small></div>
                        </div>
                        <div class="wallet-icon">
                            <i class="bx bx-wallet text-primary"></i>
                        </div>
                    </div>
                    
                    <?php if($balance < 10000): ?>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bx bx-error-circle me-1"></i>
                        <span><?php echo e(trans('messages.low_wallet_balance')); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-light d-flex justify-content-center py-3">
                    <a href="<?php echo e(route('wallet.showDepositForm')); ?>" class="btn btn-primary action-button">
                        <i class="bx bx-plus-circle me-1"></i><?php echo e(trans('messages.Charge Wallet')); ?>

                    </a>
                    <a href="<?php echo e(route('wallet.showWithdrawForm')); ?>" class="btn btn-outline-warning action-button">
                        <i class="bx bx-minus-circle me-1"></i><?php echo e(trans('messages.Withdraw')); ?>

                    </a>
                    <a href="<?php echo e(route('wallet.showTransferForm')); ?>" class="btn btn-outline-info action-button">
                        <i class="bx bx-transfer me-1"></i><?php echo e(trans('messages.Transfer')); ?>

                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="col-md-6 mb-4">
            <div class="card wallet-card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo e(trans('messages.Quick Actions')); ?></h5>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <i class="bx bx-history mb-2 text-primary" style="font-size: 2rem;"></i>
                                    <h6><?php echo e(trans('messages.Transaction History')); ?></h6>
                                    <a href="<?php echo e(route('wallet.transactions')); ?>" class="btn btn-sm btn-outline-primary mt-2">
                                        <?php echo e(trans('messages.View All')); ?>

                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <i class="bx bx-credit-card mb-2 text-success" style="font-size: 2rem;"></i>
                                    <h6><?php echo e(trans('messages.Pay with Wallet')); ?></h6>
                                    <a href="#" class="btn btn-sm btn-outline-success mt-2">
                                        <?php echo e(trans('messages.Quick Pay')); ?>

                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card wallet-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo e(trans('messages.Recent Transactions')); ?></h5>
                    <a href="<?php echo e(route('wallet.transactions')); ?>" class="btn btn-sm btn-outline-primary"><?php echo e(trans('messages.View All')); ?></a>
                </div>
                <div class="table-responsive text-nowrap">
                    <?php if($transactions->count() > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo e(trans('messages.Transaction Type')); ?></th>
                                    <th><?php echo e(trans('messages.Amount')); ?></th>
                                    <th><?php echo e(trans('messages.Description')); ?></th>
                                    <th><?php echo e(trans('messages.Date')); ?></th>
                                    <th><?php echo e(trans('messages.Status')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php if($transaction->type === 'deposit'): ?>
                                                <span class="badge bg-label-success transaction-badge">
                                                    <i class="bx bx-plus-circle me-1"></i><?php echo e(trans('messages.Deposit')); ?>

                                                </span>
                                            <?php elseif($transaction->type === 'withdrawal'): ?>
                                                <span class="badge bg-label-warning transaction-badge">
                                                    <i class="bx bx-minus-circle me-1"></i><?php echo e(trans('messages.Withdraw')); ?>

                                                </span>
                                            <?php elseif($transaction->type === 'transfer'): ?>
                                                <span class="badge bg-label-info transaction-badge">
                                                    <i class="bx bx-transfer-alt me-1"></i><?php echo e(trans('messages.Transfer')); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-label-primary transaction-badge">
                                                    <i class="bx bx-repeat me-1"></i><?php echo e($transaction->type); ?>

                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="<?php echo e($transaction->amount >= 0 ? 'text-success' : 'text-danger'); ?>">
                                                <?php echo e(number_format(abs($transaction->amount))); ?> <?php echo e(trans('messages.currency_unit')); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($transaction->description ?: trans('messages.No Description')); ?></td>
                                        <td><?php echo e($transaction->created_at->format('Y/m/d H:i')); ?></td>
                                        <td>
                                            <span class="badge bg-label-<?php echo e($transaction->status == 'completed' ? 'success' : 'warning'); ?>">
                                                <?php echo e($transaction->status == 'completed' ? trans('messages.Completed') : trans('messages.'.$transaction->status)); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3 mb-3">
                            <?php echo e($transactions->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="bx bx-info-circle bx-lg text-primary mb-2"></i>
                            <p><?php echo e(trans('messages.No transactions found')); ?></p>
                            <a href="<?php echo e(route('wallet.showDepositForm')); ?>" class="btn btn-primary">
                                <i class="bx bx-plus-circle me-1"></i><?php echo e(trans('messages.Charge Wallet')); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/wallet/index.blade.php ENDPATH**/ ?>