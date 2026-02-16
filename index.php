<?php
// Single-file file browser with streaming zip, mediainfo, async folder sizes, search/filter
set_time_limit(0);
ignore_user_abort(true);

// --- VERSION ---
define('APP_VERSION', '0.1.0');

// --- CONFIG ---
$BASE_DIR = realpath(__DIR__ . '/files');
if ($BASE_DIR === false) {
    @mkdir(__DIR__ . '/files', 0755, true);
    $BASE_DIR = realpath(__DIR__ . '/files');
}
$MAX_FILES_IN_ZIP = 10000;
$CACHE_DIR = __DIR__ . '/.cache_fb';
if (!is_dir($CACHE_DIR)) @mkdir($CACHE_DIR, 0755, true);

// --- Extension to icon/type mapping ---
function file_icon($name) {
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $map = [
        'jpg'=>'🖼️','jpeg'=>'🖼️','png'=>'🖼️','gif'=>'🖼️','bmp'=>'🖼️','svg'=>'🖼️','webp'=>'🖼️','ico'=>'🖼️','tiff'=>'🖼️','tif'=>'🖼️','avif'=>'🖼️','heic'=>'🖼️','heif'=>'🖼️',
        'mp3'=>'🎵','flac'=>'🎵','wav'=>'🎵','aac'=>'🎵','ogg'=>'🎵','wma'=>'🎵','m4a'=>'🎵','opus'=>'🎵','aiff'=>'🎵',
        'mp4'=>'🎬','mkv'=>'🎬','avi'=>'🎬','mov'=>'🎬','wmv'=>'🎬','flv'=>'🎬','webm'=>'🎬','m4v'=>'🎬','ts'=>'🎬','m2ts'=>'🎬','mpg'=>'🎬','mpeg'=>'🎬',
        'zip'=>'📦','tar'=>'📦','gz'=>'📦','bz2'=>'📦','xz'=>'📦','7z'=>'📦','rar'=>'📦','zst'=>'📦',
        'pdf'=>'📕','doc'=>'📝','docx'=>'📝','odt'=>'📝','rtf'=>'📝','txt'=>'📄','md'=>'📄','csv'=>'📄','log'=>'📄',
        'xls'=>'📊','xlsx'=>'📊','ods'=>'📊',
        'ppt'=>'��️','pptx'=>'📽️','odp'=>'📽️',
        'py'=>'💻','js'=>'💻','php'=>'💻','html'=>'💻','css'=>'💻','json'=>'💻','xml'=>'💻','sh'=>'💻','c'=>'💻','cpp'=>'💻','h'=>'💻','java'=>'💻','rs'=>'💻','go'=>'💻','rb'=>'💻','ts'=>'💻',
        'iso'=>'💿','img'=>'💿','dmg'=>'💿',
        'exe'=>'⚙️','msi'=>'⚙️','deb'=>'⚙️','rpm'=>'⚙️','AppImage'=>'⚙️',
    ];
    return $map[$ext] ?? '📄';
}

function file_category($name) {
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $cats = [
        'image' => ['jpg','jpeg','png','gif','bmp','svg','webp','ico','tiff','tif','avif','heic','heif','psd','raw','cr2','nef','arw','dng'],
        'audio' => ['mp3','flac','wav','aac','ogg','wma','m4a','opus','aiff','ape','alac','mid','midi'],
        'video' => ['mp4','mkv','avi','mov','wmv','flv','webm','m4v','ts','m2ts','mpg','mpeg','3gp','ogv','vob','asf','rm','rmvb','divx'],
        'document' => ['pdf','doc','docx','odt','rtf','ppt','pptx','odp','xls','xlsx','ods','epub'],
        'archive' => ['zip','tar','gz','bz2','xz','7z','rar','zst','tgz','cab','lz','lzma'],
    ];
    foreach ($cats as $cat => $exts) {
        if (in_array($ext, $exts)) return $cat;
    }
    return 'other';
}

// --- Helpers ---
function resolve_requested_path($requested) {
    global $BASE_DIR;
    $requested = $requested ?? '';
    $requested = str_replace(['\\', "\0"], ['/', ''], $requested);
    $requested = preg_replace('#^/+|/+$#', '', $requested);
    // Block path traversal via ".." segments in the logical path
    $parts = explode('/', $requested);
    $safe = [];
    foreach ($parts as $p) {
        if ($p === '' || $p === '.') continue;
        if ($p === '..') { if (count($safe)) array_pop($safe); else return [false, false]; }
        else $safe[] = $p;
    }
    $logicalRel = $safe ? implode('/', $safe) : '';
    $logical = $BASE_DIR . ($safe ? '/' . $logicalRel : '');
    $resolved = realpath($logical);
    if ($resolved !== false && file_exists($resolved)) return [$logicalRel, $resolved];
    return [false, false];
}

function format_size($bytes) {
    if ($bytes < 0) return '?';
    if ($bytes < 1024) return $bytes . ' B';
    $units = ['KB','MB','GB','TB','PB'];
    $i = -1;
    do { $bytes /= 1024; $i++; } while ($bytes >= 1024 && $i < count($units)-1);
    return round($bytes, 2) . ' ' . $units[$i];
}

function safe_basename($path) {
    return htmlspecialchars(basename($path), ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
}

function list_directory($path) {
    $items = [];
    if (!is_dir($path)) return $items;
    $dh = opendir($path);
    if (!$dh) return $items;
    while (($entry = readdir($dh)) !== false) {
        if ($entry === '.' || $entry === '..') continue;
        $full = $path . DIRECTORY_SEPARATOR . $entry;
        $isDir = is_dir($full);
        $size = $isDir ? -1 : (is_file($full) ? filesize($full) : 0);
        $mtime = filemtime($full) ?: 0;
        $items[] = [
            'name' => $entry, 'full' => $full,
            'is_dir' => $isDir, 'size' => $size, 'mtime' => $mtime,
        ];
    }
    closedir($dh);
    return $items;
}

function build_breadcrumbs($relPath) {
    $rel = trim(str_replace('\\','/',$relPath), '/');
    $parts = $rel === '' ? [] : explode('/', $rel);
    $crumbs = [['name' => 'Home', 'path' => '']];
    $acc = '';
    foreach ($parts as $p) {
        $acc = $acc === '' ? $p : ($acc . '/' . $p);
        $crumbs[] = ['name' => $p, 'path' => $acc];
    }
    return $crumbs;
}

function send_file_response($filePath, $downloadName = null) {
    if (!is_file($filePath) || !is_readable($filePath)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        echo "File not found."; exit;
    }
    if ($downloadName === null) $downloadName = basename($filePath);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $filePath) ?: 'application/octet-stream';
    finfo_close($finfo);
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . rawurlencode($downloadName) . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: private');
    while (ob_get_level()) ob_end_flush();
    readfile($filePath);
    exit;
}

// --- Cache helpers (sqlite) ---
function get_cache_db() {
    global $CACHE_DIR;
    $dbPath = $CACHE_DIR . '/cache.sqlite';
    $db = new SQLite3($dbPath);
    $db->busyTimeout(2000);
    $db->exec('CREATE TABLE IF NOT EXISTS cache (key TEXT PRIMARY KEY, value TEXT, mtime INTEGER)');
    return $db;
}

function cache_get($key, $currentMtime) {
    $db = get_cache_db();
    $stmt = $db->prepare('SELECT value, mtime FROM cache WHERE key = :k');
    $stmt->bindValue(':k', $key, SQLITE3_TEXT);
    $r = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    $db->close();
    if ($r && (int)$r['mtime'] >= $currentMtime) return json_decode($r['value'], true);
    return null;
}

function cache_set($key, $value, $mtime) {
    $db = get_cache_db();
    $stmt = $db->prepare('INSERT OR REPLACE INTO cache (key, value, mtime) VALUES (:k, :v, :m)');
    $stmt->bindValue(':k', $key, SQLITE3_TEXT);
    $stmt->bindValue(':v', json_encode($value), SQLITE3_TEXT);
    $stmt->bindValue(':m', $mtime, SQLITE3_INTEGER);
    $stmt->execute();
    $db->close();
}

// --- Folder size computation ---
function compute_dir_size($path) {
    $size = 0;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($it as $f) {
        if ($f->isFile()) $size += $f->getSize();
    }
    return $size;
}

// --- Mediainfo ---
function get_mediainfo($filePath) {
    $mtime = filemtime($filePath);
    $cacheKey = 'mi:' . $filePath;
    $cached = cache_get($cacheKey, $mtime);
    if ($cached !== null) return $cached;

    $cmd = 'mediainfo --Output=JSON ' . escapeshellarg($filePath) . ' 2>/dev/null';
    $json = shell_exec($cmd);
    $data = json_decode($json, true);
    if (!$data) return null;

    $result = ['streams' => []];
    $tracks = $data['media']['track'] ?? [];
    foreach ($tracks as $t) {
        $type = $t['@type'] ?? '';
        if ($type === 'General') {
            $dur = $t['Duration'] ?? null;
            if ($dur !== null) $result['duration'] = (float)$dur;
            $result['format'] = $t['Format'] ?? '';
            $result['overall_bitrate'] = $t['OverallBitRate'] ?? null;
        } else if ($type === 'Video') {
            $s = ['type'=>'video','format'=>$t['Format']??'','width'=>(int)($t['Width']??0),'height'=>(int)($t['Height']??0)];
            $s['bitrate'] = $t['BitRate'] ?? $t['BitRate_Nominal'] ?? null;
            $s['framerate'] = $t['FrameRate'] ?? null;
            $s['color_space'] = ($t['ColorSpace'] ?? '') . (isset($t['ChromaSubsampling']) ? ' '.$t['ChromaSubsampling'] : '');
            $s['bit_depth'] = $t['BitDepth'] ?? null;
            $s['language'] = $t['Language'] ?? null;
            $result['streams'][] = $s;
        } else if ($type === 'Audio') {
            $s = ['type'=>'audio','format'=>$t['Format']??'','channels'=>$t['Channels']??'','sample_rate'=>$t['SamplingRate']??''];
            $s['bitrate'] = $t['BitRate'] ?? $t['BitRate_Nominal'] ?? null;
            $s['language'] = $t['Language'] ?? null;
            $s['bit_depth'] = $t['BitDepth'] ?? null;
            $result['streams'][] = $s;
        } else if ($type === 'Text') {
            $s = ['type'=>'subtitle','format'=>$t['Format']??'','language'=>$t['Language']??null];
            $result['streams'][] = $s;
        }
    }
    cache_set($cacheKey, $result, $mtime);
    return $result;
}

