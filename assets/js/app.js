// App.js — final, form-based submit with debug logs

async function fetchQuestions() {
  try {
    const res = await fetch('get_questions.php');
    if (!res.ok) throw new Error('Failed to fetch questions: ' + res.status);
    return await res.json();
  } catch (err) {
    console.error("fetchQuestions error:", err);
    return [];
  }
}

function renderQuiz(questions) {
  const container = document.getElementById('quiz');
  container.innerHTML = '';

  questions.forEach(q => {
    const div = document.createElement('div');
    div.className = 'question neon-card';
    div.innerHTML = `
      <p><strong>${escapeHtml(q.question)}</strong></p>
      <label><input type="radio" name="q${q.id}" value="A"> A. ${escapeHtml(q.option_a)}</label><br>
      <label><input type="radio" name="q${q.id}" value="B"> B. ${escapeHtml(q.option_b)}</label><br>
      <label><input type="radio" name="q${q.id}" value="C"> C. ${escapeHtml(q.option_c)}</label><br>
      <label><input type="radio" name="q${q.id}" value="D"> D. ${escapeHtml(q.option_d)}</label>
    `;
    container.appendChild(div);
  });
}

function escapeHtml(text){
  if(text === null || text === undefined) return '';
  return String(text)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

document.addEventListener('DOMContentLoaded', async () => {
  const qs = await fetchQuestions();
  renderQuiz(qs);

  const submitBtn = document.getElementById('submitBtn');
  const resultBox = document.getElementById('result');

  if (!submitBtn) {
    console.warn("submitBtn not found on page.");
    return;
  }

  submitBtn.addEventListener('click', () => {
    const answers = {};
    qs.forEach(q => {
      const sel = document.querySelector(`input[name="q${q.id}"]:checked`);
      if (sel) answers[q.id] = sel.value;
    });

    // DEBUG: print collected answers
    console.log("Submitting answers:", answers);

    // create hidden form to POST to submit_answers.php (browser will follow redirect)
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'submit_answers.php';
    form.style.display = 'none';

    // Hidden input with JSON payload
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'json';
    input.value = JSON.stringify({ answers });

    form.appendChild(input);
    document.body.appendChild(form);

    // Extra debug: before submit, attach an onsubmit handler to log
    form.onsubmit = () => {
      console.log("Hidden form submitting to", form.action);
      return true;
    };

    // submit
    try {
      form.submit(); // browser follows redirect to results.php
    } catch (err) {
      console.error("Form submission error:", err);
      resultBox.innerText = 'Network error while saving result. (client)';
    }
  });
});
