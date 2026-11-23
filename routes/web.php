<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentSubmissionController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\ApprovalPolicyController;
use App\Http\Controllers\SecretaryApprovalController;
use App\Http\Controllers\PublicVerificationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        AuthController::class,
        'showLoginForm'
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login'
    ])->name('login.process');
});

Route::post('/logout', [
    AuthController::class,
    'logout'
])->middleware('auth')->name('logout');


Route::middleware(['auth', 'role:secretary'])
    ->prefix('secretary')
    ->name('secretary.')
    ->group(function () {
        Route::get('/dashboard', [
            DashboardController::class,
            'secretary'
        ])->name('dashboard');

        Route::get('/documents', [
            DocumentController::class,
            'index'
        ])->name('documents.index');

        Route::get('/documents/create', [
            DocumentController::class,
            'create'
        ])->name('documents.create');

        Route::post('/documents', [
            DocumentController::class,
            'store'
        ])->name('documents.store');

        Route::get('/documents/{document}', [
            DocumentController::class,
            'show'
        ])->name('documents.show');

        Route::post('/documents/{document}/submit', [
            DocumentSubmissionController::class,
            'store'
        ])->name('documents.submit');

        Route::get('/documents/{document}/revise', [
            DocumentController::class,
            'revise'
        ])->name('documents.revise');

        Route::post('/documents/{document}/revise', [
            DocumentController::class,
            'storeRevision'
        ])->name('documents.revise.store');

        Route::post('/documents/{document}/publish', [
            DocumentController::class,
            'publish'
        ])->name('documents.publish');

        Route::get('/approvals', [
            SecretaryApprovalController::class,
            'index'
        ])->name('approvals.index');
    });


Route::middleware(['auth', 'role:approver'])
    ->prefix('approver')
    ->name('approver.')
    ->group(function () {
        Route::get('/dashboard', [
            DashboardController::class,
            'approver'
        ])->name('dashboard');

        Route::get('/approvals', [
            ApprovalController::class,
            'index'
        ])->name('approvals.index');

        // Approval History
        // Must be declared before /approvals/{documentApproval}.
        Route::get('/approvals/history', [
            ApprovalController::class,
            'history'
        ])->name('approvals.history');

        Route::get('/approvals/{documentApproval}', [
            ApprovalController::class,
            'show'
        ])->name('approvals.show');

        Route::post('/approvals/{documentApproval}/decision', [
            ApprovalController::class,
            'decide'
        ])->name('approvals.decide');

        Route::get('/approvals/{documentApproval}/download', [
            ApprovalController::class,
            'download'
        ])->name('approvals.download');
    });


Route::middleware(['auth', 'role:administrator'])
    ->prefix('administrator')
    ->name('administrator.')
    ->group(function () {
        Route::get('/dashboard', [
            DashboardController::class,
            'administrator'
        ])->name('dashboard');

        Route::get('/audit-logs', [
            AuditLogController::class,
            'index'
        ])->name('audit-logs.index');

        Route::get('/audit-logs/{auditLog}', [
            AuditLogController::class,
            'show'
        ])->name('audit-logs.show');

        Route::get('/users', [
            UserController::class,
            'index'
        ])->name('users.index');

        Route::get('/users/create', [
            UserController::class,
            'create'
        ])->name('users.create');

        Route::post('/users', [
            UserController::class,
            'store'
        ])->name('users.store');

        Route::get('/users/{user}/edit', [
            UserController::class,
            'edit'
        ])->name('users.edit');

        Route::put('/users/{user}', [
            UserController::class,
            'update'
        ])->name('users.update');

        Route::get('/wallets', [
            WalletController::class,
            'index'
        ])->name('wallets.index');

        Route::get('/wallets/create', [
            WalletController::class,
            'create'
        ])->name('wallets.create');

        Route::post('/wallets', [
            WalletController::class,
            'store'
        ])->name('wallets.store');

        Route::get('/wallets/{wallet}/edit', [
            WalletController::class,
            'edit'
        ])->name('wallets.edit');

        Route::put('/wallets/{wallet}', [
            WalletController::class,
            'update'
        ])->name('wallets.update');

        Route::get('/document-types', [
            DocumentTypeController::class,
            'index'
        ])->name('document-types.index');

        Route::get('/document-types/create', [
            DocumentTypeController::class,
            'create'
        ])->name('document-types.create');

        Route::post('/document-types', [
            DocumentTypeController::class,
            'store'
        ])->name('document-types.store');

        Route::get('/document-types/{documentType}/edit', [
            DocumentTypeController::class,
            'edit'
        ])->name('document-types.edit');

        Route::put('/document-types/{documentType}', [
            DocumentTypeController::class,
            'update'
        ])->name('document-types.update');

        Route::get('/approval-policies', [ApprovalPolicyController::class, 'index'])
            ->name('approval-policies.index');

        Route::get('/approval-policies/create', [ApprovalPolicyController::class, 'create'])
            ->name('approval-policies.create');

        Route::post('/approval-policies', [ApprovalPolicyController::class, 'store'])
            ->name('approval-policies.store');

        Route::get('/approval-policies/{approvalPolicy}/edit', [ApprovalPolicyController::class, 'edit'])
            ->name('approval-policies.edit');

        Route::put('/approval-policies/{approvalPolicy}', [ApprovalPolicyController::class, 'update'])
            ->name('approval-policies.update');
    });


Route::middleware(['auth', 'role:verifier'])
    ->prefix('verifier')
    ->name('verifier.')
    ->group(function () {
        Route::get('/dashboard', [
            DashboardController::class,
            'verifier'
        ])->name('dashboard');
    });

Route::get('/verify/{token}', [PublicVerificationController::class, 'show'])
    ->whereUuid('token')
    ->name('verification.show');