function get_image_info($filePath) {
    $mtime = filemtime($filePath);
    $cacheKey = 'img:' . $filePath;
    $cached = cache_get($cacheKey, $mtime);
    if ($cached !== null) return $cached;

    $result = [];
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // SVG: parse dimensions from XML — vector format, no megapixels
    if ($ext === 'svg') {
        $result['format'] = 'SVG';
        $result['vector'] = true;
        $xml = @simplexml_load_file($filePath);
        if ($xml) {
            $w = (float)($xml['width'] ?? 0);
            $h = (float)($xml['height'] ?? 0);
            if (!$w && !$h && isset($xml['viewBox'])) {
                $vb = preg_split('/[\s,]+/', (string)$xml['viewBox']);
                if (count($vb) >= 4) { $w = (float)$vb[2]; $h = (float)$vb[3]; }
            }
            if ($w > 0 && $h > 0) {
                $result['width'] = (int)$w;
                $result['height'] = (int)$h;
            }
        }
        $result['has_alpha'] = true;
        $result['animated'] = false;
        cache_set($cacheKey, $result, $mtime);
        return $result;
    }

    $info = @getimagesize($filePath);
    if ($info) {
        $result['width'] = $info[0];
        $result['height'] = $info[1];
        $result['megapixels'] = round(($info[0] * $info[1]) / 1e6, 2);
        $typeConst = $info[2];
        $typeNames = [1=>'GIF',2=>'JPEG',3=>'PNG',4=>'SWF',5=>'PSD',6=>'BMP',9=>'JPC',13=>'SWC',14=>'IFF',15=>'WBMP',16=>'XBM',17=>'ICO',18=>'WEBP',19=>'AVIF'];
        $result['format'] = $typeNames[$typeConst] ?? image_type_to_mime_type($typeConst);
        $channels = $info['channels'] ?? null;
        $result['channels'] = $channels;
        $result['color_mode'] = $channels === 3 ? 'RGB' : ($channels === 4 ? 'RGBA/CMYK' : ($channels === 1 ? 'Grayscale' : null));
        $result['bits'] = $info['bits'] ?? null;
        $result['has_alpha'] = in_array($typeConst, [1,3,18,19]);
        $result['animated'] = false;
        if ($typeConst === 1) { $result['animated'] = _gif_is_animated($filePath); }
        else if ($typeConst === 3) { $result['animated'] = _png_is_animated($filePath); }
        else if ($typeConst === 18) { $result['animated'] = _webp_is_animated($filePath); }
    } else {
        // GD can't read it — try ImageMagick identify
        $cmd = 'identify -format "%w %h %m" ' . escapeshellarg($filePath . '[0]') . ' 2>/dev/null';
        $out = trim(shell_exec($cmd) ?? '');
        if (preg_match('/^(\d+)\s+(\d+)\s+(\S+)/', $out, $m)) {
            $result['width'] = (int)$m[1];
            $result['height'] = (int)$m[2];
            $result['megapixels'] = round(((int)$m[1] * (int)$m[2]) / 1e6, 2);
            $result['format'] = $m[3]; // e.g. TIFF, ICO, HEIC
        }
        $result['has_alpha'] = in_array($ext, ['ico','tiff','tif','heic','heif','psd']);
        $result['animated'] = false;
    }
    cache_set($cacheKey, $result, $mtime);
    return $result;
}

function _gif_is_animated($path) {
    $fh = fopen($path, 'rb');
    if (!$fh) return false;
    $count = 0;
    while (!feof($fh) && $count < 2) {
        $chunk = fread($fh, 1024*100);
        $count += substr_count($chunk, "\x00\x21\xF9\x04");
    }
    fclose($fh);
    return $count > 1;
}

function _png_is_animated($path) {
    $fh = fopen($path, 'rb');
    if (!$fh) return false;
    $data = fread($fh, min(filesize($path), 1024*512));
    fclose($fh);
    return strpos($data, 'acTL') !== false && strpos($data, 'fcTL') !== false;
}

function _webp_is_animated($path) {
    $fh = fopen($path, 'rb');
    if (!$fh) return false;
    $data = fread($fh, 64);
    fclose($fh);
    return strpos($data, 'ANIM') !== false;
}

function format_duration($seconds) {
    if (!$seconds || $seconds <= 0) return '';
    $s = (int)$seconds;
    $h = intdiv($s, 3600);
    $m = intdiv($s % 3600, 60);
    $sec = $s % 60;
    if ($h > 0) return sprintf('%d:%02d:%02d h', $h, $m, $sec);
    return sprintf('%d:%02d min', $m, $sec);
}

function format_bitrate($bps) {
    if (!$bps) return '';
    $kbps = (int)$bps / 1000;
    if ($kbps >= 1000) return round($kbps/1000, 1) . ' Mbps';
    return round($kbps) . ' kbps';
}

// --- Streaming ZIP ---
class StreamingZip {
    private $out;
    private $entries = [];
    private $centralDir = '';
    private $offset = 0;

    public function open() { $this->out = fopen('php://output', 'wb'); }

    public function addFileFromPath($localName, $filePath) {
        $size = filesize($filePath);
        $crc = hexdec(hash_file('crc32b', $filePath));
        $localName = str_replace('\\', '/', $localName);
        $nameLen = strlen($localName);
        $mtime = filemtime($filePath) ?: time();
        $dosTime = $this->toDosTime($mtime);
        $dosDate = $this->toDosDate($mtime);
        $useZip64 = ($size >= 0xFFFFFFFF);

        $h = "PK\x03\x04";
        $h .= pack('v', $useZip64 ? 45 : 20);
        $h .= pack('v', 0) . pack('v', 0);
        $h .= pack('v', $dosTime) . pack('v', $dosDate);
        $h .= pack('V', $crc);
        $h .= $useZip64 ? pack('V', 0xFFFFFFFF).pack('V', 0xFFFFFFFF) : pack('V', $size).pack('V', $size);
        $h .= pack('v', $nameLen);
        $extra = $useZip64 ? pack('v',0x0001).pack('v',16).$this->pack64($size).$this->pack64($size) : '';
        $h .= pack('v', strlen($extra)) . $localName . $extra;

        $headerOffset = $this->offset;
        fwrite($this->out, $h);
        $this->offset += strlen($h);

        $fh = fopen($filePath, 'rb');
        while (!feof($fh)) {
            $chunk = fread($fh, 1048576);
            fwrite($this->out, $chunk);
            $this->offset += strlen($chunk);
            flush();
        }
        fclose($fh);

        $c = "PK\x01\x02";
        $c .= pack('v', $useZip64?45:20) . pack('v', $useZip64?45:20);
        $c .= pack('v',0).pack('v',0);
        $c .= pack('v',$dosTime).pack('v',$dosDate);
        $c .= pack('V',$crc);
        $c .= $useZip64 ? pack('V',0xFFFFFFFF).pack('V',0xFFFFFFFF) : pack('V',$size).pack('V',$size);
        $c .= pack('v',$nameLen);
        $cdExtra = ($useZip64||$headerOffset>=0xFFFFFFFF) ? pack('v',0x0001).pack('v',24).$this->pack64($size).$this->pack64($size).$this->pack64($headerOffset) : '';
        $c .= pack('v',strlen($cdExtra)).pack('v',0).pack('v',0).pack('v',0).pack('V',0);
        $c .= $headerOffset>=0xFFFFFFFF ? pack('V',0xFFFFFFFF) : pack('V',$headerOffset);
        $c .= $localName . $cdExtra;

        $this->centralDir .= $c;
        $this->entries[] = true;
    }

    public function close() {
        $cdOff = $this->offset;
        fwrite($this->out, $this->centralDir);
        $cdSz = strlen($this->centralDir);
        $this->offset += $cdSz;
        $n = count($this->entries);
        if ($cdOff>=0xFFFFFFFF||$cdSz>=0xFFFFFFFF||$n>=0xFFFF) {
            $z="PK\x06\x06".$this->pack64(44).pack('v',45).pack('v',45).pack('V',0).pack('V',0).$this->pack64($n).$this->pack64($n).$this->pack64($cdSz).$this->pack64($cdOff);
            fwrite($this->out,$z); $this->offset+=strlen($z);
            $l="PK\x06\x07".pack('V',0).$this->pack64($cdOff+$cdSz).pack('V',1);
            fwrite($this->out,$l); $this->offset+=strlen($l);
        }
        $e="PK\x05\x06".pack('v',0).pack('v',0).pack('v',min($n,0xFFFF)).pack('v',min($n,0xFFFF)).pack('V',min($cdSz,0xFFFFFFFF)).pack('V',min($cdOff,0xFFFFFFFF)).pack('v',0);
        fwrite($this->out,$e);
        fflush($this->out); fclose($this->out);
    }

    private function toDosTime($ts){$h=(int)date('H',$ts);$m=(int)date('i',$ts);$s=(int)date('s',$ts);return($h<<11)|($m<<5)|($s>>1);}
    private function toDosDate($ts){$y=(int)date('Y',$ts)-1980;$mo=(int)date('n',$ts);$d=(int)date('j',$ts);return($y<<9)|($mo<<5)|$d;}
    private function pack64($v){return pack('V',$v&0xFFFFFFFF).pack('V',($v>>32)&0xFFFFFFFF);}
}

// ========== TASK QUEUE ==========
// SQLite-based task stack: LIFO priority, dedup, cross-session coordination
define('TASK_MAX_WORKERS', 3);       // max concurrent tasks across all sessions
define('TASK_STALE_SECONDS', 60);    // reclaim stuck tasks after this
define('TASK_BATCH_SIZE', 5);        // tasks processed per api_work call

function get_task_db() {
    global $CACHE_DIR;
    $dbPath = $CACHE_DIR . '/tasks.sqlite';
    $db = new SQLite3($dbPath);
    $db->busyTimeout(3000);
    $db->exec('PRAGMA journal_mode=WAL');
    $db->exec('PRAGMA synchronous=NORMAL');
    $db->exec('CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        type TEXT NOT NULL,
        path TEXT NOT NULL,
        priority REAL NOT NULL,
        status TEXT NOT NULL DEFAULT "pending",
        claimed_at REAL,
        result TEXT,
        created_at REAL NOT NULL,
        UNIQUE(type, path)
    )');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_tasks_status ON tasks(status, priority DESC)');
    return $db;
}

function task_enqueue($db, $tasks) {
    // $tasks = [[type, path, priority], ...]
    // Insert or bump priority if already pending. Ignore if done/running.
    $now = microtime(true);
    $stmt = $db->prepare('INSERT INTO tasks (type, path, priority, status, created_at)
        VALUES (:t, :p, :pri, "pending", :now)
        ON CONFLICT(type, path) DO UPDATE SET
            priority = MAX(priority, :pri)
        WHERE status = "pending"');
    foreach ($tasks as $t) {
        $stmt->bindValue(':t', $t[0], SQLITE3_TEXT);
        $stmt->bindValue(':p', $t[1], SQLITE3_TEXT);
        $stmt->bindValue(':pri', $t[2] ?? $now, SQLITE3_FLOAT);
        $stmt->bindValue(':now', $now, SQLITE3_FLOAT);
        $stmt->execute();
        $stmt->reset();
    }
}

function task_reclaim_stale($db) {
    $cutoff = microtime(true) - TASK_STALE_SECONDS;
    $db->exec("UPDATE tasks SET status='pending', claimed_at=NULL WHERE status='running' AND claimed_at < $cutoff");
}

function task_claim($db, $limit) {
    // Count current running tasks — respect concurrency limit
    $now = microtime(true);
    $running = $db->querySingle("SELECT COUNT(*) FROM tasks WHERE status='running'");
    $canClaim = max(0, TASK_MAX_WORKERS - (int)$running);
    if ($canClaim <= 0) return [];
    $limit = min($limit, $canClaim);

    // Claim highest-priority pending tasks (LIFO — highest priority number first)
    $claimed = [];
    $stmt = $db->prepare("SELECT id, type, path FROM tasks WHERE status='pending' ORDER BY priority DESC LIMIT :lim");
    $stmt->bindValue(':lim', $limit, SQLITE3_INTEGER);
    $res = $stmt->execute();
    $ids = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $ids[] = $row['id'];
        $claimed[] = $row;
    }
    if ($ids) {
        $idList = implode(',', $ids);
        $db->exec("UPDATE tasks SET status='running', claimed_at=$now WHERE id IN ($idList)");
    }
    return $claimed;
}

