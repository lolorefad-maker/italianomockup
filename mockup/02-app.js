/* ===== v5 · quiet luxury: night, ivory, muted gold ===== */
const S = DATA.S, DOC = DATA.DOC_SVG, I = ICONS;
const LOGO = '<svg class="mark" viewBox="0 0 32 38" fill="none" stroke-width="1.3" stroke-linecap="round" aria-hidden="true"><path stroke="currentColor" d="M3 33V16a13 13 0 0 1 26 0v17"/><path stroke="currentColor" d="M9 33V19c0-5 3-8.5 7-11.5 4 3 7 6.5 7 11.5v14"/><rect x="1" y="35.4" width="10" height="2" fill="#009246"/><rect x="11" y="35.4" width="10" height="2" fill="#F1F2F1" stroke="currentColor" stroke-opacity=".3" stroke-width=".4"/><rect x="21" y="35.4" width="10" height="2" fill="#CE2B37"/></svg>';
const STAR = '<svg viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><rect x="4" y="4" width="10" height="10"/><rect x="4" y="4" width="10" height="10" transform="rotate(45 9 9)"/></svg>';
const PHONE = "+39 333 000 0000", EMAIL = "info@nomecognome.it", WA = "393330000000";

// Majolica tile reduced to fine gold lines (Arabic eight-point star inside Sicilian floral geometry).
const corners = r => [[0, 0], [120, 0], [0, 120], [120, 120]].map(([x, y]) => `<circle cx="${x}" cy="${y}" r="${r}"/>`).join("");
const petals = [0, 90, 180, 270].map(a => `<path d="M60 54C50 44 51 26 60 15C69 26 70 44 60 54Z" transform="rotate(${a} 60 60)"/>`).join("");
const DEFS = `<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs>
<pattern id="tileG" width="120" height="120" patternUnits="userSpaceOnUse"><g fill="none" stroke="#C6AE84" stroke-width=".9">${corners(34)}${corners(16)}${petals}<g transform="translate(60 60)"><rect x="-13" y="-13" width="26" height="26"/><rect x="-13" y="-13" width="26" height="26" transform="rotate(45)"/><circle r="9.5"/></g></g></pattern>
</defs></svg>`;
const pat = cls => `<svg class="${cls}" aria-hidden="true"><rect width="100%" height="100%" fill="url(#tileG)"/></svg>`;
const SEAL = `<svg class="seal" viewBox="0 0 120 120" role="img" aria-label="Italia" style="direction:ltr"><defs><path id="sealRing" d="M60,60 m-46,0 a46,46 0 1,1 92,0 a46,46 0 1,1 -92,0"/></defs><circle class="seal-bg" cx="60" cy="60" r="58"/><circle class="seal-in" cx="60" cy="60" r="37"/><text class="seal-t"><textPath href="#sealRing" textLength="286" lengthAdjust="spacing">TRADUTTORE · INTERPRETE · ARABO · ITALIANO ·</textPath></text><circle cx="60" cy="60" r="26" fill="#009246"/><circle cx="60" cy="60" r="17" fill="#F1F2F1"/><circle cx="60" cy="60" r="8" fill="#CE2B37"/></svg>`;

