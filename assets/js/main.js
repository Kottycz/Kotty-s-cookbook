/* ── Import receptu z .txt souboru ── */
const txtImport = document.getElementById('txt-import');
if (txtImport) {
    txtImport.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const text = e.target.result;
            const status = document.getElementById('import-status');

            try {
                parseRecipeTxt(text);
                status.textContent = '✓ Recept načten!';
                status.style.color = '#16a34a';
            } catch (err) {
                status.textContent = '✗ Chyba: ' + err.message;
                status.style.color = '#dc2626';
            }
        };
        reader.readAsText(file, 'UTF-8');
    });
}

function parseRecipeTxt(text) {
    const lines = text.replace(/\r\n/g, '\n').split('\n');

    let section = null;
    const ingredients = [];
    const steps = [];

    const set = (id, val) => {
        const el = document.getElementById(id);
        if (el && val !== undefined) el.value = val.trim();
    };

    for (const raw of lines) {
        const line = raw.trim();
        if (!line) continue;

        // Sekce
        if (/^ingredience\s*:/i.test(line)) { section = 'ing'; continue; }
        if (/^postup\s*:/i.test(line))       { section = 'steps'; continue; }

        // Klíče
        const m = line.match(/^([^:]+):\s*(.*)$/);
        if (m && section === null) {
            const key = m[1].trim().toLowerCase()
                .normalize('NFD').replace(/[̀-ͯ]/g, ''); // odstranění diakritiky
            const val = m[2].trim();
            if (key === 'nazev' || key === 'název')          set('name', val);
            else if (key === 'popis')                        set('description', val);
            else if (key === 'priprava' || key === 'příprava') set('prep_time', val);
            else if (key === 'vareni' || key === 'vaření')   set('cook_time', val);
            else if (key === 'porce')                        set('servings', val);
            continue;
        }

        if (section === 'ing')   ingredients.push(line.replace(/^[-*•]\s*/, ''));
        if (section === 'steps') steps.push(line.replace(/^\d+[.)]\s*/, ''));
    }

    if (ingredients.length) set('ingredients', ingredients.join('\n'));
    if (steps.length)       set('steps', steps.join('\n'));

    if (!document.getElementById('name').value) {
        throw new Error('Soubor neobsahuje "Název:"');
    }
}

/* ── Náhled názvu nahraného obrázku ── */
const imageUpload = document.getElementById('image_upload');
if (imageUpload) {
    imageUpload.addEventListener('change', function () {
        const label = document.getElementById('image-upload-name');
        if (this.files[0]) {
            label.textContent = '✓ ' + this.files[0].name;
            label.style.color = '#16a34a';
            // Vymaž ruční cestu – soubor má přednost
            const manualInput = document.getElementById('image');
            if (manualInput) manualInput.value = '';
        }
    });
}

/* ── Dropdown uživatelského menu ── */
const userMenuBtn  = document.getElementById('user-menu-btn');
const userDropdown = document.getElementById('user-dropdown');

if (userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        userDropdown.classList.toggle('open');
    });

    // Zavři dropdown kliknutím kamkoliv jinam
    document.addEventListener('click', function () {
        userDropdown.classList.remove('open');
    });

    // Nezavírej při kliknutí uvnitř dropdown
    userDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });
}

/* ── Hamburger menu ── */
const hamburger   = document.getElementById('hamburger');
const navigation  = document.getElementById('navigation');

if (hamburger && navigation) {
    hamburger.addEventListener('click', function () {
        hamburger.classList.toggle('open');
        navigation.classList.toggle('nav-open');
    });

    // Zavři menu při kliknutí na odkaz
    navigation.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            hamburger.classList.remove('open');
            navigation.classList.remove('nav-open');
        });
    });
}
