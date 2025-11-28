/* populate UI */
const categories=[
    {id:'science',name:'Science',icon:'🧪',desc:'Biology, chemistry, physics',diff:4,color:'#4361ee'},
    {id:'gk',name:'General Knowledge',icon:'🌍',desc:'World, culture, events',diff:3,color:'#4cc9f0'},
    {id:'history',name:'History',icon:'📜',desc:'Events & figures',diff:4,color:'#f8961e'},
    {id:'computer',name:'Computer',icon:'💻',desc:'Tech & programming',diff:5,color:'#7209b7'},
    {id:'nepal',name:'Nepal',icon:'🏔️',desc:'Culture & geography',diff:2,color:'#f72585'},
    {id:'city',name:'Cities',icon:'🏙️',desc:'Famous cities',diff:3,color:'#4895ef'}
];
document.getElementById('userName').textContent=localStorage.getItem('quizUser')||'Player';
drawCategories();
loadUserStats();
loadRecentScores();

/* draw cards */
function drawCategories(){
    const grid=document.getElementById('categoryGrid');
    grid.innerHTML=categories.map(c=>`
        <div class="category-card" style="border-top:5px solid ${c.color}" onclick="startCat('${c.id}')">
            <div class="cat-icon">${c.icon}</div>
            <div class="cat-name">${c.name}</div>
            <div class="cat-desc">${c.desc}</div>
            <div class="cat-stars">${'★'.repeat(c.diff)}</div>
            <button class="cat-btn">Start Quiz</button>
        </div>`).join('');
}
function startCat(c){
    localStorage.setItem('quizCategory',c);
    window.location.href='quiz.html';
}

/* stats & history */
function loadUserStats(){
    const hist=JSON.parse(localStorage.getItem('quizHistory')||'[]');
    document.getElementById('totalQuizzes').textContent=hist.length;
    const avg=hist.length?Math.round(hist.reduce((a,b)=>a+b.score,0)/hist.length):0;
    document.getElementById('avgScore').textContent=avg+'%';
    const mins=Math.round(hist.reduce((a,b)=>a+b.time,0)/60);
    document.getElementById('totalTime').textContent=mins+'m';
}
function loadRecentScores(){
    const hist=JSON.parse(localStorage.getItem('quizHistory')||'[]');
    const list=document.getElementById('scoresList');
    if(!hist.length){list.innerHTML='<p style="color:#666">No scores yet. Take a quiz!</p>';return}
    list.innerHTML=hist.slice(-5).reverse().map(s=>`
        <div class="score-item">
            <span>${s.category}</span>
            <span>${new Date(s.date).toLocaleDateString()}</span>
            <span>${s.score}%</span>
        </div>`).join('');
}