<?php
/**
 * Fast-Start MP4 / MOV optimizer and Video Streamer
 */

function inspectAndOptimizeVideo($sourceFile, $destFile) {
    if (!file_exists($sourceFile)) {
        return ['error' => 'Source file does not exist'];
    }

    $filesize = filesize($sourceFile);
    $fp = fopen($sourceFile, 'rb');
    if (!$fp) {
        return ['error' => 'Cannot open source file'];
    }

    $atoms = [];
    $offset = 0;
    while ($offset < $filesize) {
        fseek($fp, $offset, SEEK_SET);
        $header = fread($fp, 8);
        if (strlen($header) < 8) break;
        
        $size = unpack('N', substr($header, 0, 4))[1];
        $type = substr($header, 4, 4);

        $atomSize = $size;
        $headerSize = 8;
        if ($size === 1) { // 64-bit extended size
            $ext = fread($fp, 8);
            $high = unpack('N', substr($ext, 0, 4))[1];
            $low = unpack('N', substr($ext, 4, 4))[1];
            $atomSize = ($high * 4294967296) + $low;
            $headerSize = 16;
        } elseif ($size === 0) {
            $atomSize = $filesize - $offset;
        }

        $atoms[] = [
            'type' => $type,
            'offset' => $offset,
            'size' => $atomSize,
            'headerSize' => $headerSize
        ];

        if ($atomSize <= 0) break;
        $offset += $atomSize;
    }

    $moovIndex = -1;
    $mdatIndex = -1;
    foreach ($atoms as $i => $atom) {
        if ($atom['type'] === 'moov') $moovIndex = $i;
        if ($atom['type'] === 'mdat') $mdatIndex = $i;
    }

    $alreadyFastStart = ($moovIndex !== -1 && $mdatIndex !== -1 && $moovIndex < $mdatIndex);

    // If moov is AFTER mdat, that's why it was loading slowly!
    // We can relocate moov before mdat (qtfaststart)
    $result = [
        'filesize_bytes' => $filesize,
        'filesize_mb' => round($filesize / 1048576, 2),
        'atoms' => array_map(function($a) { return $a['type'] . ' (' . round($a['size']/1024, 1) . ' KB)'; }, $atoms),
        'moov_index' => $moovIndex,
        'mdat_index' => $mdatIndex,
        'already_fast_start' => $alreadyFastStart,
    ];

    if (!$alreadyFastStart && $moovIndex !== -1 && $mdatIndex !== -1) {
        // Read moov atom into memory
        $moovAtom = $atoms[$moovIndex];
        fseek($fp, $moovAtom['offset'], SEEK_SET);
        $moovData = fread($fp, $moovAtom['size']);

        // How much moov will shift forward
        // In the new file: ftyp (if any) + other leading atoms + moov + mdat...
        $shift = $moovAtom['size'];

        // Adjust chunk offsets inside moov atom (stco / co64)
        // Scan moov for 'stco' (32-bit chunk offset table) and 'co64' (64-bit)
        $pos = 0;
        $moovLen = strlen($moovData);
        while ($pos < $moovLen - 8) {
            $tag = substr($moovData, $pos, 4);
            if ($tag === 'stco') {
                $entryCount = unpack('N', substr($moovData, $pos + 8, 4))[1];
                $tableOffset = $pos + 12;
                for ($e = 0; $e < $entryCount; $e++) {
                    $entryPos = $tableOffset + ($e * 4);
                    if ($entryPos + 4 > $moovLen) break;
                    $currentOffset = unpack('N', substr($moovData, $entryPos, 4))[1];
                    $newOffset = $currentOffset + $shift;
                    $moovData = substr_replace($moovData, pack('N', $newOffset), $entryPos, 4);
                }
            } elseif ($tag === 'co64') {
                $entryCount = unpack('N', substr($moovData, $pos + 8, 4))[1];
                $tableOffset = $pos + 12;
                for ($e = 0; $e < $entryCount; $e++) {
                    $entryPos = $tableOffset + ($e * 8);
                    if ($entryPos + 8 > $moovLen) break;
                    $high = unpack('N', substr($moovData, $entryPos, 4))[1];
                    $low = unpack('N', substr($moovData, $entryPos + 4, 4))[1];
                    $currentOffset = ($high * 4294967296) + $low;
                    $newOffset = $currentOffset + $shift;
                    $newHigh = (int)($newOffset / 4294967296);
                    $newLow = $newOffset % 4294967296;
                    $moovData = substr_replace($moovData, pack('NN', $newHigh, $newLow), $entryPos, 8);
                }
            }
            $pos++;
        }

        // Write new file
        $outFp = fopen($destFile, 'wb');
        if ($outFp) {
            // 1. Write ftyp (if present before mdat)
            for ($i = 0; $i < $mdatIndex; $i++) {
                if ($i === $moovIndex) continue;
                $a = $atoms[$i];
                fseek($fp, $a['offset'], SEEK_SET);
                $data = fread($fp, $a['size']);
                // Convert ftyp to isom/mp42 if desired
                if ($a['type'] === 'ftyp' && substr($data, 8, 4) === 'qt  ') {
                    $data = substr_replace($data, 'isom', 8, 4);
                }
                fwrite($outFp, $data);
            }

            // 2. Write shifted moov
            fwrite($outFp, $moovData);

            // 3. Write mdat and remaining atoms
            for ($i = $mdatIndex; $i < count($atoms); $i++) {
                if ($i === $moovIndex) continue;
                $a = $atoms[$i];
                fseek($fp, $a['offset'], SEEK_SET);
                $remaining = $a['size'];
                while ($remaining > 0) {
                    $chunk = fread($fp, min($remaining, 1048576));
                    if ($chunk === false || strlen($chunk) === 0) break;
                    fwrite($outFp, $chunk);
                    $remaining -= strlen($chunk);
                }
            }
            fclose($outFp);
            $result['optimized_created'] = true;
            $result['dest_size_mb'] = round(filesize($destFile) / 1048576, 2);
        }
    } else {
        $result['optimized_created'] = false;
    }

    fclose($fp);
    return $result;
}

$source = __DIR__ . '/assets/videos/home-page-banner.mov';
$dest = __DIR__ . '/assets/videos/home-page-banner-fast.mp4';
$res = inspectAndOptimizeVideo($source, $dest);
file_put_contents(__DIR__ . '/optimize_result.json', json_encode($res, JSON_PRETTY_PRINT));

