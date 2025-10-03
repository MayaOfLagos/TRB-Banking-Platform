/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "../../core/resources/views/templates/MayaOfLagos/**/*.blade.php",
        "../../core/resources/views/templates/MayaOfLagos/**/*.php",
        "./js/**/*.js"
    ],
    safelist: [
        // Custom component classes
        'btn-primary', 'btn-secondary', 'btn-outline', 'card', 'gradient-bg', 'text-gradient',
        'navbar', 'hero-section', 'section-padding', 'container-custom', 'form-input',
        'animate-float', 'animate-slide-up', 'lagos-pattern', 'african-text-shadow',

        // Legacy Bootstrap modal classes (temporary for compatibility)
        'modal-dialog', 'modal-dialog-centered', 'modal-content', 'modal-header', 'modal-body', 'modal-title',

        // Core layout classes
        'bg-white', 'bg-gray-50', 'bg-gray-100', 'bg-gray-200',
        'text-gray-600', 'text-gray-700', 'text-gray-800', 'text-gray-900',
        'border', 'border-gray-200', 'border-gray-300',

        // Teal colors (primary)
        'bg-teal-50', 'bg-teal-100', 'bg-teal-600', 'bg-teal-700',
        'text-teal-600', 'text-teal-700', 'text-teal-800',
        'border-teal-200', 'border-teal-300', 'hover:bg-teal-700',

        // Orange colors (secondary)
        'bg-orange-50', 'bg-orange-100', 'bg-orange-600', 'bg-orange-700',
        'text-orange-600', 'text-orange-700', 'text-orange-800',
        'border-orange-200', 'border-orange-300', 'hover:bg-orange-700',

        // Other theme colors
        'bg-blue-50', 'bg-blue-100', 'bg-blue-600', 'bg-blue-700',
        'text-blue-600', 'text-blue-700', 'text-blue-800',
        'bg-green-50', 'bg-green-100', 'bg-green-600', 'bg-green-700',
        'text-green-600', 'text-green-700', 'text-green-800',
        'bg-red-50', 'bg-red-100', 'bg-red-600', 'bg-red-700',
        'text-red-600', 'text-red-700', 'text-red-800',
        'bg-yellow-50', 'bg-yellow-100', 'bg-yellow-600', 'bg-yellow-700',
        'text-yellow-600', 'text-yellow-700', 'text-yellow-800',
        'bg-purple-50', 'bg-purple-100', 'bg-purple-600', 'bg-purple-700',
        'text-purple-600', 'text-purple-700', 'text-purple-800',

        // Layout & spacing
        'rounded-lg', 'rounded-xl', 'rounded-2xl', 'rounded-full',
        'p-2', 'p-3', 'p-4', 'p-6', 'p-8',
        'px-2', 'px-3', 'px-4', 'px-6', 'px-8',
        'py-2', 'py-3', 'py-4', 'py-6', 'py-8',
        'mb-2', 'mb-3', 'mb-4', 'mb-6', 'mb-8',
        'mt-2', 'mt-3', 'mt-4', 'mt-6', 'mt-8',
        'ml-2', 'ml-3', 'mr-2', 'mr-3',
        'space-x-3', 'space-y-2', 'space-y-3', 'space-y-4',
        'gap-4', 'gap-6',

        // Grid & flex
        'grid', 'grid-cols-1', 'grid-cols-2', 'grid-cols-3', 'grid-cols-12',
        'lg:grid-cols-2', 'lg:grid-cols-4', 'lg:grid-cols-8', 'lg:grid-cols-12',
        'md:grid-cols-2', 'md:grid-cols-3', 'md:grid-cols-4',
        'col-span-1', 'col-span-2', 'col-span-3', 'col-span-4', 'col-span-5', 'col-span-6',
        'col-span-7', 'col-span-8', 'col-span-9', 'col-span-10', 'col-span-11', 'col-span-12',
        'flex', 'items-center', 'justify-between', 'justify-center',
        'flex-col', 'flex-row', 'flex-1', 'flex-shrink-0',

        // Sizing
        'w-full', 'w-8', 'w-10', 'w-12', 'w-16', 'w-20', 'w-64',
        'h-8', 'h-10', 'h-12', 'h-16', 'h-20', 'h-screen',
        'max-w-md', 'max-w-lg', 'max-w-xl', 'max-w-2xl',

        // Typography
        'text-xs', 'text-sm', 'text-base', 'text-lg', 'text-xl', 'text-2xl', 'text-3xl',
        'font-medium', 'font-semibold', 'font-bold',
        'text-center', 'text-left', 'text-right',

        // Interactive states
        'hover:bg-teal-100', 'hover:bg-orange-100', 'hover:bg-blue-100',
        'hover:bg-gray-50', 'hover:text-teal-700', 'hover:text-orange-700',
        'focus:ring-2', 'focus:ring-teal-500', 'focus:border-transparent',
        'transition-colors', 'transition-all', 'duration-300',
        'transform', 'hover:scale-105',

        // Display & positioning
        'block', 'inline-block', 'hidden', 'lg:block', 'md:block', 'sm:block',
        'fixed', 'absolute', 'relative', 'top-0', 'left-0', 'right-0',
        'z-50', 'overflow-hidden', 'overflow-y-auto',

        // Shadow & effects
        'shadow-lg', 'shadow-xl', 'border-t', 'border-b', 'border-l', 'border-r',
        'bg-gradient-to-br', 'from-teal-50', 'to-orange-50',

        // Forms
        'focus:ring-4', 'focus:ring-teal-200', 'disabled:bg-gray-400',
        'disabled:cursor-not-allowed', 'cursor-pointer',

        // Mobile responsive
        'lg:translate-x-0', '-translate-x-full', 'lg:hidden',
        'sm:flex-row', 'sm:items-center', 'sm:justify-between',
        'md:col-span-2', 'lg:col-span-4', 'lg:col-span-8'
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#16a085',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
                secondary: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f39c12',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                accent: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#e74c3c',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
                dark: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#2c3e50',
                    900: '#0f172a',
                },
                light: {
                    50: '#ffffff',
                    100: '#fefefe',
                    200: '#fcfcfc',
                    300: '#f8f9fa',
                    400: '#f1f3f4',
                    500: '#ecf0f1',
                    600: '#d5dbdb',
                    700: '#bdc3c7',
                    800: '#95a5a6',
                    900: '#7f8c8d',
                }
            },
            fontFamily: {
                'sans': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                'heading': ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            fontSize: {
                'xs': '0.75rem',
                'sm': '0.875rem',
                'base': '1rem',
                'lg': '1.125rem',
                'xl': '1.25rem',
                '2xl': '1.5rem',
                '3xl': '1.875rem',
                '4xl': '2.25rem',
                '5xl': '3rem',
                '6xl': '3.75rem',
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            borderRadius: {
                'xl': '1rem',
                '2xl': '1.5rem',
                '3xl': '2rem',
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                'hard': '0 10px 40px -10px rgba(0, 0, 0, 0.2)',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}