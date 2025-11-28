/* ===== question banks ===== */
const questionSets={
    science:[
        {q:"What is H2O?",options:["Oxygen","Water","Hydrogen"],answer:"Water",exp:"H2O = water"},
        {q:"Planet known as Red Planet?",options:["Mars","Jupiter","Venus"],answer:"Mars",exp:"Mars appears red due to iron oxide"},
        {q:"Closest star to Earth?",options:["Proxima","Sun","Sirius"],answer:"The Sun",exp:"The Sun is our closest star"},
        {q:"Hardest natural substance?",options:["Gold","Diamond","Iron"],answer:"Diamond",exp:"Diamond is the hardest natural material"},
        {q:"Gas plants absorb?",options:["Oxygen","CO₂","Nitrogen"],answer:"Carbon Dioxide",exp:"Photosynthesis uses CO₂"},
        {q:"Chemical symbol of gold?",options:["Go","Gd","Au"],answer:"Au",exp:"Au from Latin 'aurum'"},
        {q:"Bones in adult human?",options:["206","300","150"],answer:"206",exp:"Babies have ~300, adults 206"},
        {q:"Main gas in atmosphere?",options:["Oxygen","CO₂","Nitrogen"],answer:"Nitrogen",exp:"≈78% nitrogen"},
        {q:"Speed of light?",options:["300k km/s","150k km/s","1M km/s"],answer:"300,000 km/s",exp:"≈299,792 km/s"},
        {q:"Planet with most moons?",options:["Jupiter","Saturn","Uranus"],answer:"Saturn",exp:"Saturn has 80+ moons"}
    ],
    gk:[
        {q:"Capital of France?",options:["Paris","London","Berlin"],answer:"Paris",exp:"Paris is the capital"},
        {q:"Largest ocean?",options:["Atlantic","Indian","Pacific"],answer:"Pacific",exp:"Covers ~30% Earth surface"},
        {q:"Number of continents?",options:["5","6","7"],answer:"7",exp:"Asia,Africa,N.America,S.America,Antarctica,Europe,Australia"},
        {q:"Largest mammal?",options:["Elephant","Blue Whale","Giraffe"],answer:"Blue Whale",exp:"Up to 100 ft long"},
        {q:"Country with largest population?",options:["India","USA","China"],answer:"China",exp:"≈1.4 billion"},
        {q:"Smallest country?",options:["Monaco","Vatican","San Marino"],answer:"Vatican City",exp:"0.17 sq mi"},
        {q:"Language with most native speakers?",options:["English","Spanish","Mandarin"],answer:"Mandarin Chinese",exp:"≈1.1 billion native"},
        {q:"Most widely spoken language?",options:["English","Mandarin","Spanish"],answer:"English",exp:"Including non-native"},
        {q:"Morning Star planet?",options:["Mars","Venus","Jupiter"],answer:"Venus",exp:"Venus visible at dawn/dusk"},
        {q:"Currency of Japan?",options:["Yuan","Won","Yen"],answer:"Yen",exp:"¥ is the yen symbol"}
    ],
    history:[
        {q:"Who discovered America?",options:["Columbus","Magellan","Vasco"],answer:"Columbus",exp:"1492 landing"},
        {q:"French Revolution year?",options:["1789","1776","1800"],answer:"1789",exp:"Storming Bastille 14 July"},
        {q:"Alexander the Great ruled?",options:["Roman","Macedonian","Ottoman"],answer:"Macedonian",exp:"Macedonian empire"},
        {q:"WWII end year?",options:["1944","1945","1946"],answer:"1945",exp:"Germany May, Japan Sept"},
        {q:"First woman Nobel winner?",options:["Marie Curie","Rosalind","Florence"],answer:"Marie Curie",exp:"1903 Physics, 1911 Chemistry"},
        {q:"Built Machu Picchu?",options:["Aztec","Maya","Inca"],answer:"Inca",exp:"15th century Peru"},
        {q:"Mona Lisa painter?",options:["Michelangelo","Da Vinci","Raphael"],answer:"Leonardo da Vinci",exp:"1503-1506"},
        {q:"Pilgrims' ship?",options:["Santa Maria","Mayflower","Nina"],answer:"Mayflower",exp:"1620 Massachusetts"},
        {q:"US Civil War?",options:["Revolutionary","Civil","1812"],answer:"Civil War",exp:"1861-1865"},
        {q:"First US President?",options:["Jefferson","Adams","Washington"],answer:"George Washington",exp:"1789-1797"}
    ],
    computer:[
        {q:"CPU stands for?",options:["Central Processing Unit","Computer Power Unit","Central Print Unit"],answer:"Central Processing Unit",exp:"Main processor"},
        {q:"HTML stands for?",options:["Hyper Text Markup Language","Home Tool Markup","Hyperlinks Text Mark"],answer:"Hyper Text Markup Language",exp:"Web markup language"},
        {q:"Java created by?",options:["Microsoft","Sun","IBM"],answer:"Sun Microsystems",exp:"James Gosling 1995"},
        {q:"RAM stands for?",options:["Random Access Memory","Readily Available Memory","Random Allocation Memory"],answer:"Random Access Memory",exp:"Volatile memory"},
        {q:"Not a programming language?",options:["Python","Java","Microsoft"],answer:"Microsoft",exp:"Microsoft is a company"},
        {q:"First iPhone year?",options:["2005","2007","2010"],answer:"2007",exp:"Announced Jan, released June"},
        {q:"URL stands for?",options:["Uniform Resource Locator","Universal Reference Link","Uniform Reference Locator"],answer:"Uniform Resource Locator",exp:"Web address format"},
        {q:"Database system?",options:["MySQL","Java","Python"],answer:"MySQL",exp:"Relational DB"},
        {q:"VPN stands for?",options:["Virtual Private Network","Virtual Public Network","Verified Private Network"],answer:"Virtual Private Network",exp:"Secure tunnel"},
        {q:"Email protocol?",options:["SMTP","HTTP","FTP"],answer:"SMTP",exp:"Simple Mail Transfer Protocol"}
    ],
    nepal:[
        {q:"Capital of Nepal?",options:["Kathmandu","Pokhara","Lalitpur"],answer:"Kathmandu",exp:"Largest city"},
        {q:"Highest mountain in Nepal?",options:["Dhaulagiri","Everest","Annapurna"],answer:"Everest",exp:"8,848 m"},
        {q:"National flower?",options:["Rhododendron","Lotus","Marigold"],answer:"Rhododendron",exp:"Laligurans"},
        {q:"Narayani river?",options:["Koshi","Gandaki","Karnali"],answer:"Gandaki",exp:"Gandaki → Narayani"},
        {q:"Republic Day?",options:["May 29","Sept 20","Jan 15"],answer:"May 29",exp:"2008 republic"},
        {q:"Currency?",options:["Rupee","Taka","Rupiah"],answer:"Rupee",exp:"Nepalese rupee"},
        {q:"Largest national park?",options:["Chitwan","Bardiya","Sagarmatha"],answer:"Bardiya",exp:"968 km²"},
        {q:"New Year called?",options:["Diwali","Dashain","Bisket Jatra"],answer:"Bisket Jatra",exp:"Bikram Sambat"},
        {q:"Highest lake?",options:["Rara","Phewa","Tilicho"],answer:"Tilicho",exp:"4,919 m"},
        {q:"Main religion?",options:["Buddhism","Hinduism","Islam"],answer:"Hinduism",exp:"≈81%"}
    ],
    city:[
        {q:"Taj Mahal city?",options:["Delhi","Agra","Jaipur"],answer:"Agra",exp:"Mughal monument"},
        {q:"Big Apple?",options:["New York","LA","Chicago"],answer:"New York",exp:"NYC nickname"},
        {q:"City of Light?",options:["Paris","London","Rome"],answer:"Paris",exp:"Enlightenment & lights"},
        {q:"Great Wall city?",options:["Shanghai","Beijing","Xi'an"],answer:"Beijing",exp:"Nearby sections"},
        {q:"118 islands city?",options:["Amsterdam","Venice","Stockholm"],answer:"Venice",exp:"Venice, Italy"},
        {q:"Carnival city?",options:["Madrid","Rio","Berlin"],answer:"Rio de Janeiro",exp:"Brazil carnival"},
        {q:"Eternal City?",options:["Athens","Rome","Jerusalem"],answer:"Rome",exp:"Long history"},
        {q:"Acropolis city?",options:["Rome","Athens","Istanbul"],answer:"Athens",exp:"Greek citadel"},
        {q:"Europe-Asia city?",options:["Moscow","Istanbul","Cairo"],answer:"Istanbul",exp:"Turkey straddle"},
        {q:"Windy City?",options:["Chicago","SF","Boston"],answer:"Chicago",exp:"Chicago nickname"}
    ]
};

