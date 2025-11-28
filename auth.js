// ---------- login ----------
function login(){
    const u=document.getElementById('username').value.trim();
    const p=document.getElementById('password').value.trim();
    const err=document.getElementById('error');
    if(!u||!p){err.textContent='Username and password required';return;}
    // demo auth – accept anything non-empty
    localStorage.setItem('quizUser',u);
    localStorage.setItem('quizLoginTime',new Date().toISOString());
    window.location.href='home.html';
}

// ---------- logout ----------
function logout(){
    ['quizUser','quizLoginTime','quizCategory','quizResults','quizHistory','userAnswers'].forEach(k=>localStorage.removeItem(k));
    window.location.href='index.html';
}

// ---------- guard ----------
function checkAuth(){
    if(!localStorage.getItem('quizUser')){window.location.href='index.html';return false;}
    return true;
}