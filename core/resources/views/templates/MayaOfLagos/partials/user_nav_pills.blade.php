<!-- Premium User Navigation Pills -->
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6">
    <!-- Mobile Navigation Dropdown -->
    <div class="md:hidden">
        <div class="relative">
            <button type="button" 
                    class="w-full px-6 py-4 text-left bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 flex items-center justify-between"
                    onclick="toggleMobileNav()">
                <div class="flex items-center space-x-3">
                    <i class="{{ $currentPageIcon ?? 'las la-user-circle' }} text-lg text-primary-600 dark:text-primary-400"></i>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $currentPageTitle ?? __('Profile Setting') }}</span>
                </div>
                <i class="las la-chevron-down text-gray-400 transition-transform duration-200" id="navChevron"></i>
            </button>
            
            <div id="mobileNavMenu" class="hidden position-relative top-full left-0 right-0 bg-white dark:bg-gray-800 border-l border-r border-b border-gray-200 dark:border-gray-700 rounded-b-2xl shadow-xl z-[9999] overflow-hidden">
                <!-- Profile Setting -->
                <a href="{{ route('user.profile.setting') }}" 
                   class="flex items-center px-6 py-4 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-600 {{ request()->routeIs('user.profile.setting') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-semibold' : '' }}">
                    <i class="las la-user-circle text-lg mr-3"></i>
                    <span>@lang('Profile Setting')</span>
                </a>

                <!-- Change Password -->
                <a href="{{ route('user.change.password') }}" 
                   class="flex items-center px-6 py-4 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-600 {{ request()->routeIs('user.change.password') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-semibold' : '' }}">
                    <i class="las la-key text-lg mr-3"></i>
                    <span>@lang('Change Password')</span>
                </a>

                <!-- 2FA Security -->
                <a href="{{ route('user.twofactor') }}" 
                   class="flex items-center px-6 py-4 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ request()->routeIs('user.twofactor') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-semibold' : '' }}">
                    <i class="las la-shield-alt text-lg mr-3"></i>
                    <span>@lang('2FA Security')</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Desktop Navigation Pills -->
    <div class="hidden md:flex">
        <div class="flex w-full bg-gray-50 dark:bg-gray-700/30 p-1 rounded-2xl">
            <!-- Profile Setting Pill -->
            <a href="{{ route('user.profile.setting') }}" 
               class="flex-1 px-6 py-3 text-center font-medium rounded-xl transition-all duration-300 flex items-center justify-center space-x-2 {{ request()->routeIs('user.profile.setting') ? 'bg-white dark:bg-gray-800 text-primary-700 dark:text-primary-300 shadow-sm border border-gray-200 dark:border-gray-600' : 'text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-white/50 dark:hover:bg-gray-800/50' }}">
                <i class="las la-user-circle text-lg"></i>
                <span>@lang('Profile Setting')</span>
            </a>

            <!-- Change Password Pill -->
            <a href="{{ route('user.change.password') }}" 
               class="flex-1 px-6 py-3 text-center font-medium rounded-xl transition-all duration-300 flex items-center justify-center space-x-2 {{ request()->routeIs('user.change.password') ? 'bg-white dark:bg-gray-800 text-primary-700 dark:text-primary-300 shadow-sm border border-gray-200 dark:border-gray-600' : 'text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-white/50 dark:hover:bg-gray-800/50' }}">
                <i class="las la-key text-lg"></i>
                <span>@lang('Change Password')</span>
            </a>

            <!-- 2FA Security Pill -->
            <a href="{{ route('user.twofactor') }}" 
               class="flex-1 px-6 py-3 text-center font-medium rounded-xl transition-all duration-300 flex items-center justify-center space-x-2 {{ request()->routeIs('user.twofactor') ? 'bg-white dark:bg-gray-800 text-primary-700 dark:text-primary-300 shadow-sm border border-gray-200 dark:border-gray-600' : 'text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-white/50 dark:hover:bg-gray-800/50' }}">
                <i class="las la-shield-alt text-lg"></i>
                <span>@lang('2FA Security')</span>
            </a>
        </div>
    </div>
</div>

@push('script')
<script>
    function toggleMobileNav() {
        const menu = document.getElementById('mobileNavMenu');
        const chevron = document.getElementById('navChevron');
        
        if (menu.classList.contains('hidden')) {
            // Show the menu
            menu.classList.remove('hidden');
            // Add a small delay to ensure the element is rendered before animation
            setTimeout(() => {
                menu.style.opacity = '0';
                menu.style.transform = 'translateY(-10px)';
                menu.style.transition = 'all 0.2s ease-out';
                
                // Trigger animation
                requestAnimationFrame(() => {
                    menu.style.opacity = '1';
                    menu.style.transform = 'translateY(0)';
                });
            }, 10);
            
            chevron.style.transform = 'rotate(180deg)';
        } else {
            // Hide the menu with animation
            menu.style.opacity = '0';
            menu.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                menu.classList.add('hidden');
                menu.style.opacity = '';
                menu.style.transform = '';
                menu.style.transition = '';
            }, 200);
            
            chevron.style.transform = 'rotate(0deg)';
        }
    }

    // Close mobile nav when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('mobileNavMenu');
        const button = event.target.closest('button[onclick="toggleMobileNav()"]');
        
        if (!button && !menu.contains(event.target) && !menu.classList.contains('hidden')) {
            // Close with animation
            menu.style.opacity = '0';
            menu.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                menu.classList.add('hidden');
                menu.style.opacity = '';
                menu.style.transform = '';
                menu.style.transition = '';
            }, 200);
            
            document.getElementById('navChevron').style.transform = 'rotate(0deg)';
        }
    });

    // Add escape key handler
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const menu = document.getElementById('mobileNavMenu');
            if (!menu.classList.contains('hidden')) {
                toggleMobileNav();
            }
        }
    });
</script>
@endpush