// Palermo drawn in a single gold line: aqueduct, Arab-Norman domes, bell tower, palms, moon.
function skyline() {
  const arches = (x0, n, w, gap, y) => {
    let d = `M${x0} 240V${y}h${n * (w + gap) + gap}V240`;
    for (let i = 0; i < n; i++) { const x = x0 + gap + i * (w + gap); d += `M${x} 240V${y + 24 + w / 2}a${w / 2} ${w / 2} 0 0 1 ${w} 0V240`; }
    return d;
  };
  const palm = (x, h, lean) => {
    const tx = x + lean * 1.6, ty = 240 - h;
    let s = `<path d="M${x} 240q${lean} -${h * .5} ${lean * 1.6} -${h}"/>`;
    [[-150, 44], [-120, 52], [-60, 52], [-30, 44], [-175, 30], [-5, 30]].forEach(([a, l]) => {
      const r = a * Math.PI / 180, ex = tx + Math.cos(r) * l, ey = ty + Math.sin(r) * l * .55 + 18;
      s += `<path d="M${tx} ${ty}Q${tx + Math.cos(r) * l * .55} ${ty + Math.sin(r) * l * .5 - 8} ${ex.toFixed(1)} ${ey.toFixed(1)}"/>`;
    });
    return s;
  };
  return `<svg class="skyline" viewBox="0 0 1440 250" preserveAspectRatio="xMidYMax meet" role="img" aria-label="Palermo" fill="none" stroke="#C6AE84" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round">
<path d="M0 214Q180 178 360 200T720 194T1080 206T1440 188" opacity=".3"/>
<path d="${arches(40, 8, 34, 14, 150)}" opacity=".5"/>
<path d="M1110 240V102h54v138M1102 102l35-32 35 32M1128 150v-12a9 9 0 0 1 18 0v12ZM1128 196v-12a9 9 0 0 1 18 0v12Z" opacity=".55"/>
<g opacity=".9"><path d="M540 240V206h50M590 240V168h100v72M690 240V140h120v100M810 240V180h80v60M890 206h70v34"/>
<path d="M602 168a38 38 0 0 1 76 0M700 140a50 50 0 0 1 100 0M818 180a32 32 0 0 1 64 0" fill="rgba(198,174,132,.09)"/>
<path d="M632 214v-12a8 8 0 0 1 16 0v12ZM732 186v-14a8 8 0 0 1 16 0v14ZM764 186v-14a8 8 0 0 1 16 0v14ZM842 218v-10a7 7 0 0 1 14 0v10Z"/></g>
<g opacity=".7">${palm(500, 120, 8)}${palm(990, 96, -10)}</g>
<circle cx="1250" cy="70" r="20" opacity=".8"/><circle cx="1250" cy="70" r="34" opacity=".18"/>
<path d="M0 240H1440" opacity=".6"/></svg>`;
}