/* ===== state ===== */
let idx=0,score=0,timer,timeLeft=15,questions=[],userAnswers=[],startTime;

/* ===== init ===== */
if(!checkAuth()) throw new Error("Unauthenticated");
const cat=localStorage.getItem('quizCategory');
questions=questionSets[cat]||[];
if(!questions.length){alert("No questions");location.href='home.html'}
startTime=Date.now();
userAnswers=new Array(questions.length);
loadQuestion();

/* ===== load ===== */
function loadQuestion(){
    clearInterval(timer);
    if(idx>=questions.length){finish();return}
    const q=questions[idx];
    document.getElementById('currentQ').textContent=idx+1;
    document.getElementById('totalQ').textContent=questions.length;
    document.getElementById('score').textContent=score;
    document.getElementById('questionText').textContent=q.q;
    updateProgress();
    renderOptions(q);
    startTimer();
}
function renderOptions(q){
    const box=document.getElementById('optionsContainer');
    box.innerHTML='';
    q.options.forEach((opt,i)=>{
        const card=document.createElement('div');
        card.className='option-card';
        card.innerHTML=`<div class="option-letter">${String.fromCharCode(65+i)}</div><div>${opt}</div>`;
        card.onclick=()=>selectCard(card,opt);
        box.appendChild(card);
    });
    document.getElementById('nextBtn').style.display='none';
}
function selectCard(card,opt){
    document.querySelectorAll('.option-card').forEach(c=>c.classList.remove('selected'));
    card.classList.add('selected');
    userAnswers[idx]=opt;
    document.getElementById('nextBtn').style.display='inline-flex';
}
function updateProgress(){
    const p=(idx/questions.length)*100;
    document.getElementById('progressFill').style.width=p+'%';
}

