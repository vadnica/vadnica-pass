document.addEventListener('DOMContentLoaded', function() {
    // Kopiranje v odložišče (Vanilla JS)
    document.querySelectorAll('.btn-copy, .btn-copy-mini').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const group = this.closest('.input-group') || this.closest('.table-input-group');
            const input = group ? group.querySelector('input') : this.parentNode.querySelector('input');
            
            if (input) {
                const realPassword = input.getAttribute('data-password');
                const textToCopy = realPassword || input.value;
                
                const performCopy = (text) => {
                    if (navigator.clipboard && window.isSecureContext) {
                        return navigator.clipboard.writeText(text);
                    } else {
                        // Fallback za execCommand
                        const originalValue = input.value;
                        const originalType = input.type;
                        if (realPassword) {
                            input.value = realPassword;
                            if (originalType === 'password') input.type = 'text';
                        }
                        input.select();
                        input.setSelectionRange(0, 99999);
                        const successful = document.execCommand('copy');
                        if (realPassword) {
                            input.value = originalValue;
                            if (originalType === 'password') input.type = 'password';
                        }
                        return successful ? Promise.resolve() : Promise.reject();
                    }
                };

                performCopy(textToCopy).then(() => {
                    // Vizualna povratna informacija
                    const icon = this.querySelector('.material-icons-outlined');
                    if (icon) {
                        const originalIcon = icon.textContent;
                        icon.textContent = 'check';
                        this.style.color = '#10b981';
                        setTimeout(() => {
                            icon.textContent = originalIcon;
                            this.style.color = '';
                        }, 2000);
                    } else {
                        const originalText = this.textContent;
                        this.textContent = 'Copied!';
                        this.classList.add('btn-success');
                        setTimeout(() => {
                            this.textContent = originalText;
                            this.classList.remove('btn-success');
                        }, 2000);
                    }
                }).catch(err => {
                    console.error('Napaka pri kopiranju:', err);
                });
            }
        });
    });

    // Pokaži/skrij geslo (Vanilla JS)
    document.querySelectorAll('.btn-toggle, .btn-toggle-mini').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const group = this.closest('.input-group') || this.closest('.table-input-group');
            const input = group ? group.querySelector('.password-field') : this.parentNode.querySelector('.password-field');
            
            if (input) {
                const icon = this.querySelector('.material-icons-outlined');
                const realPassword = input.getAttribute('data-password');

                if (input.type === 'password') {
                    input.type = 'text';
                    if (realPassword) {
                        input.value = realPassword;
                    }
                    if (icon) icon.textContent = 'visibility_off';
                    else this.textContent = 'Hide';
                } else {
                    input.type = 'password';
                    if (realPassword) {
                        input.value = '*****';
                    }
                    if (icon) icon.textContent = 'visibility';
                    else this.textContent = 'Show';
                }
            }
        });
    });

    // Sledenje obiskom (več kot 30 sekund)
    setTimeout(function() {
        const token = new URLSearchParams(window.location.search).get('_ijt');
        const url = 'count_visit.php' + (token ? '?_ijt=' + token : '');
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ duration: 31 })
        })
        .then(response => response.json())
        .then(data => console.log('Tracking:', data.status))
        .catch(err => console.error('Tracking error:', err));
    }, 30000);

    // TOTP osveževanje
    function updateTOTPCodes() {
        const totpFields = document.querySelectorAll('.totp-field');
        if (totpFields.length === 0) return;

        const token = new URLSearchParams(window.location.search).get('_ijt');
        
        totpFields.forEach(field => {
            const secret = field.getAttribute('data-secret');
            if (!secret) return;

            const url = 'get_totp.php?secret=' + encodeURIComponent(secret) + (token ? '&_ijt=' + token : '');
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    field.value = data.code;
                    
                    // Posodobi napredek odštevalnika
                    const container = field.closest('td') || field.closest('.field');
                    const progress = container ? container.querySelector('.totp-progress') : null;
                    if (progress) {
                        const percentage = (data.timeLeft / 30) * 100;
                        progress.style.transition = 'none';
                        progress.style.width = percentage + '%';
                        
                        // Sproži animacijo proti ničli
                        setTimeout(() => {
                            progress.style.transition = 'width ' + data.timeLeft + 's linear';
                            progress.style.width = '0%';
                        }, 50);
                    }
                })
                .catch(err => console.error('TOTP error:', err));
        });
    }

    // Zaženi takoj in nato na 30 sekund (oziroma ko poteče)
    if (document.querySelectorAll('.totp-field').length > 0) {
        updateTOTPCodes();
        
        // Namesto fiksnega intervala preverjamo vsako sekundo čas do konca okna
        setInterval(() => {
            const now = new Date();
            if (now.getSeconds() % 30 === 0) {
                updateTOTPCodes();
            }
        }, 1000);
    }

    // Vizualni učinek ob oddaji obrazcev (prijava, registracija, obnova)
    const authForm = document.querySelector('.auth-card form');
    if (authForm) {
        authForm.addEventListener('submit', function() {
            const btn = authForm.querySelector('button[type="submit"]');
            if (!btn) return;
            
            const alertDanger = document.querySelector('.alert-danger');
            if (alertDanger) {
                alertDanger.style.display = 'none';
            }
            
            const lang = document.documentElement.lang || 'sl';
            const text = lang === 'sl' ? 'Preverjanje...' : 'Checking...';
            
            // Posodobimo tekst gumba
            btn.textContent = text;
            btn.style.opacity = '0.7';
        });
    }

    // Funkcije za Recovery Modal (v register.php)
    window.copyRecoveryCode = function() {
        const codeElement = document.getElementById('recoveryCodeValue');
        if (!codeElement) return;
        
        const code = codeElement.innerText;
        navigator.clipboard.writeText(code).then(() => {
            const lang = document.documentElement.lang || 'sl';
            alert(lang === 'sl' ? 'Koda kopirana!' : 'Code copied!');
        });
    };

    window.closeRecoveryModal = function() {
        const modal = document.getElementById('modalRecovery');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    const recoveryModal = document.getElementById('modalRecovery');
    if (recoveryModal) {
        recoveryModal.addEventListener('click', function(e) {
            if (e.target === recoveryModal) {
                e.stopPropagation();
            }
        }, true);
    }

    // Avtomatsko formatiranje številke bančne kartice (4-4-4-4)
    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'card_number') {
            let value = e.target.value.replace(/\D/g, ''); // Odstrani vse razen številk
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            e.target.value = formattedValue.substring(0, 19); // Omeji na 16 številk + 3 presledke
        }
    });
});
