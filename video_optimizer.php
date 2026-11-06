<?php
$movPath = __DIR__ . '/assets/videos/home-page-banner.mov';
$info = [
    'exists' => file_exists($movPath),
    'size_bytes' => file_exists($movPath) ? filesize($movPath) : 0,
    'size_mb' => file_exists($movPath) ? round(filesize($movPath) / (1024 * 1024), 2) : 0,
    'has_ffmpeg' => false,
    'other_videos' => glob(__DIR__ . '/assets/videos/*'),
    'banner_images' => glob(__DIR__ . '/assets/images/*banner*'),
];

if (function_exists('exec')) {
    @exec('ffmpeg -version', $out, $ret);
    if ($ret === 0) {
        $info['has_ffmpeg'] = true;
    }
}

file_put_contents(__DIR__ . '/video_info.json', json_encode($info, JSON_PRETTY_PRINT));
echo "Done";

