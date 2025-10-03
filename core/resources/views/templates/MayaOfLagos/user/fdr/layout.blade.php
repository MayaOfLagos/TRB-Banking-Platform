@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- FDR Management Layout -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-0 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">@lang('Fixed Deposit Receipt')</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">@lang('Manage your fixed deposit investments and track your returns')</p>
                </div>
                
                <!-- Navigation Tabs -->
                <div class="flex bg-white dark:bg-gray-800 rounded-xl p-1 shadow-lg border border-gray-200 dark:border-gray-700">
                    <a href="{{ route('user.fdr.list') }}" 
                       class="flex items-center px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ menuActive('user.fdr.list') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <i class="las la-list-ul text-lg mr-2"></i>
                        @lang('My FDR List')
                    </a>
                    <a href="{{ route('user.fdr.plans') }}" 
                       class="flex items-center px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ menuActive('user.fdr.plans') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <i class="las la-chart-line text-lg mr-2"></i>
                        @lang('FDR Plans')
                    </a>
                </div>
            </div>
        </div>

        <!-- FDR Content -->
        @yield('fdr-content')
    </div>
</div>

@endsection

@push('style')
<style>
.fdr-card {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.fdr-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

.nav-tab-active {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-running {
    background-color: rgba(34, 197, 94, 0.1);
    color: rgb(34, 197, 94);
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.status-closed {
    background-color: rgba(107, 114, 128, 0.1);
    color: rgb(107, 114, 128);
    border: 1px solid rgba(107, 114, 128, 0.3);
}

@media (max-width: 768px) {
    .fdr-nav-tabs {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .fdr-nav-tabs a {
        justify-content: center;
        width: 100%;
    }
}

@media (prefers-color-scheme: dark) {
    .fdr-card {
        background-color: rgb(31, 41, 55);
        border-color: rgb(75, 85, 99);
    }
    
    .fdr-card:hover {
        border-color: rgba(96, 165, 250, 0.3);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }
}

.fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush