<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ gs()->siteName($pageTitle ?? '') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ siteFavicon() }}">
    
    <!-- Tailwind CSS for PDF -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom MayaOfLagos PDF Styles -->
    <link rel="stylesheet" href="{{ asset('assets/templates/MayaOfLagos/css/pdf-styles.css') }}">
    
    <!-- Additional PDF configuration -->
    <style>
        /* Tailwind configuration for PDF */
        @config {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </style>
    
    @stack('pdf-style')
</head>

<body class="pdf-optimized text-rendering-optimized">
    <div class="pdf-container">
        <!-- Header Section -->
        <div class="pdf-header">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <!-- Company Info -->
                    <div class="flex items-center space-x-6">
                        <div class="company-logo">
                            <img src="{{ siteLogo('dark') }}" alt="Logo" class="h-8 w-auto">
                        </div>
                        <div class="company-info">
                            <h1 class="text-2xl font-bold">{{ __(gs()->site_name) }}</h1>
                            <p class="text-blue-100 text-sm mt-1">Financial Services Platform</p>
                        </div>
                    </div>
                    
                    <!-- Document Info -->
                    <div class="text-right">
                        <h2 class="text-xl font-semibold">{{ @$pageTitle }}</h2>
                        <p class="text-blue-100 text-sm mt-1">{{ now()->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
                
                <!-- Filters Information -->
                @if (request()->date || request()->search)
                <div class="filter-info">
                    <div class="flex flex-wrap gap-3">
                        @if (request()->date)
                        <div class="filter-badge">
                            <i class="fas fa-calendar mr-2"></i>
                            <strong>Date Range:</strong> {{ request()->date }}
                        </div>
                        @endif
                        
                        @if (request()->search)
                        <div class="filter-badge">
                            <i class="fas fa-search mr-2"></i>
                            <strong>Search:</strong> {{ request()->search }}
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Content Section -->
        <div class="pdf-content">
            @yield('pdf-content')
        </div>

        <!-- Footer Section -->
        <div class="pdf-footer">
            <div class="footer-content">
                <div>
                    <p><strong>Generated on:</strong> {{ now()->format('F d, Y \a\t g:i A') }}</p>
                </div>
                <div>
                    <p>Powered by <strong>{{ __(gs()->site_name) }}</strong></p>
                </div>
                <div>
                    <p><strong>Page:</strong> 1 of 1</p>
                </div>
            </div>
        </div>
    </div>

    @stack('pdf-script')
</body>
</html>