function task_complete($db, $id, $result) {
    $stmt = $db->prepare("UPDATE tasks SET status='done', result=:r WHERE id=:id");
    $stmt->bindValue(':r', json_encode($result), SQLITE3_TEXT);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
}

function task_fail($db, $id) {
    $db->exec("UPDATE tasks SET status='error' WHERE id=$id");
}

function task_get_status($db) {
    $pending = (int)$db->querySingle("SELECT COUNT(*) FROM tasks WHERE status='pending'");
    $running = (int)$db->querySingle("SELECT COUNT(*) FROM tasks WHERE status='running'");
    return ['pending' => $pending, 'running' => $running, 'total' => $pending + $running];
}

function task_get_results($db, $types, $paths) {
    // Get completed results for given type+path pairs
    if (empty($paths)) return [];
    $results = [];
    $stmt = $db->prepare("SELECT type, path, result, status FROM tasks WHERE type=:t AND path=:p");
    for ($i = 0; $i < count($paths); $i++) {
        $stmt->bindValue(':t', $types[$i], SQLITE3_TEXT);
        $stmt->bindValue(':p', $paths[$i], SQLITE3_TEXT);
        $res = $stmt->execute();
        $row = $res->fetchArray(SQLITE3_ASSOC);
        if ($row) {
            $results[] = [
                'type' => $row['type'],
                'path' => $row['path'],
                'status' => $row['status'],
                'result' => $row['status'] === 'done' ? json_decode($row['result'], true) : null,
            ];
        }
        $stmt->reset();
    }
    return $results;
}

function task_cleanup_done($db, $maxAge = 300) {
    $cutoff = microtime(true) - $maxAge;
    $db->exec("DELETE FROM tasks WHERE status IN ('done','error') AND created_at < $cutoff");
}

// --- Execute a single task and return its result ---
function task_execute($type, $path) {
    list(, $abs) = resolve_requested_path($path);
    if (!$abs) return null;

    switch ($type) {
        case 'dirsize':
            if (!is_dir($abs)) return null;
            $mtime = filemtime($abs);
            $cacheKey = 'ds:' . $abs;
            $cached = cache_get($cacheKey, $mtime);
            if ($cached !== null) return $cached;
            $size = compute_dir_size($abs);
            // Count direct children (files + dirs)
            $children = @scandir($abs);
            $files = 0; $dirs = 0;
            if ($children) {
                foreach ($children as $c) {
                    if ($c === '.' || $c === '..') continue;
                    if (is_dir($abs . '/' . $c)) $dirs++; else $files++;
                }
            }
            $result = ['size' => $size, 'formatted' => format_size($size), 'files' => $files, 'dirs' => $dirs];
            cache_set($cacheKey, $result, $mtime);
            return $result;

        case 'info':
            if (!is_file($abs)) return null;
            $cat = file_category(basename($abs));
            $result = ['category' => $cat];
            // Sort hierarchy (descending): folders(50000+) > video/audio(1000+) > document(500+) > image(1+) > other(0)
            if ($cat === 'image') {
                $info = get_image_info($abs);
                $result['info'] = $info;
                if (!empty($info['vector'])) {
                    if (isset($info['width'], $info['height'])) {
                        $result['summary'] = $info['width'] . '×' . $info['height'];
                    } else {
                        $result['summary'] = 'Vector';
                    }
                    $result['sort_value'] = 1;
                } else {
                    if (isset($info['megapixels'])) $result['summary'] = $info['megapixels'] . ' MP';
                    $result['sort_value'] = 1 + ($info['megapixels'] ?? 0);
                }
            } else if ($cat === 'audio' || $cat === 'video') {
                $info = get_mediainfo($abs);
                $result['info'] = $info;
                if (isset($info['duration'])) {
                    $result['summary'] = format_duration($info['duration']);
                    $result['sort_value'] = 1000 + (float)$info['duration'];
                } else {
                    $result['sort_value'] = 1000;
                }
            } else if ($cat === 'document') {
                $docExt = strtolower(pathinfo(basename($abs), PATHINFO_EXTENSION));
                if ($docExt === 'pdf') {
                    $info = get_pdf_info($abs);
                } else {
                    $info = get_document_info($abs);
                }
                $result['info'] = $info;
                if (isset($info['pages'])) {
                    $result['summary'] = $info['pages'] . ' pg';
                    $result['sort_value'] = 500 + (float)$info['pages'];
                } else if (isset($info['format'])) {
                    $result['summary'] = $info['format'];
                    $result['sort_value'] = 500;
                }
            }
            return $result;

        case 'thumb':
            if (!is_file($abs)) return null;
            $cat = file_category(basename($abs));
            if (!in_array($cat, ['image','video','document'])) return null;
            $docExt = strtolower(pathinfo(basename($abs), PATHINFO_EXTENSION));
            global $CACHE_DIR;
            $mtime = filemtime($abs);
            $thumbKey = md5($abs) . '_' . $mtime;
            $thumbPath = $CACHE_DIR . '/thumb_' . $thumbKey . '.jpg';
            if (!is_file($thumbPath)) {
                if ($cat === 'image') $thumbPath = generate_image_thumbnail($abs, $thumbPath);
                else if ($cat === 'video') $thumbPath = generate_video_thumbnail($abs, $thumbPath);
                else if ($cat === 'document' && $docExt === 'pdf') $thumbPath = generate_pdf_thumbnail($abs, $thumbPath);
                else $thumbPath = null; // Non-PDF documents: no thumbnail without LibreOffice
            }
            // Result is just a flag — client fetches the actual image via api_thumb
            return ($thumbPath && is_file($thumbPath)) ? ['ready' => true] : null;

        default:
            return null;
    }
}

// ========== API ENDPOINTS ==========
$action = $_GET['action'] ?? '';
$requested = $_GET['path'] ?? '';

// --- API: enqueue tasks (POST) ---
if ($action === 'api_enqueue') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['tasks']) || !is_array($input['tasks'])) {
        echo json_encode(['error' => 'invalid']); exit;
    }
    $db = get_task_db();
    task_cleanup_done($db);
    $toEnqueue = [];
    $basePriority = microtime(true);
    foreach ($input['tasks'] as $i => $t) {
        if (!isset($t['type'], $t['path'])) continue;
        // Lower index = lower priority (LIFO: last enqueued items get processed first)
        $toEnqueue[] = [$t['type'], $t['path'], $basePriority + $i * 0.0001];
    }
    task_enqueue($db, $toEnqueue);
    $status = task_get_status($db);
    $db->close();
    echo json_encode(['ok' => true, 'status' => $status]);
    exit;
}

// --- API: worker — claim and process tasks, return results ---
if ($action === 'api_work') {
    header('Content-Type: application/json');
    $db = get_task_db();
    task_reclaim_stale($db);
    $claimed = task_claim($db, TASK_BATCH_SIZE);
    $completed = [];
    foreach ($claimed as $task) {
        $result = task_execute($task['type'], $task['path']);
        if ($result !== null) {
            task_complete($db, $task['id'], $result);
            $completed[] = ['type' => $task['type'], 'path' => $task['path'], 'result' => $result];
        } else {
            task_fail($db, $task['id']);
            $completed[] = ['type' => $task['type'], 'path' => $task['path'], 'result' => null, 'error' => true];
        }
    }
    $status = task_get_status($db);
    $db->close();
    echo json_encode(['completed' => $completed, 'status' => $status]);
    exit;
}

// --- API: poll status + collect finished results ---
if ($action === 'api_poll') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $db = get_task_db();
    $status = task_get_status($db);
    $results = [];
    // Client sends list of tasks it's waiting for
    if ($input && isset($input['waiting']) && is_array($input['waiting'])) {
        $types = []; $paths = [];
        foreach ($input['waiting'] as $w) {
            $types[] = $w['type'] ?? '';
            $paths[] = $w['path'] ?? '';
        }
        $results = task_get_results($db, $types, $paths);
    }
    $db->close();
    echo json_encode(['status' => $status, 'results' => $results]);
    exit;
}

// --- API: queue detail (list of current tasks for hover popup) ---
if ($action === 'api_queue_detail') {
    header('Content-Type: application/json');
    $db = get_task_db();
    $status = task_get_status($db);
    $tasks = [];
    $res = $db->query("SELECT type, path, status FROM tasks WHERE status IN ('running','pending') ORDER BY CASE status WHEN 'running' THEN 0 ELSE 1 END, priority DESC LIMIT 20");
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $tasks[] = ['type' => $row['type'], 'path' => basename($row['path']), 'status' => $row['status']];
    }
    $db->close();
    echo json_encode(['status' => $status, 'tasks' => $tasks]);
    exit;
}

// --- API: directory change check (lightweight hash) ---
if ($action === 'api_dircheck') {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');
    list($relPath, $absPath) = resolve_requested_path($requested);
    if ($absPath === false || !is_dir($absPath)) {
        echo json_encode(['hash' => '']); exit;
    }
    $entries = [];
    $dh = @opendir($absPath);
    if ($dh) {
        while (($entry = readdir($dh)) !== false) {
            if ($entry === '.' || $entry === '..') continue;
            $fp = $absPath . '/' . $entry;
            $entries[] = $entry . ':' . @filesize($fp) . ':' . @filemtime($fp);
        }
        closedir($dh);
    }
    sort($entries);
    echo json_encode(['hash' => md5(implode("\n", $entries))]);
    exit;
}

// --- API: directory listing as JSON (for AJAX refresh) ---
if ($action === 'api_dirlist') {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');
    list($relPath, $absPath) = resolve_requested_path($requested);
    if ($absPath === false || !is_dir($absPath)) {
        echo json_encode(['error' => 'invalid']); exit;
    }
    $items = list_directory($absPath);
    usort($items, function($a,$b) {
        if ($a['is_dir'] xor $b['is_dir']) return $a['is_dir'] ? -1 : 1;
        return strcasecmp($a['name'], $b['name']);
    });
    $out = [];
    foreach ($items as $it) {
        $cat = $it['is_dir'] ? 'folder' : file_category($it['name']);
        $icon = $it['is_dir'] ? '📁' : file_icon($it['name']);
        $rp = $relPath === '' ? $it['name'] : ($relPath . '/' . $it['name']);
        $hasMedia = in_array($cat, ['image','audio','video','document']);
        $out[] = [
            'name' => $it['name'], 'is_dir' => $it['is_dir'],
            'size' => $it['size'], 'mtime' => $it['mtime'],
            'cat' => $cat, 'icon' => $icon, 'path' => $rp,
            'has_media' => $hasMedia,
        ];
    }
    // Also compute hash for future comparisons
    $entries = [];
    foreach ($items as $it) {
        $entries[] = $it['name'] . ':' . $it['size'] . ':' . $it['mtime'];
    }
    sort($entries);
    echo json_encode(['items' => $out, 'hash' => md5(implode("\n", $entries))]);
    exit;
}

