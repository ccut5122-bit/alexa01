<?php
define('PASSWORD', 'Alexa');
define('FB_URL', 'https://alexa-a6ad8-default-rtdb.firebaseio.com');
define('UPLOAD_MAX', 50 * 1024 * 1024);
define('FILES_DIR', __DIR__ . '/files');

$auth = isset($_GET['pwd']) && $_GET['pwd'] === PASSWORD;
if (!$auth) {
    header('Content-Type: application/json');
    die(json_encode(['error' => 'auth_required']));
}

if (!is_dir(FILES_DIR)) { mkdir(FILES_DIR, 0777, true); @chmod(FILES_DIR, 0777); }

$action = $_GET['action'] ?? '';

if ($action === 'upload' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header('Content-Type: application/json');
        die(json_encode(['error' => 'upload_failed', 'code' => $file['error']]));
    }
    if ($file['size'] > UPLOAD_MAX) {
        header('Content-Type: application/json');
        die(json_encode(['error' => 'file_too_large', 'max' => UPLOAD_MAX]));
    }
    $orig = basename($file['name']);
    $ext = pathinfo($orig, PATHINFO_EXTENSION);
    $fid = uniqid() . '_' . bin2hex(random_bytes(4));
    $fname = $fid . ($ext ? '.' . $ext : '');
    $dest = FILES_DIR . '/' . $fname;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        header('Content-Type: application/json');
        die(json_encode(['error' => 'move_failed']));
    }
    $meta = [
        'name' => $orig,
        'fname' => $fname,
        'size' => $file['size'],
        'type' => $file['type'] ?: mime_content_type($dest),
        'uploaded' => date('Y-m-d H:i:s'),
        'fid' => $fid,
    ];
    $ch = curl_init(FB_URL . '/files/' . $fid . '.json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => json_encode($meta),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 15,
    ]);
    curl_exec($ch);
    curl_close($ch);
    header('Content-Type: application/json');
    die(json_encode(['ok' => true, 'file' => $meta]));
}

