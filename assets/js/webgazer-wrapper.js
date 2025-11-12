class EyeTracker {
        // inside EyeTracker class
    constructor() {
        // existing ...
        this.lastFoulSentAt = 0;
        this.foulCooldownMs = 12_000; // only send a foul once every 12s
    }

    sendFoulToServer() {
        const now = Date.now();
        if (now - this.lastFoulSentAt < this.foulCooldownMs) return; // throttle
        this.lastFoulSentAt = now;

        fetch("foul.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ user_id: this.userId })
        })
        .then(res => res.json())
        .then(data => {
    if (data.status === "suspended") {
        alert("🚫 You have been suspended due to repeated focus violations.");
        window.location.href = "logout.php";
    } else if (data.status === "warning") {
        showFoulWarning(data.count, 5);
    } else if (data.status === "ok" && data.count) {
        // Even when throttled, you can remind quietly
        showFoulWarning(data.count, 5);
    }
})
.catch(e => console.error("Foul send error:", e));
    }


    start() {
        webgazer.setRegression('ridge')
            .setGazeListener((data) => this.handleGaze(data))
            .begin();

        setTimeout(() => {
            webgazer.showVideoPreview(false)
            webgazer.showPredictionPoints(false)
        }, 2000);
    }

    handleGaze(data) {
        if (!data || this.isWarningActive) return;

        const x = data.x;
        const y = data.y;

        if (x < this.safeZonePadding || 
            x > window.innerWidth - this.safeZonePadding ||
            y < this.safeZonePadding || 
            y > window.innerHeight - this.safeZonePadding) 
        {
            this.triggerWarning();
        }
    }

    triggerWarning() {
        this.warnings++;
        this.isWarningActive = true;

        this.showWarningOverlay();
        this.sendFoulToServer();

        if (this.warnings >= 5) {
            window.location.href = "logout.php";
        }
    }

 

    showWarningOverlay() {
        let overlay = document.createElement("div");
        overlay.id = "warningOverlay";
        overlay.style.position = "fixed";
        overlay.style.top = "0";
        overlay.style.left = "0";
        overlay.style.width = "100vw";
        overlay.style.height = "100vh";
        overlay.style.background = "rgba(255,0,0,0.93)";
        overlay.style.display = "flex";
        overlay.style.flexDirection = "column";
        overlay.style.justifyContent = "center";
        overlay.style.alignItems = "center";
        overlay.style.color = "white";
        overlay.style.zIndex = "999999";

        let title = document.createElement("div");
        title.style.fontSize = "48px";
        title.style.fontWeight = "700";
        title.innerHTML = "⚠ WARNING!";

        let msg = document.createElement("div");
        msg.innerHTML = "Look at the screen or your account will be suspended!";
        msg.style.margin = "20px 0";

        let timer = document.createElement("div");
        timer.id = "countdown";
        timer.style.fontSize = "40px";
        timer.style.fontWeight = "bold";
        timer.style.marginBottom = "25px";

        let okBtn = document.createElement("button");
        okBtn.innerHTML = "OK";
        okBtn.style.padding = "12px 25px";
        okBtn.style.fontSize = "20px";
        okBtn.style.background = "#0059ff";
        okBtn.style.color = "white";
        okBtn.style.border = "none";
        okBtn.style.borderRadius = "10px";
        okBtn.style.cursor = "pointer";

        okBtn.onclick = () => {
            document.body.removeChild(overlay);
            clearInterval(this.countdownInterval);
            this.isWarningActive = false;
        };

        overlay.appendChild(title);
        overlay.appendChild(msg);
        overlay.appendChild(timer);
        overlay.appendChild(okBtn);

        document.body.appendChild(overlay);

        this.startCountdown();
    }

    startCountdown() {
        let timeLeft = 10;
        const timer = document.getElementById("countdown");
        timer.innerHTML = timeLeft;

        this.countdownInterval = setInterval(() => {
            timeLeft--;
            timer.innerHTML = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(this.countdownInterval);
                window.location.href = "logout.php";
            }
        }, 1000);
    }
}

// Global instance
let eyeTracker = new EyeTracker();

function startBrowserTracker() {
    eyeTracker.start();
}

// Visual warning system
function showFoulWarning(count, threshold = 5) {
  const overlay = document.getElementById("foulOverlay");
  const toast = document.getElementById("foulToast");

  // Show overlay flash
  overlay.style.display = "block";
  overlay.style.animation = "none"; // reset animation
  void overlay.offsetWidth; // reflow trick
  overlay.style.animation = "flashWarning 0.9s ease";
  setTimeout(() => (overlay.style.display = "none"), 900);

  // Show toast message
  toast.innerHTML = `⚠️ Warning ${count}/${threshold} — Stay focused on screen!`;
  toast.style.display = "block";
  toast.style.opacity = 1;
  setTimeout(() => {
    toast.style.transition = "opacity 0.8s";
    toast.style.opacity = 0;
    setTimeout(() => (toast.style.display = "none"), 800);
  }, 2500);
}