/* ---------- copy for this home ---------- */
const N = {
  it: {
    note: "Concept home · v5 · testi e immagini provvisori", langLabel: "Lingua",
    nav: [["Servizi", "servizi"], ["Chi sono", "chi-sono"], ["Metodo", "metodo"], ["Domande", "domande"], ["Contatti", "contatti"]],
    label: "Traduttore e interprete · Arabo — Italiano",
    h1: 'La parola giusta<br>tra <span class="ar-w" lang="ar" dir="rtl">العربية</span><br>e l’italiano.',
    wa: "oppure scrivimi su WhatsApp",
    leadShort: "Traduzioni, asseverazioni e interpretariato tra arabo e italiano. Madrelingua araba, CTU del Tribunale.",
    facts: ["Risposta entro 24 ore", "CTU · Tribunale", "Madrelingua araba"],
    wTop: "Italiano", wBot: "Arabo",
    ch: ["Perché scegliermi", "Servizi", "Chi sono", "Metodo", "Dicono di me", "Domande"],
    cred: "Credenziali", credline: "CTU presso il Tribunale di [Città] · Certificazione DITALS · Dati trattati secondo il GDPR",
    most: "La più richiesta",
    quote: "Tradurre non è sostituire parole. È accompagnare le persone da una lingua, e da una cultura, all’altra.",
    quote2: "الترجمة ليست استبدال كلمات، بل مرافقة الناس من لغة إلى أخرى، ومن ثقافة إلى أخرى.", q2: "ar",
    role: "Traduttore · Interprete · CTU",
    final: 'Hai un documento <em>da tradurre?</em>',
    based: "Con sede in Italia", fSvc: "Servizi", fCont: "Contatti"
  },
  ar: {
    note: "تصميم الصفحة الرئيسية · النسخة 5 · النصوص والصور مؤقتة", langLabel: "اللغة",
    nav: [["الخدمات", "servizi"], ["من أنا", "chi-sono"], ["طريقة العمل", "metodo"], ["أسئلة", "domande"], ["تواصل", "contatti"]],
    label: "مترجم ومترجم فوري · العربية — الإيطالية",
    h1: 'الكلمة الصحيحة<br>بين العربية<br>و<span class="it-w" lang="it" dir="ltr">l’italiano</span>',
    wa: "أو راسلني على واتساب",
    leadShort: "ترجمة عادية ومحلَّفة وترجمة فورية بين العربية والإيطالية. العربية لغتي الأم، وخبير لدى المحكمة.",
    facts: ["رد خلال 24 ساعة", "خبير لدى المحكمة", "العربية لغتي الأم"],
    wTop: "العربية", wBot: "الإيطالية",
    ch: ["لماذا أنا", "الخدمات", "من أنا", "طريقة العمل", "آراء العملاء", "أسئلة"],
    cred: "الاعتمادات", credline: "خبير لدى محكمة [المدينة] (CTU) · شهادة DITALS · معالجة البيانات وفق اللائحة الأوروبية GDPR",
    most: "الأكثر طلبًا",
    quote: "الترجمة ليست استبدال كلمات، بل مرافقة الناس من لغة إلى أخرى، ومن ثقافة إلى أخرى.",
    quote2: "Tradurre non è sostituire parole. È accompagnare le persone da una lingua, e da una cultura, all’altra.", q2: "it",
    role: "مترجم · مترجم فوري · خبير لدى المحكمة",
    final: 'لديك وثيقة <em>تحتاج إلى ترجمة؟</em>',
    based: "مقيم في إيطاليا", fSvc: "الخدمات", fCont: "التواصل"
  }
};
const PAIRS = [["Parola", "كلمة"], ["Fiducia", "ثقة"], ["Documento", "وثيقة"], ["Famiglia", "عائلة"], ["Futuro", "مستقبل"], ["Casa", "بيت"]];
const NUM = { it: ["I", "II", "III", "IV", "V", "VI"], ar: ["١", "٢", "٣", "٤", "٥", "٦"] };
const ORDER = ["asseverazioni", "traduzioni", "interpretariato", "mediazione", "lezioni"];

/* ---------- palettes (presentation tool) ---------- */
const PALETTES = [
  ["nero", "#0B0F17", "Nero e champagne", "أسود وشمبانيا"],
  ["smeraldo", "#07241D", "Smeraldo e oro", "زمردي وذهبي"],
  ["bordeaux", "#230A10", "Bordeaux e oro", "عنّابي وذهبي"],
  ["lapis", "#0A1838", "Lapislazzuli e oro", "لازوردي وذهبي"]
];
let palette = "nero";
function paletteUI(inline) {
  const lab = lang === "ar" ? "اختر اللون" : "Scegli il colore";
  const cur = PALETTES.find(p => p[0] === palette) || PALETTES[0];
  return `<div class="palette${inline ? " inline" : ""}" role="group" aria-label="${lab}"><span>${lab}</span><em class="pal-name">${lang === "ar" ? cur[3] : cur[2]}</em>${PALETTES.map(([id, sw, it, ar]) => `<button type="button" data-pal="${id}" style="--sw:${sw}" aria-pressed="${palette === id}" aria-label="${lang === "ar" ? ar : it}" title="${lang === "ar" ? ar : it}"></button>`).join("")}</div>`;
}
function applyPalette(id) {
  palette = PALETTES.some(p => p[0] === id) ? id : "nero";
  if (palette === "nero") document.documentElement.removeAttribute("data-palette");
  else document.documentElement.setAttribute("data-palette", palette);
  document.querySelectorAll("[data-pal]").forEach(b => b.setAttribute("aria-pressed", String(b.dataset.pal === palette)));
  const cur = PALETTES.find(p => p[0] === palette);
  if (cur) document.querySelectorAll(".pal-name").forEach(nm => { nm.textContent = lang === "ar" ? cur[3] : cur[2]; });
  try { localStorage.setItem("v5-palette", palette); } catch (_) {}
}

