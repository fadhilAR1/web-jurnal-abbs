<?php

return [
    'mode'                  => env('DOMPDF_MODE', 'utf-8'),
    'defines'               => [
        /**
         * The location of the DOMPDF font directory
         *
         * The location of the directory where DOMPDF will store fonts and font metrics
         * Changing this setting makes DOMPDF use a different font path
         *
         * @var string $fontDir
         */
        'font_dir'          => storage_path('fonts/'),

        /**
         * The location of the DOMPDF temporary directory
         *
         * The location of the directory where DOMPDF will store temporary files
         *
         * @var string $tempDir
         */
        'temp_dir'          => storage_path('temp/'),

        /**
         * ==== IMPORTANT ====
         *
         * dompdf's "chroot" is the value of `open_basedir`. It is automatically
         * filled in by this function, but can be overridden if needed (very rarely
         * you should change this setting).
         *
         * @var string $chroot
         */
        'chroot'            => realpath(base_path()),

        /**
         * Whether to enable font subsetting or not.
         *
         * @var bool $enableFontSubsetting
         */
        'enable_font_subsetting'           => false,

        /**
         * The PDF rendering backend to use
         *
         * Valid values are 'auto', 'pdftk', 'ghostscript', or 'imagick'.
         * If value is 'auto', initPhp() will look for available commands on
         * your PATH to find and load the appropriate one. All production
         * environments should be explicit about which backend to use.
         *
         * @var string $pdfBackend
         */
        'pdf_backend'       => env('DOMPDF_PDF_BACKEND', 'auto'),

        /**
         * The font cache directory
         *
         * @var string $fontCacheDir
         */
        'font_cache'        => storage_path('fonts/'),

        /**
         * Whether to log PPM images or not.
         *
         * @var bool $logOutputFile
         */
        'log_output_file'    => storage_path('logs/dompdf.log'),

        /**
         * Enables disk access for local files.
         *
         * @var bool $enableRemoteFiles
         */
        'enable_html5_parser' => true,
    ],

    /**
     * Orientation of the PDF file ('portrait' or 'landscape')
     *
     * @var string $orientation
     */
    'orientation'           => 'portrait',

    /**
     * Enable inline PHP
     *
     * @var bool $enablePhp
     */
    'enable_php'            => false,

    /**
     * Enable inline Javascript
     *
     * @var bool $enableJavascript
     */
    'enable_javascript'     => true,

    /**
     * A ratio used to scale the final output.
     *
     * @var float|int $scale
     */
    'scale'                 => 1,

    /**
     * Enable xdebug backtrace
     *
     * @var bool $debugKeepTemp
     */
    'debug_keep_temp'       => false,

    /**
     * PDF compression level
     *
     * 0 - no compression
     * 1 - content streams only
     * 2 - FlateDecode streams
     * 3 - all streams
     * 4 - all streams and fonts
     *
     * @var int $compressStreams
     */
    'compress_streams'      => true,

    /**
     * Timeout for server requests in milliseconds
     *
     * @var int $timeout
     */
    'timeout'               => 5000,

    /**
     * Enable CSS float
     *
     * @var bool $enableCssFloat
     */
    'enable_css_float'      => false,

    /**
     * PDF paper size
     *
     * @var string $paperSize
     */
    'paper_size'            => 'A4',

    /**
     * The default paper orientation
     *
     * @var string $defaultPaperOrientation
     */
    'default_paper_orientation' => 'portrait',

    /**
     * The default font family
     *
     * @var string $defaultFont
     */
    'default_font'          => 'serif',

    /**
     * Image DPI
     *
     * @var int $dpi
     */
    'dpi'                   => 96,

    /**
     * Enable inline images
     *
     * @var bool $enableRemoteFiles
     */
    'enable_remote_files'   => true,

    /**
     * Margin bottom in millimeters
     *
     * @var int $marginBottom
     */
    'margin_bottom'         => 0,

    /**
     * Margin left in millimeters
     *
     * @var int $marginLeft
     */
    'margin_left'           => 0,

    /**
     * Margin right in millimeters
     *
     * @var int $marginRight
     */
    'margin_right'          => 0,

    /**
     * Margin top in millimeters
     *
     * @var int $marginTop
     */
    'margin_top'            => 0,

    /**
     * Margin in millimeters
     *
     * @var int $margin
     */
    'margin'                => 0,

    /**
     * Whether to use Unicode true-type fonts
     *
     * @var bool $useUnicodeTrueType
     */
    'use_unicode_true_type' => true,

    /**
     * Set of options to enable for Ghostscript
     *
     * @var string|null $ghostscriptOptions
     */
    'ghostscript_options' => null,
];