// --- API: thumbnail (direct serve — kept for on-demand hover) ---
if ($action === 'api_thumb') {
    list(, $abs) = resolve_requested_path($requested);
    if (!$abs || !is_file($abs)) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); exit; }
    $cat = file_category(basename($abs));
    if (!in_array($cat, ['image','video','document'])) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); exit; }

    global $CACHE_DIR;
    $mtime = filemtime($abs);
    $thumbKey = md5($abs) . '_' . $mtime;
    $thumbPath = $CACHE_DIR . '/thumb_' . $thumbKey . '.jpg';

    if (!is_file($thumbPath)) {
        $docExt = strtolower(pathinfo(basename($abs), PATHINFO_EXTENSION));
        if ($cat === 'image') $thumbPath = generate_image_thumbnail($abs, $thumbPath);
        else if ($cat === 'video') $thumbPath = generate_video_thumbnail($abs, $thumbPath);
        else if ($cat === 'document' && $docExt === 'pdf') $thumbPath = generate_pdf_thumbnail($abs, $thumbPath);
        else $thumbPath = null;
    }

    if ($thumbPath && is_file($thumbPath)) {
        header('Content-Type: image/jpeg');
        header('Content-Length: ' . filesize($thumbPath));
        header('Cache-Control: public, max-age=86400');
        readfile($thumbPath);
    } else {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    }
    exit;
}

// --- API: detailed media info (direct serve — kept for on-demand hover) ---
if ($action === 'api_detail') {
    header('Content-Type: application/json');
    list(, $abs) = resolve_requested_path($requested);
    if (!$abs || !is_file($abs)) { echo json_encode(['error'=>'invalid']); exit; }
    $cat = file_category(basename($abs));
    $result = ['category' => $cat, 'name' => basename($abs)];
    if ($cat === 'image') {
        $result['detail'] = get_image_info($abs);
    } else if ($cat === 'audio' || $cat === 'video') {
        $result['detail'] = get_mediainfo($abs);
    } else if ($cat === 'document') {
        $docExt = strtolower(pathinfo(basename($abs), PATHINFO_EXTENSION));
        $result['detail'] = ($docExt === 'pdf') ? get_pdf_info($abs) : get_document_info($abs);
    }
    echo json_encode($result);
    exit;
}

function generate_image_thumbnail($srcPath, $dstPath) {
    $maxDim = 240;
    $ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));

    // Formats that need ImageMagick (convert): svg, ico, tiff, heic, heif, psd
    $imFormats = ['svg','ico','tiff','tif','heic','heif','psd'];
    if (in_array($ext, $imFormats)) {
        return _thumbnail_via_convert($srcPath, $dstPath, $maxDim);
    }

    // Try GD first
    $info = @getimagesize($srcPath);
    if (!$info) {
        // GD can't read it — try ImageMagick as last resort
        return _thumbnail_via_convert($srcPath, $dstPath, $maxDim);
    }
    $w = $info[0]; $h = $info[1]; $type = $info[2];
    $loaders = [
        IMAGETYPE_JPEG => 'imagecreatefromjpeg',
        IMAGETYPE_PNG => 'imagecreatefrompng',
        IMAGETYPE_GIF => 'imagecreatefromgif',
        IMAGETYPE_WEBP => 'imagecreatefromwebp',
        IMAGETYPE_BMP => 'imagecreatefrombmp',
    ];
    // Add AVIF if GD supports it (PHP 8.1+)
    if (defined('IMAGETYPE_AVIF') && function_exists('imagecreatefromavif')) {
        $loaders[IMAGETYPE_AVIF] = 'imagecreatefromavif';
    }
    if (!isset($loaders[$type]) || !function_exists($loaders[$type])) {
        return _thumbnail_via_convert($srcPath, $dstPath, $maxDim);
    }
    $src = @$loaders[$type]($srcPath);
    if (!$src) return _thumbnail_via_convert($srcPath, $dstPath, $maxDim);
    if ($w > $h) { $nw = min($w, $maxDim); $nh = (int)($h * $nw / $w); }
    else { $nh = min($h, $maxDim); $nw = (int)($w * $nh / $h); }
    if ($nw < 1) $nw = 1; if ($nh < 1) $nh = 1;
    $dst = imagecreatetruecolor($nw, $nh);
    // Preserve transparency for PNG/GIF/WEBP → fill white bg for JPEG output
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
    imagejpeg($dst, $dstPath, 80);
    imagedestroy($src); imagedestroy($dst);
    return $dstPath;
}

function _thumbnail_via_convert($srcPath, $dstPath, $maxDim = 240) {
    // Use ImageMagick convert as fallback
    // For SVG: specify density for quality rendering
    $ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));
    $density = ($ext === 'svg') ? '-density 150 ' : '';
    $cmd = 'convert ' . $density . escapeshellarg($srcPath . '[0]')
         . ' -thumbnail ' . $maxDim . 'x' . $maxDim
         . ' -background white -flatten -quality 80 '
         . escapeshellarg('jpeg:' . $dstPath) . ' 2>/dev/null';
    exec($cmd, $out, $ret);
    return ($ret === 0 && is_file($dstPath)) ? $dstPath : null;
}

function generate_video_thumbnail($srcPath, $dstPath) {
    // Determine seek position: 10% of duration (fallback to 1s)
    $seekSec = 1;
    $probe = shell_exec('ffprobe -v error -show_entries format=duration -of csv=p=0 '
        . escapeshellarg($srcPath) . ' 2>/dev/null');
    if ($probe !== null && is_numeric(trim($probe))) {
        $duration = (float)trim($probe);
        $seekSec = max(0, $duration * 0.10);
    }
    $cmd = 'ffmpeg -y -ss ' . $seekSec . ' -i ' . escapeshellarg($srcPath)
         . ' -vframes 1 -vf "scale=240:-1" -q:v 4 ' . escapeshellarg($dstPath)
         . ' 2>/dev/null';
    exec($cmd, $out, $ret);
    if ($ret !== 0 || !is_file($dstPath)) {
        // Fallback: try at 0 seconds
        $cmd = 'ffmpeg -y -ss 0 -i ' . escapeshellarg($srcPath)
             . ' -vframes 1 -vf "scale=240:-1" -q:v 4 ' . escapeshellarg($dstPath)
             . ' 2>/dev/null';
        exec($cmd, $out, $ret);
    }
    return (is_file($dstPath)) ? $dstPath : null;
}

function generate_pdf_thumbnail($srcPath, $dstPath) {
    // Use pdftoppm to render first page as JPEG thumbnail
    $tmpPrefix = $dstPath . '_tmp';
    $cmd = 'pdftoppm -jpeg -f 1 -l 1 -scale-to 240 -singlefile '
         . escapeshellarg($srcPath) . ' ' . escapeshellarg($tmpPrefix) . ' 2>/dev/null';
    exec($cmd, $out, $ret);
    $tmpFile = $tmpPrefix . '.jpg';
    if ($ret === 0 && is_file($tmpFile)) {
        rename($tmpFile, $dstPath);
        return $dstPath;
    }
    @unlink($tmpFile);
    return null;
}

function get_pdf_info($filePath) {
    $mtime = filemtime($filePath);
    $cacheKey = 'pdf:' . $filePath;
    $cached = cache_get($cacheKey, $mtime);
    if ($cached !== null) return $cached;

    $result = [];
    $cmd = 'pdfinfo ' . escapeshellarg($filePath) . ' 2>/dev/null';
    $output = shell_exec($cmd);
    if ($output) {
        foreach (explode("\n", $output) as $line) {
            if (preg_match('/^([^:]+):\s*(.+)$/', $line, $m)) {
                $key = trim($m[1]);
                $val = trim($m[2]);
                switch ($key) {
                    case 'Pages': $result['pages'] = (int)$val; break;
                    case 'Page size': $result['page_size'] = $val; break;
                    case 'Title': $result['title'] = $val; break;
                    case 'Author': $result['author'] = $val; break;
                    case 'Creator': $result['creator'] = $val; break;
                    case 'Producer': $result['producer'] = $val; break;
                    case 'PDF version': $result['pdf_version'] = $val; break;
                    case 'Encrypted': $result['encrypted'] = $val; break;
                    case 'CreationDate': $result['creation_date'] = $val; break;
                }
            }
        }
    }
    cache_set($cacheKey, $result, $mtime);
    return $result;
}

function get_document_info($filePath) {
    $mtime = filemtime($filePath);
    $cacheKey = 'doc:' . $filePath;
    $cached = cache_get($cacheKey, $mtime);
    if ($cached !== null) return $cached;

    $result = [];
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $formatNames = [
        'doc'=>'Word (legacy)','docx'=>'Word','odt'=>'OpenDocument Text','rtf'=>'Rich Text',
        'ppt'=>'PowerPoint (legacy)','pptx'=>'PowerPoint','odp'=>'OpenDocument Presentation',
        'xls'=>'Excel (legacy)','xlsx'=>'Excel','ods'=>'OpenDocument Spreadsheet',
        'epub'=>'EPUB',
    ];
    $result['format'] = $formatNames[$ext] ?? strtoupper($ext);

    // Try to get MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $result['mime'] = finfo_file($finfo, $filePath) ?: 'unknown';
    finfo_close($finfo);

    // For EPUB: try to read metadata from OPF
    if ($ext === 'epub') {
        $zip = new ZipArchive();
        if ($zip->open($filePath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('/\.opf$/i', $name)) {
                    $opf = $zip->getFromIndex($i);
                    if ($opf && preg_match('/<dc:title[^>]*>([^<]+)/i', $opf, $m)) $result['title'] = html_entity_decode(trim($m[1]));
                    if ($opf && preg_match('/<dc:creator[^>]*>([^<]+)/i', $opf, $m)) $result['author'] = html_entity_decode(trim($m[1]));
                    if ($opf && preg_match('/<dc:language[^>]*>([^<]+)/i', $opf, $m)) $result['language'] = trim($m[1]);
                    break;
                }
            }
            $zip->close();
        }
    }

    cache_set($cacheKey, $result, $mtime);
    return $result;
}

// --- Download ---
list($requestedRel, $absRequested) = resolve_requested_path($requested);
if ($absRequested === false) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Invalid path."; exit;
}

if ($action === 'download') {
    if (!file_exists($absRequested)) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); echo "Not found."; exit; }
    if (is_file($absRequested)) send_file_response($absRequested);
    if (is_dir($absRequested)) {
        $fileCount = 0;
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($absRequested, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($it as $f) { if ($f->isFile()) $fileCount++; }
        if ($fileCount > $MAX_FILES_IN_ZIP) { header($_SERVER["SERVER_PROTOCOL"]." 413 Payload Too Large"); echo "Too many files."; exit; }
        if ($fileCount === 0) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); echo "Empty folder."; exit; }
        $dn = preg_replace('/[^A-Za-z0-9._-]/','_', basename($absRequested)?:'folder').'.zip';
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.rawurlencode($dn).'"');
        header('Cache-Control: private');
        while (ob_get_level()) ob_end_flush();
        $zip = new StreamingZip(); $zip->open();
        $it2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($absRequested, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($it2 as $file) {
            if (!$file->isFile()) continue;
            $zip->addFileFromPath(ltrim(str_replace('\\','/',substr($file->getPathname(),strlen($absRequested))),'/'), $file->getPathname());
        }
        $zip->close(); exit;
    }
}