/* ---------- render ---------- */
let lang = "it", timer = null;
const $ = s => document.querySelector(s);
const bdi = s => `<bdi dir="ltr">${s}</bdi>`;
const waUrl = t => `https://wa.me/${WA}?text=${encodeURIComponent(t.ui.waText)}`;
const label = (k, extra = "") => `<p class="label"><span class="n" aria-hidden="true">${NUM[lang][k]}</span><span>${N[lang].ch[k]}</span>${extra}</p>`;

function view() {
  const t = S[lang], n = N[lang], h = t.home, sv = t.svc;
  const top = lang === "ar" ? 1 : 0, bot = 1 - top;
  const word = (i, k) => `<span class="w-word ${i ? "w-ar" : "w-it"}" data-i="${i}" ${i ? 'lang="ar" dir="rtl"' : 'lang="it"'}>${PAIRS[k][i]}</span>`;
  const q = h.quotes[0];
  return `
<header class="hdr dark" id="hdr"><div class="wrap">
  <a class="brand" href="#top">${LOGO}<span><span class="brand-name">${t.ui.brand}</span><span class="brand-tag">${t.ui.brandTag}</span></span></a>
  <nav class="nav" aria-label="Menu">${n.nav.map(([l, id]) => `<a href="#${id}">${l}</a>`).join("")}</nav>
  <div class="lang" role="group" aria-label="${n.langLabel}"><button type="button" lang="it" data-lang="it" aria-pressed="${lang === "it"}"><span class="flag-it" aria-hidden="true"></span>IT</button><span class="sep" aria-hidden="true"></span><button type="button" lang="ar" data-lang="ar" aria-pressed="${lang === "ar"}">عربي</button></div>
  <a class="btn btn-ink hdr-cta" href="#contatti">${t.ui.cta}</a>
  <button type="button" class="menu-btn" data-menu aria-expanded="false" aria-controls="mmenu" aria-label="${lang === "ar" ? "القائمة" : "Menu"}"><i></i><i></i></button>
</div></header>
<div class="mmenu" id="mmenu" hidden><nav aria-label="Menu">${n.nav.map(([l, id], i) => `<a href="#${id}" data-close><span>${NUM[lang][i]}</span>${l}</a>`).join("")}</nav><a class="btn btn-gold" href="#contatti" data-close>${t.ui.ctaLong}${I.arrow}</a><a class="tlink" href="${waUrl(t)}" target="_blank" rel="noopener">${I.wa}${n.wa}</a><p class="mm-c">${bdi(PHONE)} · ${bdi(EMAIL)}</p></div>
<main id="top">

<section class="hero"><div class="wrap hero-grid">
  <div class="pal-m">${paletteUI(true)}</div>
  <div class="hero-copy">
    <p class="label">${n.label}</p>
    <h1 class="hero-t">${n.h1}</h1>
    <p class="lead only-d">${h.lead}</p><p class="lead only-m">${n.leadShort}</p>
    <div class="actions"><a class="btn btn-gold" href="#contatti">${t.ui.ctaLong}${I.arrow}</a><a class="tlink" href="${waUrl(t)}" target="_blank" rel="noopener">${I.wa}${n.wa}</a></div>
    <p class="facts">${n.facts.map(f => `<span>${f}</span>`).join("")}</p>
  </div>
  <div class="window">
    <div class="w-frame"></div>
    <div class="w-arch">${pat("")}</div>
    <div class="w-panel"><span class="w-lab">${n.wTop}</span>${word(top, 0)}<span class="w-rule" aria-hidden="true"><i></i>${STAR}<i></i></span>${word(bot, 0)}<span class="w-lab">${n.wBot}</span></div>
    ${SEAL}
  </div>
</div></section>

<div class="ornament" aria-hidden="true"><i></i>${STAR}<i></i></div>
<section class="sec" style="padding-top:clamp(56px,6vw,88px)"><div class="wrap split">
  <div class="rv">${label(0)}<h2>${h.whyT}</h2><p class="lead">${h.whyL}</p></div>
  <div class="rv"><div class="nums">${h.stats.map(([a, b]) => `<div><b>${bdi(a)}</b><span>${b}</span></div>`).join("")}</div>
  <p class="credline"><strong>${n.cred}</strong>${n.credline}</p></div>
</div></section>

<section class="sec svc" id="servizi"><div class="wrap svc-grid">
  <div class="svc-side rv">${label(1)}<h2>${h.svcT}</h2><p class="lead">${h.svcL}</p><div class="doc">${DOC}</div></div>
  <div class="rows rv">${ORDER.map((s, i) => `<a class="row" href="#contatti"><span class="n">${NUM[lang][i]}</span><h3>${sv[s].name}${i === 0 ? `<span class="most">${n.most}</span>` : ""}</h3><p>${sv[s].tile}</p><span class="arr">${I.arrow}</span></a>`).join("")}</div>
</div></section>

<section class="sec about-sec" id="chi-sono"><div class="wrap about">
  <div class="pwrap rv"><div class="portrait">${pat("pat")}<svg class="sil" viewBox="0 0 400 500" preserveAspectRatio="xMidYMax meet" aria-hidden="true"><circle cx="200" cy="232" r="64"/><path d="M62 500c8-112 70-172 138-172s130 60 138 172z"/></svg><span class="p-cap">${t.ui.portrait}</span></div></div>
  <div class="rv">${label(2)}<blockquote class="bigq">${n.quote}</blockquote><p class="bigq-2" lang="${n.q2}" dir="${n.q2 === "ar" ? "rtl" : "ltr"}">${n.quote2}</p><p class="lead">${t.about.bio[1]}</p>
  <div class="sign"><strong>${t.ui.brand}</strong><span>${n.role}</span></div></div>
</div></section>

<section class="sec" id="metodo"><div class="wrap">
  <div class="rv">${label(3)}<h2>${h.howT}</h2></div>
  <ol class="steps rv">${t.steps.map(([a, b], i) => `<li class="step"><span class="n">${i + 1}</span><h3>${a}</h3><p>${b}</p></li>`).join("")}</ol>
</div></section>

<section class="sec testi-sec"><div class="wrap testi rv">
  ${label(4)}<span class="qm" aria-hidden="true">“</span><blockquote>${q[0]}</blockquote><cite>${q[1]} · ${q[2]}</cite><span class="sample">${t.ui.sample}</span>
</div></section>

<section class="sec" id="domande"><div class="wrap faq">
  <div class="rv">${label(5)}<h2>${t.ui.faq}</h2><p class="lead">${t.ui.faqLead}</p><a class="tlink" href="${waUrl(t)}" target="_blank" rel="noopener">${I.wa}${n.wa}</a></div>
  <div class="acc rv">${h.faq.map(([qq, a], i) => `<details${i === 0 ? " open" : ""}><summary>${qq}</summary><p>${a}</p></details>`).join("")}</div>
</div></section>

<section class="final" id="contatti"><div class="wrap rv">
  <p class="label">${t.nav.contatti}</p><h2>${n.final}</h2><p>${t.ui.ctaS}</p>
  <div class="actions"><a class="btn btn-gold" href="#contatti">${t.ui.ctaLong}${I.arrow}</a><a class="tlink" href="${waUrl(t)}" target="_blank" rel="noopener">${I.wa}${n.wa}</a></div>
</div>${skyline()}</section>
</main>

<footer class="foot"><div class="wrap">
  <div class="foot-grid">
    <div><a class="brand" href="#top">${LOGO}<span><span class="brand-name">${t.ui.brand}</span><span class="brand-tag">${t.ui.brandTag}</span></span></a><p>${t.ui.footTag}</p></div>
    <div><p class="foot-h">${n.fSvc}</p><ul>${ORDER.map(s => `<li>${sv[s].name}</li>`).join("")}</ul></div>
    <div><p class="foot-h">${n.fCont}</p><ul><li>${I.wa}${bdi(PHONE)}</li><li>${I.mail}${bdi(EMAIL)}</li><li>${I.pin}${t.ui.area}</li><li>${I.clock}${t.ui.hours}</li></ul></div>
  </div>
  <div class="foot-bottom"><span>${t.ui.legal}</span><span class="based"><span class="flag-it" aria-hidden="true"></span>${n.based}</span><span class="foot-note">${n.note}</span></div>
</div></footer>
<a class="fab" href="${waUrl(t)}" target="_blank" rel="noopener" aria-label="${t.ui.waLong}">${I.wa}<span>${t.ui.wa}</span></a>
${paletteUI()}
<div class="mbar"><a class="btn btn-ink" href="${waUrl(t)}" target="_blank" rel="noopener">${I.wa}${t.ui.wa}</a><a class="btn btn-gold" href="#contatti">${t.ui.cta}</a></div>`;
}

