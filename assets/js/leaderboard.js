// assets/js/leaderboard.js
// Fetches get_leaderboard.php every 5s and updates table with simple up/down animation

const API = '../php/get_leaderboard.php';
const REFRESH_MS = 5000;
let prevSnapshot = [];

function formatTime(ts){
  try {
    const d = new Date(ts);
    return d.toLocaleString();
  } catch(e) {
    return ts;
  }
}

function buildRow(item){
  const tr = document.createElement('tr');
  tr.className = 'rank-change';
  tr.dataset.username = item.username;

  const rankTd = document.createElement('td');
  rankTd.textContent = item.rank;

  const userTd = document.createElement('td');
  userTd.innerHTML = `<strong>${escapeHtml(item.username)}</strong>`;

  const scoreTd = document.createElement('td');
  scoreTd.className = 'score';
  scoreTd.textContent = item.score;

  const timeTd = document.createElement('td');
  timeTd.className = 'small';
  timeTd.textContent = formatTime(item.time);

  tr.appendChild(rankTd);
  tr.appendChild(userTd);
  tr.appendChild(scoreTd);
  tr.appendChild(timeTd);

  return tr;
}

function escapeHtml(s){
  return (''+s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

async function fetchAndRender(){
  try {
    const res = await fetch(API);
    if(!res.ok) throw new Error('network');
    const j = await res.json();
    if(j.status !== 'ok') throw new Error('api');

    const data = j.data || [];
    const tbody = document.querySelector('#leaderboardTable tbody');

    // compute movement by username
    const prevMap = {};
    prevSnapshot.forEach((it, idx) => prevMap[it.username] = { index: idx, score: it.score });

    // clear then re-add rows, applying classes for animation if present
    tbody.innerHTML = '';

    data.forEach((row, idx) => {
      const el = buildRow(row);

      const prev = prevMap[row.username];
      if(prev !== undefined) {
        if (row.rank < prev.index + 1) {
          el.classList.add('up');
        } else if (row.rank > prev.index + 1) {
          el.classList.add('down');
        }
        // remove the class after animation to allow future transitions
        setTimeout(()=> el.classList.remove('up','down'), 700);
      }

      tbody.appendChild(el);
    });

    prevSnapshot = data;
    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
  } catch(e){
    console.error('leaderboard update error', e);
  }
}

document.addEventListener('DOMContentLoaded', ()=> {
  fetchAndRender();
  setInterval(fetchAndRender, REFRESH_MS);
});
