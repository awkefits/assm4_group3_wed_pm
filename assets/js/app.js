/* CookBook interaction layer: navigation, recipe editor, sharing and serving calculator. */
(() => {
  const navToggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('#primary-nav');
  navToggle?.addEventListener('click', () => {
    const open = nav?.classList.toggle('open');
    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  document.querySelectorAll('[data-copy]').forEach(btn => btn.addEventListener('click', async () => {
    const text = btn.dataset.copy || window.location.href;
    try { await navigator.clipboard.writeText(text); btn.textContent = 'Link copied ✓'; }
    catch { window.prompt('Copy this recipe link:', text); }
  }));

  const ingredients = document.querySelector('[data-ingredients]');
  const steps = document.querySelector('[data-steps]');
  const ingredientTemplate = document.querySelector('#ingredient-template');
  const stepTemplate = document.querySelector('#step-template');

  document.querySelector('[data-add-ingredient]')?.addEventListener('click', () => {
    ingredients?.append(ingredientTemplate.content.cloneNode(true));
  });
  document.querySelector('[data-add-step]')?.addEventListener('click', () => {
    steps?.append(stepTemplate.content.cloneNode(true)); renumberSteps();
  });
  document.addEventListener('click', e => {
    const remove = e.target.closest('[data-remove-row]');
    if (!remove) return;
    const row = remove.closest('.repeat-row,.step-editor');
    const parent = row?.parentElement;
    if (parent && parent.children.length > 2) row.remove();
    else row?.querySelectorAll('input,textarea').forEach(el => el.value = '');
    renumberSteps();
  });
  function renumberSteps(){ document.querySelectorAll('.step-editor>span').forEach((el,i)=>el.textContent=String(i+1)); }

  function parseQty(value){
    value = String(value || '').trim();
    if (!value) return null;
    if (value.includes(' ')) {
      const [whole, frac] = value.split(/\s+/,2); const f = parseQty(frac);
      return Number(whole) + (f ?? 0);
    }
    if (value.includes('/')) { const [a,b] = value.split('/').map(Number); return b ? a/b : null; }
    const n = Number(value); return Number.isFinite(n) ? n : null;
  }
  function pretty(n){
    const common = [[.25,'1/4'],[.333,'1/3'],[.5,'1/2'],[.667,'2/3'],[.75,'3/4']];
    const whole = Math.floor(n); const frac = n-whole;
    const found = common.find(([v]) => Math.abs(frac-v)<.03);
    if(found) return (whole?whole+' ':'')+found[1];
    return Number.isInteger(n) ? String(n) : String(Math.round(n*100)/100);
  }
  document.querySelector('[data-serving-toggle]')?.addEventListener('click', () => {
    const list = document.querySelector('[data-serving-list]');
    const base = Number(list?.dataset.baseServings || 1);
    const raw = window.prompt(`Current recipe serves ${base}. Enter desired servings:`, String(base));
    const desired = Number(raw);
    if (!desired || desired < 1 || desired > 100) return;
    const factor = desired/base;
    list.querySelectorAll('[data-qty]').forEach(el => {
      const original = parseQty(el.dataset.qty);
      if (original !== null) el.textContent = pretty(original*factor);
    });
  });
})();