// The two words in the arch change slowly, with a plain fade.
function startCycler() {
  clearInterval(timer);
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
  let k = 0;
  timer = setInterval(() => {
    k = (k + 1) % PAIRS.length;
    const words = document.querySelectorAll(".w-word");
    words.forEach(w => w.classList.add("out"));
    setTimeout(() => words.forEach(w => { w.textContent = PAIRS[k][+w.dataset.i]; w.classList.remove("out"); }), 1150);
  }, 4800);
}

// Header follows the section underneath it: light text over night sections, solid ivory elsewhere.
function syncHeader() {
  const h = $("#hdr");
  if (!h) return;
  const probe = h.getBoundingClientRect().bottom + 2;
  const dark = [...document.querySelectorAll(".hero, .about-sec, .final")].some(s => { const r = s.getBoundingClientRect(); return r.top <= probe && r.bottom > probe; });
  const menu = document.documentElement.classList.contains("menu-open");
  h.classList.toggle("dark", dark || menu);
  h.classList.toggle("solid", !dark && !menu);
  const act = $(".hero .actions"), bar = $(".mbar");
  if (bar) bar.classList.toggle("show", !act || act.getBoundingClientRect().bottom < 0);
}

function render(keepScroll) {
  const y = window.scrollY;
  document.documentElement.lang = lang;
  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
  document.documentElement.classList.remove("menu-open");
  document.documentElement.style.overflow = "";
  document.getElementById("app").innerHTML = view();
  document.title = S[lang].seo.home[0];
  startCycler();
  if (keepScroll) window.scrollTo(0, y);
  syncHeader();
}

