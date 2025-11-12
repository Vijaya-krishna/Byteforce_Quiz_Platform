<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../index.html"); exit; }
require_once __DIR__ . '/DB.php';
$db = (new DB())->conn();
$uid = intval($_SESSION['user_id']);
$u = $db->query("SELECT username, suspended FROM users WHERE id = $uid")->fetch_assoc();
if (!$u) { header("Location: ../index.html"); exit; }
if (intval($u['suspended']) === 1) { header("Location: ../index.html?status=suspended"); exit; }
$username = htmlspecialchars($u['username'] ?? 'Player');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Quiz — BYTE FORCE Portal</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
<style>
:root{--bg1:#0f0c29;--bg2:#22183a;--card:#0f1724;--muted:rgba(255,255,255,0.65);--accent:#00eaff;--accent2:#7b61ff;--danger:#ff4d6d}
*{box-sizing:border-box}
html,body{height:100%;margin:0;font-family:'Poppins',sans-serif;background:linear-gradient(180deg,var(--bg2),#0f0c29);color:#e8fbff}
.container{max-width:1200px;margin:24px auto;padding:16px}
.header{display:flex;justify-content:space-between;align-items:center;gap:16px}
.greet{background:linear-gradient(90deg,#171026,#2a153a);padding:18px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.5)}
.greet h1{margin:0;font-size:22px}
.greet p{margin:6px 0 0;color:var(--muted);font-size:13px}
.main{display:grid;grid-template-columns:1fr 320px;gap:18px;margin-top:18px;align-items:start}
.card{background:linear-gradient(180deg,rgba(255,255,255,0.02),rgba(255,255,255,0.01));padding:22px;border-radius:14px;box-shadow:0 12px 48px rgba(0,0,0,0.6);border:1px solid rgba(255,255,255,0.02)}
.question-card{min-height:260px;padding:26px;border-radius:12px;position:relative}
.qtitle{font-weight:800;font-size:18px;text-align:center;margin:0 0 18px}
.opts{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.opt{padding:16px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.03);cursor:pointer;font-weight:700;transition:all .18s;min-height:54px;display:flex;align-items:center}
.opt:hover{transform:translateY(-6px);box-shadow:0 14px 30px rgba(0,0,0,0.45)}
.opt.correct{background:linear-gradient(90deg,#0d3b2f,#0b4b38);color:#eafff3}
.opt.wrong{background:linear-gradient(90deg,#3a0b0b,#4c0000);color:#fff}
.right-panel{display:flex;flex-direction:column;gap:14px}
.score-box{padding:16px;border-radius:12px;text-align:center}
.score-num{font-size:32px;font-weight:900;color:var(--accent)}
.timer-wrap{display:flex;flex-direction:column;align-items:center;padding:14px;border-radius:12px}
.circular-timer{width:140px;height:140px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:8px solid rgba(255,255,255,0.06);position:relative}
.circular-timer .time{font-size:28px;font-weight:800;color:var(--accent)}
.progress-bar{width:100%;height:10px;border-radius:10px;background:rgba(255,255,255,0.03);overflow:hidden;margin-top:14px}
.progress-bar>i{display:block;height:100%;width:0%;background:linear-gradient(90deg,var(--accent),var(--accent2));transition:width 0.3s linear}
.webcam-preview{position:fixed;right:18px;bottom:18px;width:180px;height:135px;border-radius:12px;overflow:hidden;border:3px solid rgba(0,234,255,0.18);box-shadow:0 8px 30px rgba(0,234,255,0.06);background:#010112;z-index:999}
.webcam-preview video{width:100%;height:100%;object-fit:cover;transform:scaleX(-1)}
.controls{display:flex;gap:12px;justify-content:flex-end;margin-top:12px}
.btn{padding:10px 16px;border-radius:10px;border:none;cursor:pointer;font-weight:800;background:linear-gradient(90deg,var(--accent),var(--accent2));color:#02131d}
.secondary{background:transparent;color:var(--accent);border:1px solid rgba(255,255,255,0.04)}
.warning-banner{position:fixed;left:50%;transform:translateX(-50%);bottom:18px;padding:12px 20px;border-radius:12px;background:linear-gradient(90deg,#7b2d2d,#5a1a1a);color:#fff;font-weight:800;display:none;z-index:2000;box-shadow:0 14px 40px rgba(0,0,0,0.6)}
.status-pill{display:inline-flex;align-items:center;gap:10px;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,0.03);font-weight:700}
.status-dot{width:12px;height:12px;border-radius:50%;box-shadow:0 0 10px rgba(0,0,0,0.6)}
.muted{color:var(--muted);font-size:13px}
#toast{position:fixed;bottom:40px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.6);padding:12px 22px;border-radius:10px;color:#fff;font-weight:600;display:none;z-index:3000}
.badge{display:inline-block;padding:6px 10px;border-radius:999px;background:rgba(0,234,255,0.1);border:1px solid rgba(0,234,255,0.2);font-weight:700;color:#88f5ff}
@media(max-width:980px){.main{grid-template-columns:1fr}.webcam-preview{width:140px;height:105px}}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="left greet">
      <h1>Hello, <?php echo $username; ?></h1>
      <p class="muted">Stay focused — webcam monitoring active</p>
    </div>
    <div class="right greet" style="display:flex;align-items:center;gap:18px">
      <div class="status-pill">
        <div class="status-dot" id="statusDot" style="background:#f0a500"></div>
        <div id="webcamStatusText" class="muted">Initializing camera…</div>
      </div>
      <div style="min-width:110px;text-align:right">
        <div class="muted">Score</div>
        <div class="score-num" id="scoreNum">0</div>
        <div class="muted" style="margin-top:4px">Fouls: <span class="badge" id="foulBadge">0/5</span></div>
      </div>
    </div>
  </div>

  <div class="main">
    <div class="card question-card" id="questionCard">
      <div class="qtitle" id="qTitle">Loading quiz and models…</div>
      <div id="questionArea" style="opacity:0.001;transition:opacity .6s">
        <div class="opts" id="optsContainer"></div>
        <div style="margin-top:18px"><div class="progress-bar" aria-hidden="true"><i id="progressBarFill"></i></div></div>
      </div>
      <div style="margin-top:18px;display:flex;justify-content:space-between;align-items:center">
        <div class="muted">Question <span id="qIndex">0</span> of <span id="qTotal">0</span></div>
        <div class="muted" id="timerSmall">—</div>
      </div>
    </div>

    <div class="right-panel">
      <div class="card score-box">
        <div class="muted">Score</div>
        <div class="score-num" id="scoreLarge">0</div>
      </div>

      <div class="card timer-wrap">
        <div class="muted" style="margin-bottom:6px;text-align:center">Question Timer</div>
        <div class="circular-timer" id="circle"><div class="time" id="circleTime">45</div></div>
        <div style="width:100%;margin-top:12px">
          <div class="muted" style="text-align:center">Progress</div>
          <div class="progress-bar" style="margin-top:8px"><i id="progFill"></i></div>
        </div>
      </div>

      <div class="card">
        <div class="muted">Quick Links</div>
        <div style="margin-top:12px;display:flex;gap:10px;justify-content:flex-end">
          <button class="btn secondary" onclick="location.href='leaderboard.php'">Leaderboard</button>
          <button class="btn secondary" onclick="location.href='logout.php'">Logout</button>
        </div>
      </div>
    </div>
  </div>

  <div class="controls">
    <button class="btn" id="quitBtn" style="background:transparent;color:var(--accent);border:1px solid rgba(255,255,255,0.06)">Quit</button>
    <button class="btn" id="nextBtn" style="display:none">Next</button>
  </div>
</div>

<div class="webcam-preview" id="webcamPreview" title="Webcam preview">
  <video id="preview" autoplay muted playsinline></video>
</div>

<div class="warning-banner" id="warningBanner">Warning</div>
<div id="toast"></div>

<script src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
class UI{
  constructor(){this.banner=g('warningBanner');this.toast=g('toast');this.dot=g('statusDot');this.status=g('webcamStatusText');this.foul=g('foulBadge')}
  bannerShow(t){this.banner.textContent=t;this.banner.style.display='block';clearTimeout(this.bt);this.bt=setTimeout(()=>this.banner.style.display='none',2000)}
  toastShow(t){this.toast.textContent=t;this.toast.style.display='block';clearTimeout(this.tt);this.tt=setTimeout(()=>this.toast.style.display='none',1600)}
  setStatus(c,t){this.dot.style.background=c;this.status.textContent=t}
  setFoul(n){this.foul.textContent=n+'/5';this.foul.style.borderColor=n>=5?'var(--danger)':'rgba(0,234,255,0.2)';this.foul.style.color=n>=5?'#ffd9df':'#88f5ff'}
}
class Quiz{
  constructor(){this.qs=[];this.i=0;this.score=0;this.timer=null;this.left=45;this.base=10;this.bF=5;this.bM=3;this.bS=1;this.start=0;
    this.qTitle=g('qTitle');this.opts=g('optsContainer');this.qIdx=g('qIndex');this.qTot=g('qTotal');this.circleTime=g('circleTime');this.tSmall=g('timerSmall');this.pFill=g('progFill');this.pFill2=g('progressBarFill');this.sNum=g('scoreNum');this.sLg=g('scoreLarge');this.area=g('questionArea')}
  async load(){const r=await fetch('fetch_questions.php',{cache:'no-store'});const j=await r.json();if(j.error){this.qTitle.textContent=j.error;return}
    this.qs=j;this.qTot.textContent=this.qs.length;this.i=0;this.render();this.area.style.opacity=1}
  render(){if(this.i>=this.qs.length){this.finish();return}const q=this.qs[this.i];
    this.qIdx.textContent=this.i+1;this.qTitle.textContent=(this.i+1)+'. '+q.question;
    this.opts.innerHTML=q.options.map((o,i)=>`<div class="opt" data-i="${i}">${o}</div>`).join('');
    [...this.opts.children].forEach(c=>c.addEventListener('click',e=>this.answer(e)));
    this.updateP();this.resetTimer();this.start=Date.now()}
  answer(e){const idx=parseInt(e.currentTarget.getAttribute('data-i'),10), q=this.qs[this.i];
    const correct=q.options.findIndex(o=>o===q.answer), opts=[...this.opts.children];
    opts.forEach(o=>o.style.pointerEvents='none');clearInterval(this.timer);
    if(idx===correct){const b=this.bonus(), pts=this.base+b;this.score+=pts;this.sNum.textContent=this.score;this.sLg.textContent=this.score;opts[idx].classList.add('correct');ui.toastShow('✅ Correct +'+pts+(b?` (+${b})`:''))}
    else{opts[idx].classList.add('wrong');if(correct>=0)opts[correct].classList.add('correct');ui.toastShow('❌ Wrong')}
    setTimeout(()=>{this.i++;this.render()},800)}
  bonus(){const s=(Date.now()-this.start)/1000; if(s<=3)return this.bF; if(s<=6)return this.bM; if(s<=10)return this.bS; return 0}
  updateP(){const p=this.qs.length?((this.i)/(this.qs.length))*100:0; this.pFill.style.width=p+'%'; this.pFill2.style.width=p+'%'}
  resetTimer(){clearInterval(this.timer);this.left=45;this.circleTime.textContent=this.left;this.tSmall.textContent=this.left+'s';
    this.timer=setInterval(()=>{this.left--;this.circleTime.textContent=this.left;this.tSmall.textContent=this.left+'s';
      const used=((45-this.left)/45)*100;this.pFill.style.width=used+'%';
      if(this.left<=0){clearInterval(this.timer);this.i++;this.render()}},1000)}
  finish(){proctor.stop();this.qTitle.textContent='Quiz Complete';
    g('questionArea').innerHTML=`<div style="text-align:center;padding:40px;">
      <div style="font-size:40px;font-weight:900;color:var(--accent)">${this.score} pts</div>
      <p class="muted">Result recorded. Great work!</p>
      <div style="margin-top:16px">
        <button class="btn" onclick="location.href='leaderboard.php'">Leaderboard</button>
        <button class="btn secondary" onclick="location.href='logout.php'">Logout</button>
      </div></div>`;
    fetch('submit_answers.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({score:this.score,total:this.qs.length})}).catch(()=>{})}
}
class Proctor{
  constructor(){
    this.video=g('preview'); this.stream=null; this.ready=false; this.started=0; this.interval=null;
    this.warns=0; this.cool=false; this.lastPost=0;
    this.inputSize=224; this.scoreThr=0.5; this.minBoxArea=0.12;
    this.yawThr=25; this.pitchThr=18;
    this.missStreak=0; this.lookStreak=0; this.reqMiss=2; this.reqLook=2;
    this.step=350; this.grace=1800; this.modelsLoaded=false;
  }
  async init(){
    ui.setStatus('#f0a500','Requesting camera…');
    this.stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'user'},audio:false});
    this.video.srcObject=this.stream; await this.video.play();
    ui.setStatus('#00ffb3','Webcam ready'); this.started=Date.now();
    await this.loadModels(); this.modelsLoaded=true;
    ui.setStatus('#00ffb3','Proctoring active');
    document.addEventListener('visibilitychange',()=>{ if(document.hidden) this.raise('Tab switched') });
    this.loop();
  }
  async loadModels(){
    const local='/models';
    const cdn='https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';
    try{
      await faceapi.nets.tinyFaceDetector.loadFromUri(local);
      await faceapi.nets.faceLandmark68TinyNet.loadFromUri(local);
    }catch(e){
      await faceapi.nets.tinyFaceDetector.loadFromUri(cdn);
      await faceapi.nets.faceLandmark68TinyNet.loadFromUri(cdn);
    }
  }
  loop(){
    this.interval=setInterval(async()=>{
      if(!this.modelsLoaded) return;
      if(Date.now()-this.started < this.grace) return;

      const det = await faceapi
        .detectSingleFace(this.video,new faceapi.TinyFaceDetectorOptions({inputSize:this.inputSize,scoreThreshold:this.scoreThr}))
        .withFaceLandmarks(true);

      if(!det){
        this.missStreak++;
        if(this.missStreak>=this.reqMiss){ this.missStreak=0; this.raise('Face not visible') }
        return;
      }
      this.missStreak=0;

      const area=(det.detection.box.width*det.detection.box.height)/(this.video.videoWidth*this.video.videoHeight);
      if(area < this.minBoxArea){ this.raise('Too far from camera'); return }

      const lm=det.landmarks;
      const yaw=this.yaw(lm), pitch=this.pitch(lm);
      if(Math.abs(yaw)>this.yawThr || Math.abs(pitch)>this.pitchThr){
        this.lookStreak++; if(this.lookStreak>=this.reqLook){ this.lookStreak=0; this.raise('Look at the screen') }
      } else { this.lookStreak=0 }
    }, this.step);
  }
  yaw(l){
    const le=l.getLeftEye()[0], re=l.getRightEye()[3], n=l.getNose()[3];
    const cx=(le.x+re.x)/2, dx=n.x-cx, eyeW=Math.max(1,re.x-le.x);
    return (dx/eyeW)*90;
  }
  pitch(l){
    const n=l.getNose()[3], ch=l.getMouth()[3], br=l.getJawOutline()[8];
    const dy=(ch.y-n.y)/(Math.max(1, br.y-n.y));
    return (dy-0.42)*120;
  }
  raise(msg){
    if(this.cool) return;
    this.warns++; ui.setFoul(this.warns);
    ui.bannerShow('Warning '+this.warns+'/5 — '+msg);
    this.cool=true; setTimeout(()=>this.cool=false,1500);

    const now=Date.now(); if(now-this.lastPost<1500) return; this.lastPost=now;
    fetch('foul.php',{method:'POST'}).then(r=>r.json()).then(j=>{
      if(j.status==='suspended'){
        ui.bannerShow('Account suspended'); setTimeout(()=>location.href='logout.php',1100);
      }else if(j.status==='warning'){
        ui.setFoul(j.count);
      }
    }).catch(()=>{});
  }
  stop(){
    if(this.interval) clearInterval(this.interval);
    if(this.stream){ this.stream.getTracks().forEach(t=>t.stop()); this.stream=null }
  }
}
function g(id){return document.getElementById(id)}
const ui=new UI(), quiz=new Quiz(), proctor=new Proctor();

g('quitBtn').addEventListener('click',()=>{ if(confirm('Quit the quiz?')){ proctor.stop(); location.href='logout.php' } });

(async()=>{
  try{ await proctor.init() }catch(e){ ui.setStatus('#ff5b5b','Unable to access webcam'); ui.bannerShow('Camera permission required') }
  await quiz.load();
})();
window.addEventListener('beforeunload',()=>{ try{ proctor.stop() }catch(e){} });
</script>
</body>
</html>