if ($action === 'list') {
    $ch = curl_init(FB_URL . '/files.json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    $files = json_decode($data, true) ?: [];
    $list = [];
    foreach ($files as $fid => $f) {
        if (is_array($f) && isset($f['name'])) {
            $f['url'] = '?pwd=' . PASSWORD . '&action=dl&fid=' . $fid;
            $list[] = $f;
        }
    }
    usort($list, fn($a, $b) => ($b['uploaded'] ?? '') <=> ($a['uploaded'] ?? ''));
    header('Content-Type: application/json');
    die(json_encode($list));
}

if ($action === 'dl' && isset($_GET['fid'])) {
    $fid = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['fid']);
    $ch = curl_init(FB_URL . '/files/' . $fid . '.json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $meta = json_decode(curl_exec($ch), true);
    curl_close($ch);
    if (!$meta || !isset($meta['fname'])) {
        header('Content-Type: application/json');
        die(json_encode(['error' => 'not_found']));
    }
    $path = FILES_DIR . '/' . $meta['fname'];
    if (!file_exists($path)) {
        header('Content-Type: application/json');
        die(json_encode(['error' => 'file_missing']));
    }
    header('Content-Type: ' . ($meta['type'] ?: 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . $meta['name'] . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

if ($action === 'dlall') {
    $ch = curl_init(FB_URL . '/files.json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    $files = json_decode($data, true) ?: [];
    $zip = new ZipArchive();
    $tmp = tempnam(sys_get_temp_dir(), 'files_');
    if ($zip->open($tmp, ZipArchive::CREATE) !== true) {
        header('Content-Type: application/json');
        die(json_encode(['error' => 'zip_failed']));
    }
    $count = 0;
    foreach ($files as $fid => $f) {
        if (!is_array($f) || !isset($f['fname'])) continue;
        $path = FILES_DIR . '/' . $f['fname'];
        if (file_exists($path)) {
            $zip->addFile($path, $f['name']);
            $count++;
        }
    }
    $zip->close();
    if ($count === 0) {
        unlink($tmp);
        header('Content-Type: application/json');
        die(json_encode(['error' => 'no_files']));
    }
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="all_files_' . date('Ymd') . '.zip"');
    header('Content-Length: ' . filesize($tmp));
    readfile($tmp);
    unlink($tmp);
    exit;
}

if ($action === 'delete' && isset($_GET['fid'])) {
    $fid = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['fid']);
    $ch = curl_init(FB_URL . '/files/' . $fid . '.json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $meta = json_decode(curl_exec($ch), true);
    curl_close($ch);
    if ($meta && isset($meta['fname'])) {
        $path = FILES_DIR . '/' . $meta['fname'];
        if (file_exists($path)) unlink($path);
    }
    $ch = curl_init(FB_URL . '/files/' . $fid . '.json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_TIMEOUT => 10,
    ]);
    curl_exec($ch);
    curl_close($ch);
    header('Content-Type: application/json');
    die(json_encode(['ok' => true]));
}

if ($action === 'img' && isset($_GET['fid'])) {
    $fid = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['fid']);
    $ch = curl_init(FB_URL . '/files/' . $fid . '.json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $meta = json_decode(curl_exec($ch), true);
    curl_close($ch);
    if (!$meta || !isset($meta['fname'])) {
        header('Content-Type: application/json');
        die(json_encode(['error' => 'not_found']));
    }
    $path = FILES_DIR . '/' . $meta['fname'];
    if (!file_exists($path)) {
        header('Content-Type: application/json');
        die(json_encode(['error' => 'file_missing']));
    }
    $mt = mime_content_type($path);
    if (strpos($mt, 'image/') !== 0 && strpos($mt, 'video/') !== 0) {
        header('Content-Type: application/json');
        die(json_encode(['error' => 'not_media']));
    }
    header('Content-Type: ' . $mt);
    readfile($path);
    exit;
}

// --- HTML ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<title>MyVault - File Manager</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#080812;color:#e0e0f0;min-height:100vh;overflow-x:hidden}

.bg{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;overflow:hidden;pointer-events:none}
.bg-orbs{position:absolute;width:100%;height:100%}
.bg-orbs span{position:absolute;border-radius:50%;opacity:.06;animation:orbFloat 20s infinite ease-in-out}
.bg-orbs span:nth-child(1){width:600px;height:600px;background:radial-gradient(circle,#6c3cf0,transparent);top:-10%;left:-10%;animation-delay:0s}
.bg-orbs span:nth-child(2){width:500px;height:500px;background:radial-gradient(circle,#1a9fff,transparent);bottom:-15%;right:-10%;animation-delay:-7s}
.bg-orbs span:nth-child(3){width:350px;height:350px;background:radial-gradient(circle,#f03c9f,transparent);top:40%;left:60%;animation-delay:-14s}
@keyframes orbFloat{0%,100%{transform:translate(0,0) scale(1)}25%{transform:translate(60px,-40px) scale(1.05)}50%{transform:translate(-30px,60px) scale(.95)}75%{transform:translate(40px,30px) scale(1.02)}}
.bg-grid{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(108,60,240,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(108,60,240,.03) 1px,transparent 1px);background-size:60px 60px;animation:gridShift 8s linear infinite}
@keyframes gridShift{0%{transform:translate(0,0)}100%{transform:translate(60px,60px)}}

.glass{background:rgba(255,255,255,.025);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border:1px solid rgba(255,255,255,.05);border-radius:14px;transition:all .3s cubic-bezier(.4,0,.2,1)}
.glass:hover{border-color:rgba(255,255,255,.09);transform:translateY(-1px)}

.head{padding:18px 24px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.04);position:relative;z-index:1;flex-wrap:wrap;gap:10px}
.head-l{display:flex;align-items:center;gap:10px}
.head-l .hl{width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#f03c9f,#6c3cf0);display:flex;align-items:center;justify-content:center}
.head-l .hl::after{content:'';width:14px;height:14px;border:2px solid rgba(255,255,255,.25);border-radius:3px;transform:rotate(45deg)}
.head-l h1{font-size:18px;font-weight:800;letter-spacing:-.5px;background:linear-gradient(135deg,#f03c9f,#6c3cf0);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.head-r{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.h-btn{padding:8px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.06);background:rgba(255,255,255,.03);color:rgba(255,255,255,.6);font-size:11px;cursor:pointer;transition:all .3s;font-family:inherit;font-weight:500}
.h-btn:hover{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.1)}
.h-btn.red{background:linear-gradient(135deg,#f03c6c,#c03050);border:none;color:#fff}
.h-btn.prim{background:linear-gradient(135deg,#6c3cf0,#4a1fc0);border:none;color:#fff}
.h-btn.prim:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(108,60,240,.2)}
.h-info{font-size:11px;color:rgba(255,255,255,.35);padding:5px 10px;background:rgba(255,255,255,.02);border-radius:6px;border:1px solid rgba(255,255,255,.03)}

.main{padding:20px 24px;position:relative;z-index:1;max-width:1200px;margin:0 auto}

/* Upload zone */
.uzone{border:2px dashed rgba(255,255,255,.08);border-radius:16px;padding:40px 20px;text-align:center;cursor:pointer;transition:all .3s;margin-bottom:24px}
.uzone:hover,.uzone.dragover{border-color:rgba(108,60,240,.3);background:rgba(108,60,240,.03)}
.uzone .uz-icon{width:48px;height:48px;margin:0 auto 12px;border-radius:12px;background:linear-gradient(135deg,rgba(108,60,240,.15),rgba(26,159,255,.1));display:flex;align-items:center;justify-content:center}
.uzone .uz-icon::after{content:'';width:20px;height:20px;border:2px solid rgba(108,60,240,.3);border-radius:4px;position:relative}
.uzone .uz-icon::before{content:'';position:absolute;width:2px;height:20px;background:rgba(108,60,240,.3);border-radius:1px;transform:rotate(90deg)}
.uzone p{font-size:13px;color:rgba(255,255,255,.4);margin-bottom:4px}
.uzone .sub{font-size:10px;color:rgba(255,255,255,.2)}
.uzone input{display:none}

.pbar{height:3px;background:rgba(255,255,255,.05);border-radius:2px;margin-top:12px;overflow:hidden;display:none}
.pbar-in{height:100%;width:0%;background:linear-gradient(90deg,#6c3cf0,#1a9fff);border-radius:2px;transition:width .3s}

/* Filters */
.fbar{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;align-items:center}
.fbar .count{font-size:12px;color:rgba(255,255,255,.3);margin-left:auto}
.tag{display:inline-block;padding:4px 10px;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer;transition:all .3s;border:1px solid transparent;background:rgba(255,255,255,.04);color:rgba(255,255,255,.4)}
.tag.act{background:rgba(108,60,240,.15);border-color:rgba(108,60,240,.2);color:#6c3cf0}
.tag:hover{background:rgba(255,255,255,.07)}

/* Grid */
.grids{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px}
.card{position:relative;overflow:hidden;border-radius:12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.04);transition:all .3s;animation:cardIn .4s ease-out both}
.card:hover{border-color:rgba(255,255,255,.08);transform:translateY(-2px)}
.card .thumb{width:100%;aspect-ratio:1;object-fit:cover;display:block;background:rgba(255,255,255,.02)}
.card .vid{display:flex;align-items:center;justify-content:center;width:100%;aspect-ratio:1;background:rgba(255,255,255,.02);position:relative}
.card .vid::after{content:'';width:0;height:0;border:16px solid transparent;border-left:24px solid rgba(255,255,255,.15);border-right:0}
.card .info{padding:10px 12px;display:flex;align-items:center;gap:8px}
.card .info .nm{font-size:12px;color:rgba(255,255,255,.7);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1}
.card .info .sz{font-size:10px;color:rgba(255,255,255,.25);white-space:nowrap}
.card .del{position:absolute;top:6px;right:6px;width:28px;height:28px;border-radius:6px;background:rgba(0,0,0,.5);border:none;color:#fff;font-size:14px;cursor:pointer;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
.card:hover .del{display:flex}
.card .del:hover{background:rgba(240,60,108,.6)}
.file-i{padding:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;width:100%;aspect-ratio:1;background:rgba(255,255,255,.02);gap:8px}
.file-i .fi{width:32px;height:40px;border:2px solid rgba(255,255,255,.1);border-radius:4px;position:relative}
.file-i .fi::after{content:'';position:absolute;top:-1px;right:-1px;border:6px solid rgba(255,255,255,.08);border-bottom-color:transparent;border-left-color:transparent}
.file-i .fi-ext{font-size:8px;color:rgba(255,255,255,.2);text-transform:uppercase;letter-spacing:1px}
.file-i .fi-nm{font-size:10px;color:rgba(255,255,255,.3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;text-align:center}

@keyframes cardIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

/* List view */
.lview{display:none}
.lview.on{display:block}
.lrow{display:grid;grid-template-columns:auto 1fr auto auto auto;gap:10px;align-items:center;padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.03);transition:all .2s;font-size:13px}
.lrow:hover{background:rgba(255,255,255,.02)}
.lrow .icon{width:24px;height:24px;border-radius:4px;background:rgba(255,255,255,.03);display:flex;align-items:center;justify-content:center}
.lrow .icon.fi{font-size:16px}
.lrow .name{color:rgba(255,255,255,.7);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.lrow .size{color:rgba(255,255,255,.25);font-size:11px}
.lrow .date{color:rgba(255,255,255,.2);font-size:11px}
.lrow .lact{display:flex;gap:6px}
.lrow .lact button{padding:4px 8px;border-radius:4px;border:none;font-size:10px;cursor:pointer;font-family:inherit;transition:all .2s}
.lrow .lact .dlb{background:rgba(108,60,240,.15);color:#6c3cf0}
.lrow .lact .dlb:hover{background:rgba(108,60,240,.25)}
.lrow .lact .ddel{background:rgba(240,60,108,.12);color:#f03c6c}
.lrow .lact .ddel:hover{background:rgba(240,60,108,.2)}

/* Empty */
.empty{text-align:center;padding:60px 20px;color:rgba(255,255,255,.2)}
.empty .e-icon{width:48px;height:48px;margin:0 auto 12px;border-radius:12px;background:rgba(255,255,255,.03);display:flex;align-items:center;justify-content:center}
.empty .e-icon::after{content:'';width:20px;height:20px;border:2px solid rgba(255,255,255,.08);border-radius:4px}
.empty p{font-size:13px;margin-bottom:4px}
.empty .sub{font-size:10px}

.toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);padding:10px 20px;border-radius:8px;background:rgba(0,0,0,.7);backdrop-filter:blur(10px);color:#fff;font-size:12px;z-index:999;display:none;border:1px solid rgba(255,255,255,.06)}

.modal{position:fixed;top:0;left:0;width:100%;height:100%;z-index:100;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.5);backdrop-filter:blur(4px)}
.modal.on{display:flex}
.modal-c{max-width:90vw;max-height:90vh;border-radius:12px;overflow:hidden;position:relative}
.modal-c img,.modal-c video{max-width:90vw;max-height:90vh;display:block}
.modal-c .mclose{position:absolute;top:8px;right:8px;width:32px;height:32px;border-radius:8px;background:rgba(0,0,0,.5);border:none;color:#fff;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center}

@media(max-width:600px){
  .head{padding:14px 16px}
  .main{padding:14px 16px}
  .grids{grid-template-columns:repeat(auto-fill,minmax(130px,1fr))}
}
</style>
</head>
<body>

<div class="bg"><div class="bg-orbs">
<span></span><span></span><span></span>
</div></div>
<div class="bg-grid"></div>

<div class="head">
<div class="head-l">
<div class="hl"></div>
<div><h1>MyVault</h1></div>
</div>
<div class="head-r">
<span class="h-info" id="cntInfo">0 files</span>
<button class="h-btn" id="toggleView">List</button>
<button class="h-btn prim" id="dlAll">Download All</button>
</div>
</div>

<div class="main">
<div class="uzone glass" id="uzone">
<div class="uz-icon"></div>
<p>Drop files here or click to upload</p>
<div class="sub">Photos, Videos, Documents — Max 50MB</div>
<div class="pbar" id="pbar"><div class="pbar-in" id="pbarIn"></div></div>
<input type="file" id="fileInput" multiple>
</div>

<div class="fbar">
<span class="tag act" data-filter="all">All</span>
<span class="tag" data-filter="image">Photos</span>
<span class="tag" data-filter="video">Videos</span>
<span class="tag" data-filter="application">Documents</span>
<span class="tag" data-filter="other">Other</span>
<span class="count" id="fileCount">0 files</span>
</div>

<div class="grids" id="gridV"></div>
<div class="lview on" id="listV"></div>

<div class="empty" id="empty">
<div class="e-icon"></div>
<p>No files yet</p>
<div class="sub">Upload your first file to get started</div>
</div>
</div>

<div class="modal" id="modal"><div class="modal-c glass">
<button class="mclose">&times;</button>
<img id="modalImg" style="display:none">
<video id="modalVid" controls style="display:none"></video>
</div></div>

<div class="toast" id="toast"></div>

<script>
const PWD = '<?=PASSWORD?>';
const BASE = '?pwd=' + PWD;
let files = [], filter = 'all', viewMode = 'grid';

function sz(v) {
    if (!v) return '0 B';
    const u = ['B','KB','MB','GB'], i = Math.floor(Math.log(v)/Math.log(1024));
    return (v/Math.pow(1024,i)).toFixed(i>0?1:0) + ' ' + u[i];
}

function extCls(t) {
    if (!t) return '?';
    if (t.startsWith('image/')) return 'img';
    if (t.startsWith('video/')) return 'vid';
    const m = t.split('/');
    return m[1] || 'file';
}

function isImg(t) { return t && t.startsWith('image/'); }
function isVid(t) { return t && t.startsWith('video/'); }

function filterFiles() {
    return files.filter(f => {
        if (filter === 'all') return true;
        if (filter === 'other') return !isImg(f.type) && !isVid(f.type) && !f.type.startsWith('application/');
        return f.type && f.type.startsWith(filter);
    });
}

function render() {
    const ff = filterFiles();
    const g = document.getElementById('gridV');
    const l = document.getElementById('listV');
    const e = document.getElementById('empty');
    const c = document.getElementById('fileCount');
    const ci = document.getElementById('cntInfo');
    
    c.textContent = ff.length + ' file' + (ff.length!==1?'s':'');
    ci.textContent = files.length + ' file' + (files.length!==1?'s':'');
    
    if (ff.length === 0) {
        g.innerHTML = ''; l.innerHTML = ''; l.classList.remove('on');
        e.style.display = 'block';
        return;
    }
    e.style.display = 'none';
    
    // Grid
    g.innerHTML = ff.map(f => {
        let inner;
        if (isImg(f.type)) {
            inner = `<img class="thumb" src="${BASE}&action=img&fid=${f.fid}" loading="lazy">`;
        } else if (isVid(f.type)) {
            inner = `<div class="vid"></div>`;
        } else {
            const ext = (f.name.split('.').pop() || '?').toUpperCase();
            inner = `<div class="file-i"><div class="fi"></div><div class="fi-ext">${ext}</div><div class="fi-nm">${f.name}</div></div>`;
        }
        return `<div class="card" data-type="${f.type||''}" data-fid="${f.fid}">
            ${inner}
            <div class="info">
                <div class="nm">${f.name}</div>
                <div class="sz">${sz(f.size)}</div>
            </div>
            <button class="del" onclick="delFile('${f.fid}')">&times;</button>
        </div>`;
    }).join('');
    
    // List
    l.innerHTML = ff.map(f => {
        const ic = isImg(f.type) ? '&#9632;' : isVid(f.type) ? '&#9654;' : '&#9632;';
        return `<div class="lrow">
            <div class="icon fi">${ic}</div>
            <div class="name">${f.name}</div>
            <div class="size">${sz(f.size)}</div>
            <div class="date">${(f.uploaded||'').split(' ')[0]}</div>
            <div class="lact">
                <button class="dlb" onclick="dlFile('${f.fid}')">DL</button>
                <button class="ddel" onclick="delFile('${f.fid}')">Del</button>
            </div>
        </div>`;
    }).join('');
    
    // Click handlers for cards
    document.querySelectorAll('.card').forEach(c => {
        c.addEventListener('click', function(e) {
            if (e.target.closest('.del')) return;
            const fid = this.dataset.fid;
            const f = files.find(x => x.fid === fid);
            if (!f) return;
            if (isImg(f.type)) {
                showModal('<img src="' + BASE + '&action=img&fid=' + fid + '">');
            } else if (isVid(f.type)) {
                showModal('<video src="' + BASE + '&action=img&fid=' + fid + '" controls autoplay></video>');
            } else {
                dlFile(fid);
            }
        });
    });
}

function showModal(html) {
    const m = document.getElementById('modal');
    document.querySelector('.modal-c').innerHTML = '<button class="mclose">&times;</button>' + html;
    m.classList.add('on');
    m.querySelector('.mclose').onclick = () => m.classList.remove('on');
    m.onclick = (e) => { if (e.target === m) m.classList.remove('on'); };
}

function loadFiles() {
    fetch(BASE + '&action=list')
        .then(r => r.json())
        .then(d => { files = d; render(); })
        .catch(() => toast('Failed to load files'));
}

function delFile(fid) {
    if (!confirm('Delete this file?')) return;
    fetch(BASE + '&action=delete&fid=' + fid)
        .then(r => r.json())
        .then(d => {
            if (d.ok) { loadFiles(); toast('File deleted'); }
            else toast('Delete failed');
        })
        .catch(() => toast('Delete failed'));
}

function dlFile(fid) { window.location.href = BASE + '&action=dl&fid=' + fid; }

function toast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.style.display = 'block';
    clearTimeout(t._t); t._t = setTimeout(() => t.style.display = 'none', 2500);
}

// Upload
const uz = document.getElementById('uzone');
const fi = document.getElementById('fileInput');
uz.onclick = () => fi.click();
uz.ondragover = e => { e.preventDefault(); uz.classList.add('dragover'); };
uz.ondragleave = () => uz.classList.remove('dragover');
uz.ondrop = e => { e.preventDefault(); uz.classList.remove('dragover'); uploadFiles(e.dataTransfer.files); };
fi.onchange = () => { uploadFiles(fi.files); fi.value = ''; };

function uploadFiles(files) {
    const pbar = document.getElementById('pbar');
    const pbarIn = document.getElementById('pbarIn');
    pbar.style.display = 'block';
    let done = 0, total = files.length, errs = [];
    
    function uploadOne(i) {
        if (i >= total) {
            pbar.style.display = 'none';
            if (errs.length) toast(errs.length + ' upload(s) failed');
            else toast('All uploaded');
            loadFiles();
            return;
        }
        const fd = new FormData();
        fd.append('file', files[i]);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', BASE + '&action=upload', true);
        xhr.upload.onprogress = e => {
            if (e.lengthComputable) {
                const pct = (done + e.loaded / e.total) / total * 100;
                pbarIn.style.width = pct + '%';
            }
        };
        xhr.onload = () => {
            done++;
            pbarIn.style.width = (done / total * 100) + '%';
            try { const r = JSON.parse(xhr.responseText); if (!r.ok) errs.push(files[i].name); }
            catch(e) { errs.push(files[i].name); }
            uploadOne(i + 1);
        };
        xhr.onerror = () => { done++; errs.push(files[i].name); uploadOne(i + 1); };
        xhr.send(fd);
    }
    uploadOne(0);
}

// Toggle view
document.getElementById('toggleView').onclick = function() {
    const g = document.getElementById('gridV');
    const l = document.getElementById('listV');
    viewMode = viewMode === 'grid' ? 'list' : 'grid';
    this.textContent = viewMode === 'grid' ? 'List' : 'Grid';
    g.style.display = viewMode === 'grid' ? 'grid' : 'none';
    l.classList.toggle('on', viewMode === 'list');
};

// Download all
document.getElementById('dlAll').onclick = () => {
    if (files.length === 0) return toast('No files to download');
    window.location.href = BASE + '&action=dlall';
};

// Filters
document.querySelectorAll('.tag').forEach(t => {
    t.onclick = function() {
        document.querySelectorAll('.tag').forEach(x => x.classList.remove('act'));
        this.classList.add('act');
        filter = this.dataset.filter;
        render();
    };
});

// Init
loadFiles();

// Close modal on escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('modal').classList.remove('on');
});
</script>
</body>
</html>