function toggleMenu(open) {
  const m = $("#mmenu"), b = $("[data-menu]");
  if (!m || !b) return;
  if (open === undefined) open = m.hidden;
  m.hidden = !open;
  document.documentElement.classList.toggle("menu-open", open);
  document.documentElement.style.overflow = open ? "hidden" : "";
  b.setAttribute("aria-expanded", String(open));
  requestAnimationFrame(() => m.classList.toggle("open", open));
  syncHeader();
}
document.addEventListener("keydown", e => { if (e.key === "Escape") toggleMenu(false); });

document.addEventListener("click", e => {
  if (e.target.closest("[data-menu]")) { toggleMenu(); return; }
  if (e.target.closest("[data-close]")) toggleMenu(false);
  const p = e.target.closest("[data-pal]");
  if (p) { applyPalette(p.dataset.pal); return; }
  const b = e.target.closest("[data-lang]");
  if (b && b.dataset.lang !== lang) {
    lang = b.dataset.lang;
    try { localStorage.setItem("v5-lang", lang); } catch (_) {}
    render(true);
  }
});
window.addEventListener("scroll", syncHeader, { passive: true });

document.body.insertAdjacentHTML("afterbegin", DEFS);
try { const s = localStorage.getItem("v5-lang"); if (s === "ar" || s === "it") lang = s; } catch (_) {}
try { const p = localStorage.getItem("v5-palette"); if (p) palette = p; } catch (_) {}
const hp = ["nero", "smeraldo", "bordeaux", "lapis"].find(x => location.hash === "#" + x);
if (hp) palette = hp;
if (location.hash === "#ar") lang = "ar";
if (location.hash === "#it") lang = "it";
render(false);
applyPalette(palette);
