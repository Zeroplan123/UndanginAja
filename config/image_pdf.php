<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HTML to Image to PDF Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for HTML to Image to PDF conversion system
    |
    */

    // Enable/disable the image to PDF system
    'enabled' => env('IMAGE_PDF_ENABLED', true),

    // Default conversion method priority
    'conversion_priority' => [
        'chrome',       // Chrome/Chromium (recommended)
        'wkhtmltoimage', // wkhtmltoimage
        'browsershot'   // Browsershot with Puppeteer
    ],

    // Chrome/Chromium settings
    'chrome' => [
        'enabled' => env('CHROME_ENABLED', true),
        'timeout' => env('CHROME_TIMEOUT', 60),
        'window_size' => [
            'width' => env('CHROME_WINDOW_WIDTH', 1200),
            'height' => env('CHROME_WINDOW_HEIGHT', 1600)
        ],
        'device_scale_factor' => env('CHROME_DEVICE_SCALE_FACTOR', 2),
        'args' => [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--disable-extensions',
            '--disable-plugins'
        ]
    ],

    // wkhtmltoimage settings
    'wkhtmltoimage' => [
        'enabled' => env('WKHTMLTOIMAGE_ENABLED', true),
        'path' => env('WKHTMLTOIMAGE_PATH', null),
        'timeout' => env('WKHTMLTOIMAGE_TIMEOUT', 60),
        'width' => env('WKHTMLTOIMAGE_WIDTH', 1200),
        'height' => env('WKHTMLTOIMAGE_HEIGHT', 1600),
        'quality' => env('WKHTMLTOIMAGE_QUALITY', 100),
        'format' => env('WKHTMLTOIMAGE_FORMAT', 'png')
    ],

    // Browsershot settings
    'browsershot' => [
        'enabled' => env('BROWSERSHOT_ENABLED', true),
        'timeout' => env('BROWSERSHOT_TIMEOUT', 60),
        'window_size' => [
            'width' => env('BROWSERSHOT_WINDOW_WIDTH', 1200),
            'height' => env('BROWSERSHOT_WINDOW_HEIGHT', 1600)
        ],
        'device_scale_factor' => env('BROWSERSHOT_DEVICE_SCALE_FACTOR', 2),
        'quality' => env('BROWSERSHOT_QUALITY', 100),
        'format' => env('BROWSERSHOT_FORMAT', 'png')
    ],

    // Image settings
    'image' => [
        'format' => env('IMAGE_FORMAT', 'png'),
        'quality' => env('IMAGE_QUALITY', 100),
        'max_width' => env('IMAGE_MAX_WIDTH', 1200),
        'max_height' => env('IMAGE_MAX_HEIGHT', 1600)
    ],

    // PDF settings
    'pdf' => [
        'paper_size' => env('PDF_PAPER_SIZE', 'A4'),
        'orientation' => env('PDF_ORIENTATION', 'portrait'),
        'margin' => env('PDF_MARGIN', 20), // points
        'dpi' => env('PDF_DPI', 150),
        'quality' => env('PDF_QUALITY', 100)
    ],

    // File management
    'storage' => [
        'temp_image_dir' => storage_path('app/temp/images'),
        'temp_pdf_dir' => storage_path('app/temp/pdf'),
        'cleanup_hours' => env('IMAGE_PDF_CLEANUP_HOURS', 24),
        'auto_cleanup' => env('IMAGE_PDF_AUTO_CLEANUP', true)
    ],

    // Performance settings
    'performance' => [
        'memory_limit' => env('IMAGE_PDF_MEMORY_LIMIT', '512M'),
        'max_execution_time' => env('IMAGE_PDF_MAX_EXECUTION_TIME', 120),
        'concurrent_conversions' => env('IMAGE_PDF_CONCURRENT_CONVERSIONS', 3)
    ],

    // Logging
    'logging' => [
        'enabled' => env('IMAGE_PDF_LOGGING', true),
        'level' => env('IMAGE_PDF_LOG_LEVEL', 'info'),
        'log_performance' => env('IMAGE_PDF_LOG_PERFORMANCE', true)
    ],

    // Fallback options
    'fallback' => [
        'enabled' => env('IMAGE_PDF_FALLBACK_ENABLED', true),
        'fallback_to_dompdf' => env('IMAGE_PDF_FALLBACK_DOMPDF', true)
    ],

    // HTML optimization
    'html_optimization' => [
        'enabled' => env('IMAGE_PDF_HTML_OPTIMIZATION', true),
        'remove_animations' => env('IMAGE_PDF_REMOVE_ANIMATIONS', true),
        'remove_transforms' => env('IMAGE_PDF_REMOVE_TRANSFORMS', true),
        'remove_filters' => env('IMAGE_PDF_REMOVE_FILTERS', true),
        'optimize_fonts' => env('IMAGE_PDF_OPTIMIZE_FONTS', true),
        'replace_emojis' => env('IMAGE_PDF_REPLACE_EMOJIS', true)
    ]
];