// --- Directory listing page ---
if (!file_exists($absRequested)) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); echo "Not found."; exit; }
if (!is_dir($absRequested)) {
    header('Location: ?'.http_build_query(['path'=>$requested,'action'=>'download'])); exit;
}

$items = list_directory($absRequested);
usort($items, function($a,$b) {
    if ($a['is_dir'] xor $b['is_dir']) return $a['is_dir'] ? -1 : 1;
    return strcasecmp($a['name'], $b['name']);
});

$crumbs = build_breadcrumbs($requestedRel);
$currentRel = $requestedRel;
$scriptName = htmlspecialchars($_SERVER['SCRIPT_NAME']);
?>
<?php $themeClass = ($_COOKIE['fb_theme'] ?? '') === 'light' ? ' class="light"' : ''; ?>
<!doctype html>
<html lang="en"<?php echo $themeClass; ?>>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>PHP Autoindexed File-Viewer v<?php echo APP_VERSION; ?><?php echo $currentRel ? ' - /'.htmlspecialchars($currentRel) : ''; ?></title>
<!-- lean inline favicon block -->
<link rel="shortcut icon" href="data:image/x-icon;base64,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">
<style>
/* --- Dark theme (default) --- */
:root{
  --bg:#1e2738;--card:#263044;--muted:#8899ab;--accent:#5b9cf6;--text:#dde5f0;
  --hover:#2e3d56;--border:rgba(255,255,255,0.06);--input-bg:#1b2233;--popup-bg:#1f2b3e;
  --btn-bg:transparent;--btn-border:var(--border)
}
/* --- Light theme --- */
html.light{
  --bg:#f0f2f5;--card:#fff;--muted:#666;--accent:#2563eb;--text:#1a1a1a;
  --hover:#e8edf3;--border:rgba(0,0,0,0.08);--input-bg:#fff;--popup-bg:#fff;
  --btn-bg:#fff;--btn-border:rgba(0,0,0,0.12)
}
*{box-sizing:border-box;font-family:Inter,system-ui,Segoe UI,Roboto,"Helvetica Neue",Arial,sans-serif;margin:0}
body{background:var(--bg);color:var(--text);padding:20px 24px}
a{color:var(--accent);text-decoration:none}
a:visited{color:var(--accent)}
.container{max-width:1200px;margin:0 auto}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px}
.title{font-size:18px;font-weight:600}
.toolbar{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
.btn{background:var(--btn-bg);border:1px solid var(--btn-border);padding:4px 10px;border-radius:6px;color:var(--text);cursor:pointer;text-decoration:none;font-size:13px;white-space:nowrap;line-height:1.5}
.btn:hover{background:var(--hover)}
.path-bar{background:var(--card);padding:6px 12px;border-radius:6px;margin-bottom:8px;color:var(--muted);font-size:13px;display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.path-bar a{color:var(--muted)}
.path-bar a:hover{color:var(--text)}
.path-bar .sep{opacity:0.4}
/* Search/filter bar */
.filterbar{display:flex;gap:6px;margin-bottom:8px;flex-wrap:wrap;align-items:center}
.filterbar input,.filterbar select{background:var(--input-bg);border:1px solid var(--border);border-radius:6px;padding:5px 10px;color:var(--text);font-size:13px;outline:none}
.filterbar input:focus,.filterbar select:focus{border-color:var(--accent)}
.filterbar input[type=text]{min-width:200px;flex:1;max-width:400px}
/* Table */
.table{width:100%;border-collapse:collapse;background:var(--card);border-radius:8px;overflow:visible}
.table thead{background:rgba(128,128,128,0.04)}
.table th{padding:7px 12px;color:var(--muted);font-size:12px;text-align:left;cursor:pointer;user-select:none;white-space:nowrap;border-bottom:1px solid var(--border);font-weight:500}
.table th:last-child{cursor:default}
.table th:hover{color:var(--text)}
.table th:last-child:hover{color:var(--muted)}
.table th .arrow{margin-left:3px;font-size:9px;opacity:0.3}
.table th.sorted .arrow{opacity:1;color:var(--accent)}
.table td{padding:4px 12px;border-bottom:1px solid var(--border);font-size:13px}
.table tbody tr:last-child td{border-bottom:none}
.table tbody tr:hover td{background:var(--hover)}
.table tbody tr.hidden{display:none}
.icon{display:inline-block;width:20px;margin-right:6px;text-align:center;font-size:13px;flex-shrink:0}
.namecell{display:flex;align-items:center}
.small{color:var(--muted);font-size:13px}
.actions{display:flex;gap:5px}
.empty{padding:30px;text-align:center;color:var(--muted)}
.info-cell{white-space:nowrap;color:var(--muted);font-size:12px}
.info-cell .media-tag{cursor:default}
/* Popup overlay */
.media-popup{display:none;position:fixed;z-index:10000;min-width:320px;max-width:480px;max-height:420px;overflow-x:hidden;overflow-y:auto;background:var(--popup-bg);border:1px solid var(--border);border-radius:8px;padding:12px;font-size:12px;color:var(--text);box-shadow:0 8px 32px rgba(0,0,0,0.3);line-height:1.6;pointer-events:auto;word-wrap:break-word;overflow-wrap:break-word}
.media-popup.active{display:block}
.media-popup h4{margin:0 0 6px;font-size:13px;color:var(--accent);overflow-wrap:break-word;word-break:break-all}
.media-popup .mp-row{display:flex;gap:8px;flex-wrap:wrap}
.media-popup .mp-label{color:var(--muted);min-width:80px;flex-shrink:0}
.media-popup .mp-val{min-width:0;word-break:break-all}
.media-popup .stream{border-top:1px solid var(--border);margin-top:6px;padding-top:6px;overflow-wrap:break-word;word-break:break-word}
/* Thumbnail popup */
.thumb-popup{display:none;position:fixed;z-index:10001;background:var(--popup-bg);border:1px solid var(--border);border-radius:8px;padding:4px;box-shadow:0 8px 32px rgba(0,0,0,0.3);pointer-events:none}
.thumb-popup.active{display:block}
.thumb-popup img{display:block;max-width:240px;max-height:240px;border-radius:4px}
/* Theme toggle */
.theme-toggle{background:none;border:none;cursor:pointer;font-size:18px;padding:2px 6px;line-height:1;color:var(--text)}
/* Queue status */
.queue-status{font-size:11px;color:var(--muted);padding:3px 8px;border-radius:6px;background:var(--card);border:1px solid var(--border);white-space:nowrap;transition:opacity .3s}
.queue-status:empty{display:none}
.queue-status .qs-spinner{display:inline-block;width:10px;height:10px;border:2px solid var(--muted);border-top-color:var(--accent);border-radius:50%;animation:qs-spin .8s linear infinite;margin-right:4px;vertical-align:middle}
@keyframes qs-spin{to{transform:rotate(360deg)}}
/* Queue detail popup */
.queue-status{position:relative;cursor:default}
.queue-detail-popup{display:none;position:absolute;left:0;top:100%;margin-top:6px;min-width:260px;max-width:360px;max-height:320px;overflow-y:auto;background:var(--popup-bg);border:1px solid var(--border);border-radius:8px;padding:8px 0;font-size:11px;color:var(--text);box-shadow:0 8px 32px rgba(0,0,0,0.3);z-index:10002;line-height:1.5}
.queue-detail-popup.active{display:block}
.queue-detail-popup .qd-title{padding:2px 10px 6px;font-weight:600;font-size:12px;color:var(--accent);border-bottom:1px solid var(--border);margin-bottom:4px}
.queue-detail-popup .qd-row{padding:2px 10px;display:flex;gap:6px;align-items:center}
.queue-detail-popup .qd-type{color:var(--muted);min-width:50px;text-transform:capitalize}
.queue-detail-popup .qd-name{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.queue-detail-popup .qd-badge{font-size:9px;padding:1px 5px;border-radius:4px;font-weight:600}
.queue-detail-popup .qd-badge.running{background:var(--accent);color:#fff}
.queue-detail-popup .qd-badge.pending{background:var(--border);color:var(--muted)}
@media(max-width:700px){
  .table th:nth-child(4),.table td:nth-child(4){display:none}
  body{padding:12px}
  .filterbar input[type=text]{min-width:120px}
}
/* Cookie consent banner */
.cookie-banner{display:none;position:fixed;bottom:0;left:0;right:0;background:var(--card);border-top:1px solid var(--border);padding:10px 20px;font-size:12px;color:var(--muted);z-index:20000;text-align:center;box-shadow:0 -4px 16px rgba(0,0,0,0.15)}
.cookie-banner.active{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap}
.cookie-banner .cb-text{flex:1;min-width:200px;text-align:left}
.cookie-banner .cb-btn{background:var(--accent);color:#fff;border:none;padding:5px 16px;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap}
.cookie-banner .cb-btn:hover{opacity:0.9}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="title">PHP Autoindexed File-Viewer v<?php echo APP_VERSION; ?></div>
    <div class="toolbar">
      <span class="queue-status" id="queueStatus" title="Background tasks"><span id="queueStatusText"></span><div class="queue-detail-popup" id="queueDetailPopup"></div></span>
      <a class="btn" href="?<?php echo http_build_query(['path'=>$currentRel,'action'=>'download']); ?>" title="Download folder as ZIP">⤓ Download folder</a>
      <a class="btn" href="<?php echo $scriptName; ?>">Home</a>
      <button class="theme-toggle" id="themeToggle" title="Toggle light/dark mode">&#9790;</button>
    </div>
  </div>

  <div class="path-bar">
    <?php foreach ($crumbs as $i => $c): ?>
      <?php if ($i > 0): ?><span class="sep">/</span><?php endif; ?>
      <?php if ($i === count($crumbs)-1): ?>
        <span><?php echo htmlspecialchars($c['name']); ?></span>
      <?php else: ?>
        <a href="?<?php echo http_build_query(['path'=>$c['path']]); ?>"><?php echo htmlspecialchars($c['name']); ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>

  <div class="filterbar">
    <input type="text" id="searchInput" placeholder="Search by name..." autocomplete="off"/>
    <select id="filterType">
      <option value="">All types</option>
      <option value="folder">Folders</option>
      <option value="image">Images</option>
      <option value="audio">Audio</option>
      <option value="video">Video</option>
      <option value="document">Documents</option>
      <option value="archive">Archives</option>
      <option value="other">Other</option>
    </select>
  </div>

  <?php if (count($items) === 0): ?>
    <div class="empty">This folder is empty.</div>
  <?php else: ?>
  <table class="table" id="filetable">
    <thead>
      <tr>
        <th data-sort="name" class="sorted">Name <span class="arrow">▲</span></th>
        <th data-sort="size" style="width:90px">Size <span class="arrow">▲</span></th>
        <th data-sort="info" style="width:80px">Info <span class="arrow">▲</span></th>
        <th data-sort="mtime" style="width:140px">Modified <span class="arrow">▲</span></th>
        <th style="width:120px">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
        if (trim($currentRel) !== '') {
            $parent = dirname($currentRel);
            if ($parent === '.') $parent = '';
            echo '<tr data-isdir="1" data-name="" data-size="-1" data-mtime="0" data-info="-1" data-cat="folder" class="parentrow">';
            echo '<td><div class="namecell"><span class="icon">↰</span><a href="?'.http_build_query(['path'=>$parent]).'">Parent folder</a></div></td>';
            echo '<td class="small"></td><td class="info-cell"></td><td class="small"></td><td></td>';
            echo '</tr>';
        }
      ?>
      <?php foreach ($items as $it):
        $cat = $it['is_dir'] ? 'folder' : file_category($it['name']);
        $icon = $it['is_dir'] ? '📁' : file_icon($it['name']);
        $relPath = $currentRel === '' ? $it['name'] : ($currentRel.'/'.$it['name']);
        $hasMedia = in_array($cat, ['image','audio','video','document']);
      ?>
        <tr data-isdir="<?php echo $it['is_dir']?'1':'0'; ?>"
            data-name="<?php echo htmlspecialchars($it['name']); ?>"
            data-size="<?php echo $it['size']; ?>"
            data-mtime="<?php echo $it['mtime']; ?>"
            data-info="<?php echo $it['is_dir'] ? '50000' : '0'; ?>"
            data-cat="<?php echo $cat; ?>"
            data-path="<?php echo htmlspecialchars($relPath); ?>"
            <?php if ($hasMedia): ?>data-media="1"<?php endif; ?>>
          <td>
            <div class="namecell">
              <span class="icon"><?php echo $icon; ?></span>
              <?php if ($it['is_dir']): ?>
                <a href="?<?php echo http_build_query(['path'=>$relPath]); ?>"><?php echo safe_basename($it['name']); ?></a>
              <?php else: ?>
                <a href="?<?php echo http_build_query(['path'=>$relPath,'action'=>'download']); ?>"><?php echo safe_basename($it['name']); ?></a>
              <?php endif; ?>
            </div>
          </td>
          <td class="small size-cell"><?php echo $it['is_dir'] ? '<span class="dir-size" data-path="'.htmlspecialchars($relPath).'">…</span>' : format_size($it['size']); ?></td>
          <td class="info-cell"><?php if ($it['is_dir']): ?><span class="dir-entry" data-path="<?php echo htmlspecialchars($relPath); ?>">…</span><?php elseif ($hasMedia): ?><span class="media-tag" data-path="<?php echo htmlspecialchars($relPath); ?>">…</span><div class="media-popup"></div><?php endif; ?></td>
          <td class="small"><?php echo $it['mtime'] ? date('d.m.Y H:i', $it['mtime']) : '-'; ?></td>
          <td>
            <div class="actions">
              <?php if (!$it['is_dir']): ?>
                <a class="btn" href="?<?php echo http_build_query(['path'=>$relPath,'action'=>'download']); ?>">Download</a>
              <?php else: ?>
                <a class="btn" href="?<?php echo http_build_query(['path'=>$relPath]); ?>">Open</a>
                <a class="btn" href="?<?php echo http_build_query(['path'=>$relPath,'action'=>'download']); ?>">Zip</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<div class="cookie-banner" id="cookieBanner">
  <span class="cb-text">This site uses essential cookies to save your preferences (theme, sort order). No tracking or third-party cookies.</span>
  <button class="cb-btn" id="cookieAccept">OK</button>
</div>

<script>
(function(){
// --- Cookie helpers ---
function setCookie(name, value, days){
  var d = new Date(); d.setTime(d.getTime() + (days||365)*24*60*60*1000);
  document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
}
function getCookie(name){
  var m = document.cookie.match('(^|;)\\s*' + name + '=([^;]*)');
  return m ? decodeURIComponent(m[2]) : null;
}

// --- Cookie consent banner ---
if(!getCookie('fb_cookie_ok')){
  var banner = document.getElementById('cookieBanner');
  if(banner) banner.classList.add('active');
  document.getElementById('cookieAccept').addEventListener('click', function(){
    setCookie('fb_cookie_ok', '1', 365);
    banner.classList.remove('active');
  });
}

// --- Theme toggle ---
var html = document.documentElement;
// Theme already set server-side via PHP cookie read; JS handles toggle only
var toggleBtn = document.getElementById('themeToggle');
function updateToggleIcon(){ toggleBtn.textContent = html.classList.contains('light') ? '\u263D' : '\u2600'; }
updateToggleIcon();
toggleBtn.addEventListener('click', function(){
  html.classList.toggle('light');
  setCookie('fb_theme', html.classList.contains('light') ? 'light' : 'dark');
  updateToggleIcon();
});

var table = document.getElementById('filetable');
if (!table) return;
var thead = table.querySelector('thead');
var tbody = table.querySelector('tbody');
var ths = thead.querySelectorAll('th[data-sort]');
var currentSort = 'name', currentDir = 1;
var folderPath = <?php echo json_encode($currentRel); ?>;

// Restore sort preference for this folder
var savedSort = getCookie('fb_sort');
if(savedSort){
  try {
    var prefs = JSON.parse(savedSort);
    if(prefs[folderPath]){
      currentSort = prefs[folderPath].k || 'name';
      currentDir = prefs[folderPath].d || 1;
    }
  } catch(e){}
}

function saveSortPref(){
  var prefs = {};
  try { prefs = JSON.parse(getCookie('fb_sort') || '{}'); } catch(e){}
  prefs[folderPath] = {k: currentSort, d: currentDir};
  // Keep max 50 folders to avoid cookie overflow
  var keys = Object.keys(prefs);
  if(keys.length > 50){ delete prefs[keys[0]]; }
  setCookie('fb_sort', JSON.stringify(prefs));
}

// --- Sorting ---
function getRows(){ return Array.from(tbody.querySelectorAll('tr:not(.parentrow)')); }

function sortTable(key, dir) {
  var rows = getRows();
  rows.sort(function(a,b){
    var ad=parseInt(a.dataset.isdir), bd=parseInt(b.dataset.isdir);
    if(ad!==bd) return bd-ad;
    var av,bv;
    if(key==='name'){
      av=a.dataset.name.toLowerCase(); bv=b.dataset.name.toLowerCase();
      return dir*(av<bv?-1:av>bv?1:0);
    } else if(key==='size'){
      av=parseFloat(a.dataset.size); bv=parseFloat(b.dataset.size);
      return dir*(av-bv);
    } else if(key==='info'){
      av=parseFloat(a.dataset.info)||0; bv=parseFloat(b.dataset.info)||0;
      return dir*(av-bv);
    } else {
      av=parseInt(a.dataset.mtime); bv=parseInt(b.dataset.mtime);
      return dir*(av-bv);
    }
  });
  rows.forEach(function(r){ tbody.appendChild(r); });
}

function updateHeaders(key,dir){
  ths.forEach(function(th){ th.classList.remove('sorted'); th.querySelector('.arrow').textContent='\u25B2'; });
  var act=thead.querySelector('th[data-sort="'+key+'"]');
  if(act){ act.classList.add('sorted'); act.querySelector('.arrow').textContent=dir===1?'\u25B2':'\u25BC'; }
}

ths.forEach(function(th){
  th.addEventListener('click', function(){
    var key=this.dataset.sort;
    if(currentSort===key) currentDir*=-1; else { currentSort=key; currentDir=(key==='info'||key==='size')?-1:1; }
    updateHeaders(currentSort,currentDir);
    sortTable(currentSort,currentDir);
    saveSortPref();
  });
});

// Apply saved sort on load
updateHeaders(currentSort, currentDir);
sortTable(currentSort, currentDir);

// --- Search & filter ---
var searchInput = document.getElementById('searchInput');
var filterType = document.getElementById('filterType');

function applyFilters(){
  var q = searchInput.value.toLowerCase();
  var t = filterType.value;
  getRows().forEach(function(r){
    var name = r.dataset.name.toLowerCase();
    var cat = r.dataset.cat;
    var matchName = !q || name.indexOf(q) !== -1;
    var matchType = !t || cat === t;
    r.classList.toggle('hidden', !(matchName && matchType));
  });
}
searchInput.addEventListener('input', applyFilters);
filterType.addEventListener('change', applyFilters);

// =========== QUEUE-BASED ASYNC LOADING ===========
var queueStatusEl = document.getElementById('queueStatus');
var queueDetailPopup = document.getElementById('queueDetailPopup');
var pendingTasks = {}; // key = type:path → {type, path, applied: bool}
var workInterval = null;
var pollInterval = null;
var working = false;
var queueHoverTimer = null;
var queueDetailLoading = false;

function taskKey(type, path){ return type + ':' + path; }

function updateStatusUI(status){
  var textEl = document.getElementById('queueStatusText');
  if(!textEl) return;
  if(!status || status.total === 0){
    textEl.innerHTML = '';
    queueStatusEl.style.display = 'none';
  } else {
    queueStatusEl.style.display = '';
    textEl.innerHTML = '<span class="qs-spinner"></span>' +
      status.running + ' processing, ' + status.pending + ' queued';
  }
}

// Collect all tasks needed for this page and batch-enqueue them
function collectAndEnqueue(){
  var tasks = [];

  // Folder sizes (lowest priority — enqueued first, so they sit at bottom of stack)
  document.querySelectorAll('.dir-size').forEach(function(el){
    var path = el.dataset.path;
    var row = el.closest('tr');
    var entryEl = row ? row.querySelector('.dir-entry') : null;
    var key = taskKey('dirsize', path);
    pendingTasks[key] = {type:'dirsize', path:path, applied:false, el:el, entryEl:entryEl};
    tasks.push({type:'dirsize', path:path});
  });

  // Media info summaries (medium priority)
  document.querySelectorAll('.media-tag').forEach(function(el){
    var path = el.dataset.path;
    var key = taskKey('info', path);
    pendingTasks[key] = {type:'info', path:path, applied:false, el:el};
    tasks.push({type:'info', path:path});
  });

  // Thumbnails for image/video/document (highest priority — enqueued last → top of stack)
  document.querySelectorAll('tr[data-media]').forEach(function(row){
    var cat = row.dataset.cat;
    if(cat !== 'image' && cat !== 'video' && cat !== 'document') return;
    var path = row.dataset.path;
    var key = taskKey('thumb', path);
    pendingTasks[key] = {type:'thumb', path:path, applied:false, row:row};
    tasks.push({type:'thumb', path:path});
  });

  if(tasks.length === 0) return;

  // Enqueue all at once
  fetch('?action=api_enqueue', {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({tasks:tasks})
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    updateStatusUI(d.status);
    // Start the work loop
    startWorkLoop();
  })
  .catch(function(e){ console.error('Enqueue failed', e); });
}

function startWorkLoop(){
  if(workInterval) return;
  // Trigger workers rapidly at first, then slow down
  doWork();
  workInterval = setInterval(doWork, 1500);
  // Also poll for results from other workers/tabs
  pollInterval = setInterval(pollResults, 2000);
}

function stopWorkLoop(){
  if(workInterval){ clearInterval(workInterval); workInterval=null; }
  if(pollInterval){ clearInterval(pollInterval); pollInterval=null; }
  updateStatusUI(null);
}

function doWork(){
  if(working) return;
  working = true;
  fetch('?action=api_work')
    .then(function(r){ return r.json(); })
    .then(function(d){
      working = false;
      // Apply completed results
      if(d.completed) d.completed.forEach(applyResult);
      updateStatusUI(d.status);
      if(d.status && d.status.total === 0){
        // All done — one final poll to catch any stragglers, then stop
        pollResults().then(function(){ stopWorkLoop(); });
      }
    })
    .catch(function(){ working = false; });
}

function pollResults(){
  // Ask server for results of our pending (unapplied) tasks
  var waiting = [];
  for(var key in pendingTasks){
    if(!pendingTasks[key].applied) waiting.push({type:pendingTasks[key].type, path:pendingTasks[key].path});
  }
  if(waiting.length === 0){ stopWorkLoop(); return Promise.resolve(); }

  return fetch('?action=api_poll', {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({waiting:waiting})
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    if(d.results) d.results.forEach(applyResult);
    updateStatusUI(d.status);
    if(d.status && d.status.total === 0){
      // Check if all applied
      var allDone = true;
      for(var k in pendingTasks){ if(!pendingTasks[k].applied) allDone=false; }
      if(allDone) stopWorkLoop();
    }
  })
  .catch(function(){});
}

function applyResult(r){
  if(!r || !r.type || !r.path) return;
  var key = taskKey(r.type, r.path);
  var task = pendingTasks[key];
  if(!task || task.applied) return;
  if(r.status && r.status !== 'done') return; // not finished yet

  var result = r.result;
  if(!result){ task.applied=true; return; } // error or null

  if(r.type === 'dirsize' && task.el){
    task.el.textContent = result.formatted || '-';
    var row = task.el.closest('tr');
    if(row && result.size !== undefined) row.dataset.size = result.size;
    // Update entry count in info column
    var totalEntries = (result.files||0) + (result.dirs||0);
    if(task.entryEl){
      var parts = [];
      if(result.files) parts.push(result.files + (result.files===1?' file':' files'));
      if(result.dirs) parts.push(result.dirs + (result.dirs===1?' folder':' folders'));
      task.entryEl.textContent = parts.length ? parts.join(', ') : 'empty';
    }
    if(row) row.dataset.info = 50000 + totalEntries;
    task.applied = true;
    if(currentSort === 'size' || currentSort === 'info') sortTable(currentSort, currentDir);
  }
  else if(r.type === 'info' && task.el){
    if(result.summary){
      task.el.textContent = result.summary;
      var row = task.el.closest('tr');
      if(row && result.sort_value !== undefined) row.dataset.info = result.sort_value;
    } else {
      task.el.textContent = '';
    }
    task.applied = true;
    if(currentSort === 'info') sortTable(currentSort, currentDir);
  }
  else if(r.type === 'thumb' && task.row){
    // Thumbnail generated — pre-warm the cache by marking it ready
    // Actual display happens on hover via api_thumb
    task.row.dataset.thumbReady = '1';
    task.applied = true;
  }
}

// Kick off!
collectAndEnqueue();

// --- Queue status hover popup ---
queueStatusEl.addEventListener('mouseenter', function(){
  clearTimeout(queueHoverTimer);
  if(queueDetailPopup) queueDetailPopup.classList.add('active');
  loadQueueDetail();
});
queueStatusEl.addEventListener('mouseleave', function(){
  queueHoverTimer = setTimeout(function(){
    if(queueDetailPopup) queueDetailPopup.classList.remove('active');
  }, 200);
});

function loadQueueDetail(){
  if(queueDetailLoading) return;
  queueDetailLoading = true;
  fetch('?action=api_queue_detail')
    .then(function(r){ return r.json(); })
    .then(function(d){
      queueDetailLoading = false;
      if(!queueDetailPopup) return;
      if(!d.tasks || d.tasks.length === 0){
        queueDetailPopup.innerHTML = '<div class="qd-title">No active tasks</div>';
        return;
      }
      var h = '<div class="qd-title">' + (d.status.running||0) + ' running, ' + (d.status.pending||0) + ' queued</div>';
      d.tasks.forEach(function(t){
        h += '<div class="qd-row">';
        h += '<span class="qd-type">' + esc(t.type) + '</span>';
        h += '<span class="qd-name" title="' + esc(t.path) + '">' + esc(t.path) + '</span>';
        h += '<span class="qd-badge ' + t.status + '">' + t.status + '</span>';
        h += '</div>';
      });
      queueDetailPopup.innerHTML = h;
    })
    .catch(function(){ queueDetailLoading = false; });
}

// --- Hover popup for media detail (on-demand, not queued) ---
var activePopup = null;
var popupTimer = null;

function positionFixed(anchor, popup){
  var r = anchor.getBoundingClientRect();
  popup.style.left = r.left + 'px';
  popup.style.top = (r.bottom + 4) + 'px';
  requestAnimationFrame(function(){
    var pr = popup.getBoundingClientRect();
    var vw = window.innerWidth, vh = window.innerHeight;
    // Flip above anchor if overflows bottom
    if(pr.bottom > vh){
      var above = r.top - pr.height - 4;
      popup.style.top = (above >= 0 ? above : 4) + 'px';
    }
    // Constrain top to viewport
    pr = popup.getBoundingClientRect();
    if(pr.top < 0) popup.style.top = '4px';
    // Constrain right edge
    if(pr.right > vw) popup.style.left = Math.max(4, vw - pr.width - 8) + 'px';
    // Constrain left edge
    pr = popup.getBoundingClientRect();
    if(pr.left < 0) popup.style.left = '4px';
  });
}

document.querySelectorAll('tr[data-media]').forEach(function(row){
  var tag = row.querySelector('.media-tag');
  var popup = row.querySelector('.media-popup');
  if(!tag || !popup) return;
  var loaded = false;

  tag.addEventListener('mouseenter', function(){
    clearTimeout(popupTimer);
    if(activePopup && activePopup !== popup){ activePopup.classList.remove('active'); }
    if(!loaded){
      var path = tag.dataset.path;
      popup.innerHTML = '<span style="color:var(--muted)">Loading…</span>';
      popup.classList.add('active');
      positionFixed(tag, popup);
      activePopup = popup;
      fetch('?'+new URLSearchParams({action:'api_detail',path:path}))
        .then(function(r){return r.json();})
        .then(function(d){ popup.innerHTML = renderDetail(d); loaded=true; positionFixed(tag, popup); })
        .catch(function(){ popup.innerHTML='Error loading info'; });
    } else {
      popup.classList.add('active');
      positionFixed(tag, popup);
      activePopup = popup;
    }
  });

  tag.addEventListener('mouseleave', function(){
    popupTimer = setTimeout(function(){ popup.classList.remove('active'); activePopup=null; }, 200);
  });
  popup.addEventListener('mouseenter', function(){ clearTimeout(popupTimer); });
  popup.addEventListener('mouseleave', function(){ popup.classList.remove('active'); activePopup=null; });
});

// --- Thumbnail hover on name column for image/video ---
var thumbPopup = document.createElement('div');
thumbPopup.className = 'thumb-popup';
document.body.appendChild(thumbPopup);
var thumbCache = {};
var thumbTimer = null;

document.querySelectorAll('tr[data-media]').forEach(function(row){
  var cat = row.dataset.cat;
  if(cat !== 'image' && cat !== 'video' && cat !== 'document') return;
  var nc = row.querySelector('.namecell');
  if(!nc) return;
  var path = row.dataset.path;

  nc.addEventListener('mouseenter', function(){
    clearTimeout(thumbTimer);
    if(thumbCache[path]){
      showThumb(thumbCache[path], nc);
    } else if(thumbCache[path] !== false){
      // Try to load — will work if thumb is ready (queued or cached)
      thumbPopup.innerHTML = '<span style="padding:8px;color:var(--muted);font-size:12px">Loading…</span>';
      thumbPopup.classList.add('active');
      posThumb(nc);
      fetch('?'+new URLSearchParams({action:'api_thumb',path:path}))
        .then(function(r){ if(!r.ok) throw 0; return r.blob(); })
        .then(function(bl){ var url=URL.createObjectURL(bl); thumbCache[path]=url; showThumb(url, nc); })
        .catch(function(){ thumbCache[path]=false; thumbPopup.classList.remove('active'); });
    }
  });
  nc.addEventListener('mouseleave', function(){
    thumbTimer = setTimeout(function(){ thumbPopup.classList.remove('active'); }, 150);
  });
});

function showThumb(url, anchor){
  thumbPopup.innerHTML = '<img src="'+url+'">';
  thumbPopup.classList.add('active');
  posThumb(anchor);
}
function posThumb(anchor){
  var r = anchor.getBoundingClientRect();
  thumbPopup.style.left = (r.left + 20) + 'px';
  thumbPopup.style.top = (r.bottom + 4) + 'px';
  requestAnimationFrame(function(){
    var pr = thumbPopup.getBoundingClientRect();
    if(pr.bottom > window.innerHeight) thumbPopup.style.top = (r.top - pr.height - 4) + 'px';
    if(pr.right > window.innerWidth) thumbPopup.style.left = Math.max(4, window.innerWidth - pr.width - 8) + 'px';
  });
}

// --- Render helpers ---
function renderDetail(d){
  var h = '<h4>'+esc(d.name||'')+'</h4>';
  var det = d.detail;
  if(!det) return h+'<span style="color:var(--muted)">No info available</span>';

  if(d.category === 'document'){
    if(det.format) h += mpRow('Format', det.format);
    if(det.pages) h += mpRow('Pages', det.pages);
    if(det.title) h += mpRow('Title', det.title);
    if(det.author) h += mpRow('Author', det.author);
    if(det.language) h += mpRow('Language', det.language);
    if(det.creator) h += mpRow('Creator', det.creator);
    if(det.producer) h += mpRow('Producer', det.producer);
    if(det.pdf_version) h += mpRow('PDF version', det.pdf_version);
    if(det.mime) h += mpRow('MIME', det.mime);
    if(det.encrypted) h += mpRow('Encrypted', det.encrypted);
    if(det.page_size) h += mpRow('Page size', det.page_size);
    if(det.creation_date) h += mpRow('Created', det.creation_date);
  } else if(d.category === 'image'){
    if(det.width && det.height) h += mpRow('Resolution', det.width+'\u00D7'+det.height);
    if(det.vector) h += mpRow('Type', 'Vector graphic');
    else if(det.megapixels) h += mpRow('Megapixels', det.megapixels+' MP');
    if(det.format) h += mpRow('Format', det.format);
    if(det.color_mode) h += mpRow('Color', det.color_mode + (det.bits ? ' '+det.bits+'bit' : ''));
    if(det.has_alpha) h += mpRow('Alpha', 'Yes');
    if(det.animated) h += mpRow('Animated', 'Yes');
  } else {
    if(det.duration) h += mpRow('Duration', formatDur(det.duration));
    if(det.format) h += mpRow('Container', det.format);
    if(det.overall_bitrate) h += mpRow('Overall bitrate', formatBr(det.overall_bitrate));
    if(det.streams && det.streams.length){
      det.streams.forEach(function(s){
        h += '<div class="stream">';
        h += '<strong style="color:var(--accent);text-transform:capitalize">'+esc(s.type)+'</strong>';
        if(s.format) h += ' \u2014 '+esc(s.format);
        if(s.language) h += ' ['+esc(s.language)+']';
        h += '<br>';
        if(s.type==='video'){
          if(s.width) h += mpRow('Resolution', s.width+'\u00D7'+s.height);
          if(s.bitrate) h += mpRow('Bitrate', formatBr(s.bitrate));
          if(s.framerate) h += mpRow('FPS', s.framerate);
          if(s.color_space) h += mpRow('Color', s.color_space.trim());
          if(s.bit_depth) h += mpRow('Bit depth', s.bit_depth);
        }
        if(s.type==='audio'){
          if(s.bitrate) h += mpRow('Bitrate', formatBr(s.bitrate));
          if(s.sample_rate) h += mpRow('Sample rate', (parseInt(s.sample_rate)/1000)+'kHz');
          if(s.channels) h += mpRow('Channels', s.channels);
          if(s.bit_depth) h += mpRow('Bit depth', s.bit_depth);
        }
        h += '</div>';
      });
    }
  }
  return h;
}

function mpRow(label,val){ return '<div class="mp-row"><span class="mp-label">'+esc(label)+'</span><span class="mp-val">'+esc(String(val))+'</span></div>'; }
function esc(s){ var d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
function formatDur(s){ s=Math.round(s); var h=Math.floor(s/3600),m=Math.floor((s%3600)/60),sec=s%60; return h>0?(h+':'+pad(m)+':'+pad(sec)+' h'):(m+':'+pad(sec)+' min'); }
function pad(n){ return n<10?'0'+n:n; }
function formatBr(b){ var k=parseInt(b)/1000; return k>=1000?(Math.round(k/100)/10+' Mbps'):(Math.round(k)+' kbps'); }

// =========== LIVE DIRECTORY CHANGE DETECTION ===========
var dirCheckHash = null;
var dirCheckPath = <?php echo json_encode($currentRel); ?>;
var dirCheckBusy = false;

function formatSizeJS(bytes){
  if(bytes < 0) return '?';
  if(bytes < 1024) return bytes+' B';
  var u=['KB','MB','GB','TB'], i=-1;
  do { bytes/=1024; i++; } while(bytes>=1024 && i<u.length-1);
  return (Math.round(bytes*100)/100)+' '+u[i];
}
function formatDateJS(ts){
  if(!ts) return '-';
  var d=new Date(ts*1000);
  var dd=String(d.getDate()).padStart(2,'0'), mm=String(d.getMonth()+1).padStart(2,'0');
  var hh=String(d.getHours()).padStart(2,'0'), mi=String(d.getMinutes()).padStart(2,'0');
  return dd+'.'+mm+'.'+d.getFullYear()+' '+hh+':'+mi;
}

function buildRowHTML(it, scriptName){
  var safeName = esc(it.name);
  var rp = encodeURIComponent(it.path).replace(/%2F/gi,'/');
  var pathQ = 'path='+encodeURIComponent(it.path);
  var h = '<tr data-isdir="'+(it.is_dir?'1':'0')+'" data-name="'+esc(it.name)+'" data-size="'+it.size+'" data-mtime="'+it.mtime+'" data-info="'+(it.is_dir?'50000':'0')+'" data-cat="'+it.cat+'" data-path="'+esc(it.path)+'"';
  if(it.has_media) h += ' data-media="1"';
  h += '>';
  h += '<td><div class="namecell"><span class="icon">'+it.icon+'</span>';
  if(it.is_dir){
    h += '<a href="?'+pathQ+'">'+safeName+'</a>';
  } else {
    h += '<a href="?'+pathQ+'&action=download">'+safeName+'</a>';
  }
  h += '</div></td>';
  h += '<td class="small size-cell">';
  if(it.is_dir) h += '<span class="dir-size" data-path="'+esc(it.path)+'">…</span>';
  else h += formatSizeJS(it.size);
  h += '</td>';
  h += '<td class="info-cell">';
  if(it.is_dir) h += '<span class="dir-entry" data-path="'+esc(it.path)+'">…</span>';
  else if(it.has_media) h += '<span class="media-tag" data-path="'+esc(it.path)+'">…</span><div class="media-popup"></div>';
  h += '</td>';
  h += '<td class="small">'+formatDateJS(it.mtime)+'</td>';
  h += '<td><div class="actions">';
  if(!it.is_dir){
    h += '<a class="btn" href="?'+pathQ+'&action=download">Download</a>';
  } else {
    h += '<a class="btn" href="?'+pathQ+'">Open</a>';
    h += '<a class="btn" href="?'+pathQ+'&action=download">Zip</a>';
  }
  h += '</div></td></tr>';
  return h;
}

function refreshDirectory(){
  if(dirCheckBusy || !table) return;
  dirCheckBusy = true;
  fetch('?'+new URLSearchParams({action:'api_dircheck',path:dirCheckPath}))
    .then(function(r){ return r.json(); })
    .then(function(d){
      if(!d.hash){ dirCheckBusy=false; return; }
      if(dirCheckHash === null){ dirCheckHash = d.hash; dirCheckBusy=false; return; }
      if(d.hash === dirCheckHash){ dirCheckBusy=false; return; }
      // Hash changed — fetch full listing
      return fetch('?'+new URLSearchParams({action:'api_dirlist',path:dirCheckPath}))
        .then(function(r){ return r.json(); })
        .then(function(data){
          dirCheckHash = data.hash;
          dirCheckBusy = false;
          if(!data.items) return;
          applyDirectoryUpdate(data.items);
        });
    })
    .catch(function(){ dirCheckBusy=false; });
}

function applyDirectoryUpdate(items){
  if(!tbody) return;
  // Build map of current rows by name
  var existingRows = {};
  getRows().forEach(function(r){ existingRows[r.dataset.name] = r; });

  // Build map of new items by name
  var newItems = {};
  items.forEach(function(it){ newItems[it.name] = it; });

  // Remove rows not in new listing
  for(var name in existingRows){
    if(!newItems[name]){
      existingRows[name].remove();
    }
  }

  // Add or update rows
  var needsEnqueue = [];
  items.forEach(function(it){
    var existing = existingRows[it.name];
    if(existing){
      // Update size/mtime if changed
      if(String(it.size) !== existing.dataset.size){
        existing.dataset.size = it.size;
        var sc = existing.querySelector('.size-cell');
        if(sc && !it.is_dir) sc.textContent = formatSizeJS(it.size);
      }
      if(String(it.mtime) !== existing.dataset.mtime){
        existing.dataset.mtime = it.mtime;
        var cells = existing.querySelectorAll('td');
        if(cells[3]) cells[3].textContent = formatDateJS(it.mtime);
      }
    } else {
      // New item — insert into tbody
      var tmp = document.createElement('tbody');
      tmp.innerHTML = buildRowHTML(it);
      var newRow = tmp.firstChild;
      tbody.appendChild(newRow);
      // Wire up hover handlers for new row
      wireRowHandlers(newRow);
      needsEnqueue.push(it);
    }
  });

  // Re-sort with current settings
  sortTable(currentSort, currentDir);

  // Enqueue tasks for new items
  if(needsEnqueue.length > 0){
    var tasks = [];
    needsEnqueue.forEach(function(it){
      if(it.is_dir){
        var el = tbody.querySelector('tr[data-path="'+CSS.escape(it.path)+'"] .dir-size');
        if(el){
          var entryEl = tbody.querySelector('tr[data-path="'+CSS.escape(it.path)+'"] .dir-entry');
          var key = taskKey('dirsize', it.path);
          pendingTasks[key] = {type:'dirsize', path:it.path, applied:false, el:el, entryEl:entryEl};
          tasks.push({type:'dirsize', path:it.path});
        }
      }
      if(it.has_media){
        var tag = tbody.querySelector('tr[data-path="'+CSS.escape(it.path)+'"] .media-tag');
        if(tag){
          var key2 = taskKey('info', it.path);
          pendingTasks[key2] = {type:'info', path:it.path, applied:false, el:tag};
          tasks.push({type:'info', path:it.path});
        }
        if(it.cat==='image'||it.cat==='video'||it.cat==='document'){
          var row = tbody.querySelector('tr[data-path="'+CSS.escape(it.path)+'"]');
          if(row){
            var key3 = taskKey('thumb', it.path);
            pendingTasks[key3] = {type:'thumb', path:it.path, applied:false, row:row};
            tasks.push({type:'thumb', path:it.path});
          }
        }
      }
    });
    if(tasks.length > 0){
      fetch('?action=api_enqueue', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({tasks:tasks})
      })
      .then(function(r){ return r.json(); })
      .then(function(d){ updateStatusUI(d.status); startWorkLoop(); })
      .catch(function(){});
    }
  }
}

function wireRowHandlers(row){
  // Media detail hover
  var tag = row.querySelector('.media-tag');
  var popup = row.querySelector('.media-popup');
  if(tag && popup){
    var loaded = false;
    tag.addEventListener('mouseenter', function(){
      clearTimeout(popupTimer);
      if(activePopup && activePopup !== popup){ activePopup.classList.remove('active'); }
      if(!loaded){
        popup.innerHTML = '<span style="color:var(--muted)">Loading…</span>';
        popup.classList.add('active');
        positionFixed(tag, popup);
        activePopup = popup;
        fetch('?'+new URLSearchParams({action:'api_detail',path:tag.dataset.path}))
          .then(function(r){return r.json();})
          .then(function(d){ popup.innerHTML = renderDetail(d); loaded=true; positionFixed(tag, popup); })
          .catch(function(){ popup.innerHTML='Error loading info'; });
      } else {
        popup.classList.add('active'); positionFixed(tag, popup); activePopup = popup;
      }
    });
    tag.addEventListener('mouseleave', function(){
      popupTimer = setTimeout(function(){ popup.classList.remove('active'); activePopup=null; }, 200);
    });
    popup.addEventListener('mouseenter', function(){ clearTimeout(popupTimer); });
    popup.addEventListener('mouseleave', function(){ popup.classList.remove('active'); activePopup=null; });
  }
  // Thumb hover
  var cat = row.dataset.cat;
  if(cat==='image'||cat==='video'||cat==='document'){
    var nc = row.querySelector('.namecell');
    if(nc){
      var path = row.dataset.path;
      nc.addEventListener('mouseenter', function(){
        clearTimeout(thumbTimer);
        if(thumbCache[path]){ showThumb(thumbCache[path], nc); }
        else if(thumbCache[path] !== false){
          thumbPopup.innerHTML = '<span style="padding:8px;color:var(--muted);font-size:12px">Loading…</span>';
          thumbPopup.classList.add('active'); posThumb(nc);
          fetch('?'+new URLSearchParams({action:'api_thumb',path:path}))
            .then(function(r){ if(!r.ok) throw 0; return r.blob(); })
            .then(function(bl){ var url=URL.createObjectURL(bl); thumbCache[path]=url; showThumb(url, nc); })
            .catch(function(){ thumbCache[path]=false; thumbPopup.classList.remove('active'); });
        }
      });
      nc.addEventListener('mouseleave', function(){
        thumbTimer = setTimeout(function(){ thumbPopup.classList.remove('active'); }, 150);
      });
    }
  }
}

setInterval(refreshDirectory, 10000);

})();
</script>
</body>
</html>