/* ===== timer ===== */
function startTimer(){
    timeLeft=15;
    const fill=document.getElementById('timerFill');
    const txt=document.getElementById('timerText');
    fill.style.width='100%';
    fill.style.background='var(--warning)';
    txt.textContent='15s';
    txt.classList.remove('timer-warning');
    timer=setInterval(()=>{
        timeLeft--;
        fill.style.width=(timeLeft/15*100)+'%';
        txt.textContent=timeLeft+'s';
        if(timeLeft<=5){fill.style.background='var(--danger)';txt.classList.add('timer-warning')}
        if(timeLeft<=0){clearInterval(timer);nextQuestion()}
    },1000);
}

/* ===== nav ===== */
function nextQuestion(){
    if(userAnswers[idx]===undefined){alert('Select an answer');return}
    clearInterval(timer);
    const cards=document.querySelectorAll('.option-card');
    const correctAns=questions[idx].answer;
    cards.forEach(c=>c.classList.add('disabled'));
    const selectedCard=Array.from(cards).find(c=>c.classList.contains('selected'));
    if(userAnswers[idx]===correctAns){
        score++;
        selectedCard.classList.add('correct');
        playSound('sndCorrect');
        sparkle(selectedCard);
    }else{
        selectedCard.classList.add('wrong');
        playSound('sndWrong');
        cards.forEach(c=>{
            if(c.textContent.trim().includes(correctAns)) c.classList.add('correct');
        });
    }
    document.getElementById('nextBtn').style.display='none';
    setTimeout(()=>{idx++;loadQuestion()},1000);
}
function exitQuiz(){
    if(confirm('Exit quiz?')) location.href='home.html';
}

/* ===== finish ===== */
function finish(){
    const totalTime=Math.round((Date.now()-startTime)/1000);
    const pct=Math.round((score/questions.length)*100);
    document.getElementById('quizWrapper').classList.add('hidden');
    document.getElementById('resultsWrapper').classList.remove('hidden');
    document.getElementById('scorePercent').textContent=pct+'%';
    document.getElementById('correctStat').textContent=score;
    document.getElementById('totalStat').textContent=questions.length;
    document.getElementById('timeStat').textContent=totalTime+'s';
    document.getElementById('feedback').innerHTML=getFeedback(pct);
    playSound('sndScore');
    if(pct===100) celebrate();
    
    // 1.  build result object
    const result={
        category: localStorage.getItem('quizCategory'),
        score: pct,
        correct: score,
        total: questions.length,
        time: totalTime,
        date: new Date().toISOString()
    };

    // 2.  push into history
    const history=JSON.parse(localStorage.getItem('quizHistory')||'[]');
    history.push(result);
    localStorage.setItem('quizHistory',JSON.stringify(history));

    // 3.  go home
    window.location.href='home.html';
}
function getFeedback(p){
    if(p===100) return '<h3>🎉 Perfect!</h3><p>You are a genius!</p>';
    if(p>=70) return '<h3>👍 Great!</h3><p>Solid knowledge!</p>';
    if(p>=50) return '<h3>💪 Good!</h3><p>Keep improving!</p>';
    return '<h3>📚 Keep learning!</h3><p>Review and retry.</p>';
}
function celebrate(){
    const dur=5000,end=Date.now()+dur,def={startVelocity:30,spread:360,ticks:60,zIndex:1000};
    const intr=setInterval(()=>{
        const t=end-Date.now();if(t<=0){clearInterval(intr);return}
        confetti(Object.assign({},def,{particleCount:50*(t/dur),origin:{x:Math.random(),y:Math.random()-.2}}));
    },250);
}

/* ===== sfx ===== */
function playSound(id){
    const el=document.getElementById(id);
    if(el?.src) el.play().catch(()=>{});
}
function sparkle(card){
    for(let i=0;i<7;i++){
        const s=document.createElement('div');
        s.style.cssText=`position:absolute;width:6px;height:6px;border-radius:50%;background:radial-gradient(circle,#fff,#ff0);top:${Math.random()*70}%;left:${Math.random()*70}%;pointer-events:none;animation:sparkle .8s ease-out`;
        card.appendChild(s);
        setTimeout(()=>s.remove(),800);
    }
}