// quiz_app.js — Final version for Neon Tech (no network errors)

async function fetchQuestions() {
  try {
    const res = await fetch('get_questions.php');
    if (!res.ok) throw new Error('Failed to fetch questions');
    return await res.json();
  } catch (err) {
    console.error("Error fetching questions:", err);
    const stage = document.getElementById('stage');
    stage.innerHTML = `<div style="padding:28px;text-align:center;color:red;">Failed to load questions. Please refresh.</div>`;
    return [];
  }
}

function renderQuestion(q, index, total) {
  return `
    <div class="question-card">
      <div class="qtext">${index + 1}. ${q.question}</div>
      <div class="opts">
        ${['A','B','C','D'].map(opt => `
          <div class="opt" data-q="${q.id}" data-value="${opt}">
            ${opt}. ${q['option_' + opt.toLowerCase()]}
          </div>
        `).join('')}
      </div>
      <div class="hint">Question ${index + 1} of ${total}</div>
    </div>
  `;
}

document.addEventListener('DOMContentLoaded', async () => {
  const stage = document.getElementById('stage');
  const scoreNum = document.getElementById('scoreNum');
  const progressList = document.getElementById('progressList');
  const quitBtn = document.getElementById('quitBtn');
  const username = document.querySelector('.user').textContent.replace('Hello, ', '');

  const questions = await fetchQuestions();
  if (!questions.length) return;

  let score = 0;
  let current = 0;
  const total = questions.length;
  const answers = {};

  function renderProgress() {
    progressList.innerHTML = '';
    for (let i = 0; i < total; i++) {
      const pill = document.createElement('div');
      pill.className = 'pill';
      pill.style.opacity = i < current ? 1 : 0.3;
      pill.innerText = i + 1;
      progressList.appendChild(pill);
    }
  }

  function loadQuestion() {
    stage.innerHTML = renderQuestion(questions[current], current, total);
    renderProgress();

    document.querySelectorAll('.opt').forEach(opt => {
      opt.addEventListener('click', () => {
        const qid = opt.dataset.q;
        const selected = opt.dataset.value;
        answers[qid] = selected;

        // move next after 0.6s
        opt.classList.add('correct');
        setTimeout(() => {
          current++;
          if (current < total) loadQuestion();
          else finishQuiz();
        }, 600);
      });
    });
  }

  function finishQuiz() {
    stage.innerHTML = `
      <div class="result-screen">
        <div style="font-size:26px;font-weight:700">Quiz Complete</div>
        <div class="big-score">Saving result...</div>
        <p style="font-size:14px;color:rgba(255,255,255,0.7)">Please wait a moment...</p>
      </div>
    `;

    // Create hidden form for result submission
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'submit_answers.php';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'json';
    input.value = JSON.stringify({ answers });

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }

  quitBtn.addEventListener('click', () => {
    window.location.href = 'logout.php';
  });

  // start
  loadQuestion();
});
