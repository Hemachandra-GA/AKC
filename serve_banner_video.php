<?php
/**
 * Ultra-Fast Video Streamer with Byte-Range Support (HTTP 206 Partial Content)
 * Enables instant video startup on Chrome, Safari, iOS, and Android
 */

// Turn off output buffering to stream directly
if (ob_get_level()) {
    ob_end_clean();
}

$file = __DIR__ . '/assets/videos/home-page-banner-fast.mp4';
if (!file_exists($file)) {
    $file = __DIR__ . '/assets/videos/home-page-banner.mp4';
}
if (!file_exists($file)) {
    $file = __DIR__ . '/assets/videos/home-page-banner.mov';
}

if (!file_exists($file)) {
    http_response_code(404);
    exit("Video file not found");
}

$fp = @fopen($file, 'rb');
if (!$fp) {
    http_response_code(500);
    exit("Unable to read video file");
}

$size   = filesize($file);
$length = $size;
$start  = 0;
$end    = $size - 1;

$etag = md5($file . $size . filemtime($file));
header('ETag: "' . $etag . '"');
header('Accept-Ranges: bytes');
header('Cache-Control: public, max-age=604800, immutable');

// Check If-None-Match
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH'], '"') === $etag) {
    http_response_code(304);
    fclose($fp);
    exit;
}

$mime = 'video/mp4';

if (isset($_SERVER['HTTP_RANGE'])) {
    $c_start = $start;
    $c_end   = $end;

    list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
    if (strpos($range, ',') !== false) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes */$size");
        fclose($fp);
        exit;
    }

    if ($range === '-') {
        $c_start = $size - substr($range, 1);
    } else {
        $range  = explode('-', $range);
        $c_start = (int)$range[0];
        $c_end   = (isset($range[1]) && is_numeric($range[1])) ? (int)$range[1] : $size - 1;
    }

    $c_end = ($c_end > $end) ? $end : $c_end;
    if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes */$size");
        fclose($fp);
        exit;
    }

    $start  = $c_start;
    $end    = $c_end;
    $length = $end - $start + 1;
    fseek($fp, $start);
    header('HTTP/1.1 206 Partial Content');
} else {
    // Deliver initial 2MB chunk for instant preview if no range requested
    $maxInitial = 2 * 1024 * 1024;
    if ($length > $maxInitial) {
        $end = $maxInitial - 1;
        $length = $maxInitial;
        header('HTTP/1.1 206 Partial Content');
    } else {
        header('HTTP/1.1 200 OK');
    }
}

header("Content-Range: bytes $start-$end/$size");
header("Content-Type: $mime");
header("Content-Length: " . $length);

$buffer = 256 * 1024; // 256KB buffer
while (!feof($fp) && ($pos = ftell($fp)) <= $end) {
    if ($pos + $buffer > $end) {
        $buffer = $end - $pos + 1;
    }
    set_time_limit(0);
    echo fread($fp, $buffer);
    flush();
}
fclose($fp);
exit;

