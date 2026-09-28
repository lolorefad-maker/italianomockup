# Traduttore e interprete arabo – italiano · concept

تصميم موقع لمترجم عربي–إيطالي: الصفحة الرئيسية بلغتين (إيطالي + عربي باتجاه RTL كامل)، مع مشروع WordPress جاهز.

Concept del sito di un traduttore e interprete arabo–italiano: home page bilingue (italiano + arabo, RTL completo) e progetto WordPress pronto.

## مشاهدة التصميم · Vedere il design

- افتح `index.html` بأي متصفح.
- أو فعّل **GitHub Pages**: Settings ← Pages ← Deploy from a branch ← `main` / `root`. الرابط بيصير:
  `https://lolorefad-maker.github.io/italianomockup/`

إضافات على آخر الرابط:

| | |
|---|---|
| `#ar` | يفتح بالعربي · apre in arabo |
| `#smeraldo` · `#bordeaux` · `#lapis` · `#nero` | يفتح على لون معيّن · apre con una palette |

فوق الصفحة في "اختر اللون / Scegli il colore" حتى العميل يجرّب الألوان الأربعة.

## المحتوى · Contenuto

```
index.html      التصميم النهائي (v5) · il design finale
versions/       نسخ سابقة للمقارنة · versioni precedenti
  v4-oro-di-monreale.html   النسخة الذهبية
  v1-mockup-8-pagine.html   أول موكب بالصفحات الثمانية
mockup/         مصدر التصميم · sorgenti del design
  01-head.html  الألوان والخطوط والتنسيق (CSS)
  02-app.js     محتوى الصفحة والتفاعل
  build.sh      يبني index.html من جديد: sh mockup/build.sh
wordpress/      مشروع WordPress كامل (Docker) · progetto WordPress completo
```

## WordPress

التفاصيل بملف [`wordpress/README.md`](wordpress/README.md). باختصار:

```bash
cd wordpress
cp .env.example .env        # وغيّر كلمات السر
docker compose up -d
sh tools/setup.sh
```

بعدها: http://localhost:8088 (إيطالي) و http://localhost:8088/ar/ (عربي).

> النصوص والصور مؤقتة · Testi e immagini provvisori.
