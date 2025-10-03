<?php
header("Content-Type:text/css");

// Get color parameters from URL
$color = $_GET['color'] ?? '#16a085';
$secondColor = $_GET['secondColor'] ?? '#f39c12';

// Function to lighten/darken colors
function adjustBrightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0, min(255, $color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}

// Function to convert hex to rgba
function hexToRgba($hex, $alpha = 1) {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }
    
    $rgb = array_map('hexdec', str_split($hex, 2));
    return "rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, $alpha)";
}

// Generate color variations
$primaryLight = adjustBrightness($color, 30);
$primaryDark = adjustBrightness($color, -30);
$secondaryLight = adjustBrightness($secondColor, 30);
$secondaryDark = adjustBrightness($secondColor, -30);

$primaryRgba10 = hexToRgba($color, 0.1);
$primaryRgba20 = hexToRgba($color, 0.2);
$primaryRgba30 = hexToRgba($color, 0.3);
$secondaryRgba10 = hexToRgba($secondColor, 0.1);
$secondaryRgba20 = hexToRgba($secondColor, 0.2);
?>

/* MayaOfLagos Dynamic Color Scheme */
:root {
    --primary-color: <?php echo $color; ?>;
    --primary-light: <?php echo $primaryLight; ?>;
    --primary-dark: <?php echo $primaryDark; ?>;
    --primary-rgba-10: <?php echo $primaryRgba10; ?>;
    --primary-rgba-20: <?php echo $primaryRgba20; ?>;
    --primary-rgba-30: <?php echo $primaryRgba30; ?>;
    
    --secondary-color: <?php echo $secondColor; ?>;
    --secondary-light: <?php echo $secondaryLight; ?>;
    --secondary-dark: <?php echo $secondaryDark; ?>;
    --secondary-rgba-10: <?php echo $secondaryRgba10; ?>;
    --secondary-rgba-20: <?php echo $secondaryRgba20; ?>;
    
    --gradient-primary: linear-gradient(135deg, <?php echo $color; ?> 0%, <?php echo $secondColor; ?> 100%);
    --gradient-primary-reverse: linear-gradient(135deg, <?php echo $secondColor; ?> 0%, <?php echo $color; ?> 100%);
}

/* Primary Color Applications */
.btn-primary {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.btn-primary:hover {
    background-color: var(--primary-dark) !important;
    border-color: var(--primary-dark) !important;
}

.btn-outline {
    border-color: var(--primary-color) !important;
    color: var(--primary-color) !important;
}

.btn-outline:hover {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.text-primary {
    color: var(--primary-color) !important;
}

.bg-primary {
    background-color: var(--primary-color) !important;
}

.border-primary {
    border-color: var(--primary-color) !important;
}

/* Secondary Color Applications */
.btn-secondary {
    background-color: var(--secondary-color) !important;
    border-color: var(--secondary-color) !important;
}

.btn-secondary:hover {
    background-color: var(--secondary-dark) !important;
    border-color: var(--secondary-dark) !important;
}

.text-secondary {
    color: var(--secondary-color) !important;
}

.bg-secondary {
    background-color: var(--secondary-color) !important;
}

/* Navigation */
.nav-link.active,
.nav-link:hover {
    color: var(--primary-color) !important;
    background-color: var(--primary-rgba-10) !important;
}

/* Links */
a:hover {
    color: var(--primary-color) !important;
}

.footer-link:hover {
    color: var(--primary-color) !important;
}

/* Form Elements */
.form-control:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 3px var(--primary-rgba-20) !important;
}

/* Gradients */
.gradient-bg {
    background: var(--gradient-primary) !important;
}

.text-gradient {
    background: var(--gradient-primary) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

/* Hero Section */
.hero {
    background: linear-gradient(135deg, <?php echo $color; ?> 0%, <?php echo $color; ?> 35%, #2c3e50 100%) !important;
}

/* Counters */
.counter {
    color: var(--secondary-color) !important;
}

/* Cards */
.card:hover {
    border-color: var(--primary-rgba-20) !important;
}

/* Lagos Pattern */
.lagos-pattern {
    background-image: 
        radial-gradient(circle at 25% 25%, var(--primary-rgba-10) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, var(--secondary-rgba-10) 0%, transparent 50%) !important;
}

/* Loading Spinner */
.preloader .bar {
    background: var(--primary-color) !important;
}

.preloader .bar:nth-child(2n) {
    background: var(--secondary-color) !important;
}

/* Back to Top Button */
.back-to-top {
    background-color: var(--primary-color) !important;
}

.back-to-top:hover {
    background-color: var(--primary-dark) !important;
}

/* Testimonial Cards */
.testimonial-card {
    border-left: 4px solid var(--primary-color) !important;
}

/* Section Titles */
.section-title {
    background: var(--gradient-primary) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

/* Custom Teal and Orange Classes for Better Integration */
.text-teal-600 { color: var(--primary-color) !important; }
.text-orange-500 { color: var(--secondary-color) !important; }
.bg-teal-600 { background-color: var(--primary-color) !important; }
.bg-orange-500 { background-color: var(--secondary-color) !important; }
.border-teal-600 { border-color: var(--primary-color) !important; }
.border-orange-500 { border-color: var(--secondary-color) !important; }

.hover\:bg-teal-700:hover { background-color: var(--primary-dark) !important; }
.hover\:bg-orange-600:hover { background-color: var(--secondary-dark) !important; }
.hover\:text-teal-600:hover { color: var(--primary-color) !important; }
.hover\:text-orange-500:hover { color: var(--secondary-color) !important; }

/* Focus States */
.focus\:ring-teal-500:focus {
    --tw-ring-color: var(--primary-rgba-30) !important;
}

.focus\:border-teal-500:focus {
    border-color: var(--primary-color) !important;
}