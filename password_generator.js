document.addEventListener('DOMContentLoaded', function() {
    initPasswordGenerators();
});

function initPasswordGenerators() {
    const genIcons = document.querySelectorAll('.password-gen-icon');
    
    genIcons.forEach(icon => {
        icon.addEventListener('click', function(e) {
            e.stopPropagation();
            const ui = this.nextElementSibling;
            
            // Zapri vse ostale odprte generatorje
            document.querySelectorAll('.password-generator-ui').forEach(otherUi => {
                if (otherUi !== ui) otherUi.classList.remove('active');
            });
            
            ui.classList.toggle('active');
            
            if (ui.classList.contains('active')) {
                generateForUi(ui);
            }
        });
    });

    // Zapri ob kliku drugam
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.password-generator-ui') && !e.target.closest('.password-gen-icon')) {
            document.querySelectorAll('.password-generator-ui').forEach(ui => {
                ui.classList.remove('active');
            });
        }
    });

    // Slider logic
    const sliders = document.querySelectorAll('.gen-slider');
    sliders.forEach(slider => {
        slider.addEventListener('input', function() {
            const valSpan = this.nextElementSibling;
            valSpan.textContent = this.value;
            generateForUi(this.closest('.password-generator-ui'));
        });
    });

    // Radio logic
    const radios = document.querySelectorAll('.gen-option input');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            generateForUi(this.closest('.password-generator-ui'));
        });
    });

    // Apply button logic
    const applyBtns = document.querySelectorAll('.btn-gen-apply');
    applyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const ui = this.closest('.password-generator-ui');
            const container = ui.closest('.password-input-container');
            const input = container.querySelector('input');
            const generatedPass = ui.getAttribute('data-current-pass');
            
            if (input && generatedPass) {
                input.value = generatedPass;
                
                // Če je input tipa password in ima maskiranje v tabeli (čeprav tukaj gre za vnosna polja)
                // V registraciji in dodajanju so to navadni inputi, ki jih vidimo ob vpisu
                
                ui.classList.remove('active');
                
                // Sproži event 'input', če so kakšni listenerji (npr. za preverjanje ujemanja gesel)
                input.dispatchEvent(new Event('input'));
            }
        });
    });
}

function generateForUi(ui) {
    const length = parseInt(ui.querySelector('.gen-slider').value);
    const type = ui.querySelector('input[name^="gen-type"]:checked').value; // name^ da so unikatni po UI
    
    const password = generatePassword(length, type);
    ui.setAttribute('data-current-pass', password);
    
    const preview = ui.querySelector('.gen-preview');
    if (preview) {
        preview.textContent = password;
    }
}

function generatePassword(length, type) {
    const charsetLower = "abcdefghijklmnopqrstuvwxyz";
    const charsetUpper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const charsetNumbers = "0123456789";
    const charsetSymbols = "!@#$%^&*()_+~`|}{[]:;?><,./-=";
    
    let allChars = charsetLower + charsetUpper + charsetNumbers;
    if (type === 'all') {
        allChars += charsetSymbols;
    }
    
    let password = "";
    for (let i = 0; i < length; i++) {
        password += allChars.charAt(Math.floor(Math.random() * allChars.length));
    }
    
    // Zagotovi vsaj en znak iz vsake skupine za boljšo varnost
    // (preprost generator, lahko bi bil bolj kompleksen, a za demo zadošča)
    
    return password;
}
