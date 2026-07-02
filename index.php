<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Redirecting...</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;background:#0a0a0f;min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden}
.bg{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0}
.bg span{position:absolute;border-radius:50%;animation:float 6s infinite ease-in-out}
.bg span:nth-child(1){width:400px;height:400px;background:radial-gradient(circle,rgba(240,30,50,.12),transparent);top:-15%;left:-10%;animation-delay:0s}
.bg span:nth-child(2){width:350px;height:350px;background:radial-gradient(circle,rgba(200,20,40,.1),transparent);bottom:-20%;right:-10%;animation-delay:-2s}
.bg span:nth-child(3){width:250px;height:250px;background:radial-gradient(circle,rgba(255,50,70,.08),transparent);top:50%;left:50%;animation-delay:-4s}
@keyframes float{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(40px,-30px) scale(1.05)}66%{transform:translate(-20px,40px) scale(.95)}}
.card{position:relative;z-index:1;text-align:center;padding:60px 80px;background:rgba(255,255,255,.02);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.04);border-radius:24px;animation:fadeIn .8s ease-out}
@keyframes fadeIn{from{opacity:0;transform:scale(.9) translateY(20px)}to{opacity:1;transform:scale(1) translateY(0)}}
.emoji{font-size:80px;margin-bottom:20px;animation:bounce 1.5s infinite}
@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-15px)}}
h1{font-size:48px;font-weight:900;background:linear-gradient(135deg,#f03c3c,#ff2060);-webkit-background-clip:text;-webkit-text-fill-color:transparent;letter-spacing:-1px;margin-bottom:8px}
.sub{font-size:16px;color:rgba(255,255,255,.3);letter-spacing:2px;text-transform:uppercase;margin-bottom:30px}
.progress{width:200px;height:3px;background:rgba(255,255,255,.05);border-radius:4px;margin:0 auto;overflow:hidden}
.progress .bar{height:100%;width:0;background:linear-gradient(90deg,#f03c3c,#ff2060);border-radius:4px;animation:fill 3s linear forwards}
@keyframes fill{to{width:100%}}
.redirect-text{margin-top:20px;font-size:12px;color:rgba(255,255,255,.15)}
.countdown{font-size:14px;color:rgba(255,255,255,.25);margin-top:8px;font-variant-numeric:tabular-nums}
</style>
</head>
<body>
<div class="bg"><span></span><span></span><span></span></div>
<div class="card">
  <div class="emoji">🖕</div>
  <h1>Fuck you Nikal Ja</h1>
  <div class="sub">Bhad Me Jao</div>
  <div class="progress"><div class="bar"></div></div>
  <div class="redirect-text">Redirecting to Telegram...</div>
  <div class="countdown" id="countdown">3</div>
</div>
<script>
var sec=3;
setInterval(function(){
  sec--;
  document.getElementById('countdown').textContent=sec;
  if(sec<=0) window.location.href='https://t.me/ALXADM1';
},1000);
setTimeout(function(){
  window.location.href='https://t.me/ALXADM1';
},3000);
</script>
</body>
</html>
