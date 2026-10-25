document.querySelectorAll('.count').forEach(el=>{const n=+el.dataset.n;let i=0;const t=setInterval(()=>{i+=Math.max(1,Math.ceil(n/30));if(i>=n){i=n;clearInterval(t)}el.textContent=i},30)});
document.querySelectorAll('.eye').forEach(e=>e.onclick=()=>{const i=e.previousElementSibling;i.type=i.type==='password'?'text':'password'});
document.querySelectorAll('[data-c]').forEach(a=>a.onclick=ev=>{if(!confirm(a.dataset.c))ev.preventDefault()});
document.querySelectorAll('.search').forEach(s=>s.oninput=()=>{document.querySelectorAll('#'+s.dataset.t+' tr').forEach((r,i)=>{if(i)r.style.display=r.textContent.toLowerCase().includes(s.value.toLowerCase())?'':'none'})});
const th=document.getElementById('theme');if(th)th.onclick=()=>{const d=document.documentElement;d.dataset.theme=d.dataset.theme==='dark'?'light':'dark';localStorage.t=d.dataset.theme};
const mn=document.getElementById('menu');if(mn)mn.onclick=()=>document.getElementById('sb').classList.toggle('open');
setTimeout(()=>document.querySelectorAll('.toast').forEach(t=>t.style.opacity=0),2800);
