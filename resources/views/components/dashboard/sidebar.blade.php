@php
    $role = auth()->user()->role->slug;

    $menus = [
        'secretary' => [
            [
                'label' => 'Overview',
                'icon' => 'bi-grid',
                'active' => request()->routeIs('secretary.dashboard'),
                'url' => route('secretary.dashboard'),
            ],
            [
                'label' => 'Documents',
                'icon' => 'bi-file-earmark-text',
                'active' => request()->routeIs('secretary.documents.*'),
                'url' => route('secretary.documents.index'),
            ],
            [
                'label' => 'Approvals',
                'icon' => 'bi-pen',
                'active' => request()->routeIs('secretary.approvals.*'),
                'url' => route('secretary.approvals.index'),
            ],
            ['label' => 'Verify', 'icon' => 'bi-shield-lock'],
            ['label' => 'Settings', 'icon' => 'bi-sliders'],
        ],

        'approver' => [
            [
                'label' => 'Overview',
                'icon' => 'bi-grid',
                'active' => request()->routeIs('approver.dashboard'),
                'url' => route('approver.dashboard'),
            ],
            [
                'label' => 'Assigned Documents',
                'icon' => 'bi-file-earmark-text',
                'active' => request()->routeIs(
                    'approver.approvals.index',
                    'approver.approvals.show'
                ),
                'url' => route('approver.approvals.index'),
            ],
            [
                'label' => 'Approval History',
                'icon' => 'bi-clock-history',
                'active' => request()->routeIs('approver.approvals.history'),
                'url' => route('approver.approvals.history'),
            ],
            [
                'label' => 'Settings',
                'icon' => 'bi-sliders',
            ],
        ],

        'administrator' => [
            [
                'label' => 'Overview',
                'icon' => 'bi-grid',
                'active' => request()->routeIs('administrator.dashboard'),
                'url' => route('administrator.dashboard'),
            ],
            [
                'label' => 'Users',
                'icon' => 'bi-people',
                'active' => request()->routeIs('administrator.users.*'),
                'url' => route('administrator.users.index'),
            ],
            [
                'label' => 'Wallets',
                'icon' => 'bi-wallet2',
                'active' => request()->routeIs('administrator.wallets.*'),
                'url' => route('administrator.wallets.index'),
            ],
            [
                'label' => 'Document Types',
                'icon' => 'bi-collection',
                'active' => request()->routeIs('administrator.document-types.*'),
                'url' => route('administrator.document-types.index'),
            ],
            [
                'label' => 'Approval Policies',
                'icon' => 'bi-diagram-3',
                'active' => request()->routeIs('administrator.approval-policies.*'),
                'url' => route('administrator.approval-policies.index'),
            ],
            [
                'label' => 'Audit Logs',
                'icon' => 'bi-journal-text',
                'active' => request()->routeIs('administrator.audit-logs.*'),
                'url' => route('administrator.audit-logs.index'),
            ],
        ],

        'verifier' => [
            ['label' => 'Overview', 'icon' => 'bi-grid', 'active' => true],
            ['label' => 'Verify Document', 'icon' => 'bi-shield-check'],
            ['label' => 'Verification History', 'icon' => 'bi-clock-history'],
        ],
    ];

    $roleMenus = $menus[$role] ?? [];
@endphp

<aside class="sidebar" id="appSidebar">

    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-shield-check"></i>
        </div>

        <span>ApproSeal</span>
    </div>

    <nav class="sidebar-nav">

        @foreach ($roleMenus as $menu)

            @if (isset($menu['url']))
                <a href="{{ $menu['url'] }}"
                   class="sidebar-link {{ !empty($menu['active']) ? 'active' : '' }}">

                    <i class="bi {{ $menu['icon'] }}"></i>
                    <span>{{ $menu['label'] }}</span>

                </a>
            @else
                <span class="sidebar-link sidebar-link-disabled"
                      title="Fitur belum tersedia">

                    <i class="bi {{ $menu['icon'] }}"></i>
                    <span>{{ $menu['label'] }}</span>

                </span>
            @endif

        @endforeach

    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-footer-icon">
            <i class="bi bi-shield-lock"></i>
        </div>

        <div>
            <strong>ApproSeal</strong>
            <small>Document Governance</small>
        </div>
    </div>

</aside>
