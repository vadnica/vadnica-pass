# vadnica-pass 💻 🔒

> **Privacy-focused password manager built with PHP & MySQL.**  
> **Geselnik z osredotočenostjo na zasebnost, napisan v PHP in MySQL.**

A zero-knowledge password manager that stores your passwords encrypted on the server. Even the administrator cannot see your passwords!  
Zero-knowledge geselnik, ki shranjuje gesla šifrirano na strežniku. Tudi administrator ne more videti tvojih gesel!

## ✨ Features | Funkcije

- **Zero-Knowledge Architecture** | **Arhitektura brez znanja**  
  Your master password is never sent to the server. All encryption happens in your browser.  
  Tvoje glavno geslo nikoli ne gre na strežnik. Vsa šifriranja se zgodijo v brskalniku.

- **AES-256 Encryption** | **AES-256 šifriranje**  
  All data is encrypted with a strong key derived from your master password.  
  Vsi podatki so šifrirani s ključem, izpeljanim iz tvojega glavnega gesla.

- **TOTP Support** | **Podpora za TOTP**  
  Store and generate 2FA codes directly in the manager.  
  Shrani in generiraj 2FA kode neposredno v geslniku.

- **Export to KeePass/KeePassXC** | **Izvoz v KeePass/KeePassXC**  
  Export your vault in XML/CSV format compatible with popular password managers.  
  Izvozi svoj sef v XML/CSV formatu, združljiv z znanimi urejevalniki gesel.

- **Dark/Light Mode** | **Temni/Svetli način**  
  Toggle between themes for comfortable viewing.  
  Preklapljanje med temami za udobno ogledovanje.

## 🚀 Installation | Namestitev

1. Clone this repository | Klonirajte ta repozitorij:
   ```bash
   git clone https://github.com/vadnica/vadnica-pass.git
   cd vadnica-pass

2. Copy config.example.php to config.php | Kopirajte config.example.php v config.php:
   cp config.example.php config.php

3. Edit config.php and fill in your database credentials | Uredite config.php in izpolnite podatke za bazo.

4. Run setup.php in your browser | Zaženite setup.php v brskalniku, da ustvarite tabele.

5. Register a new account | Registrirajte nov račun.

📄 License | Licenca

This project is licensed under the GNU Affero General Public License v3.0 (AGPL-3.0).
See LICENSE for details.
🤝 Contributing | Prispevanje

Contributions are welcome! Please read the guidelines before submitting pull requests.
Prispevki so dobrodošli! Preden pošljete pull request, preberite smernice.
🌐 Links | Povezave

    Live Demo | Živ demo: pass.vadnica.org
    Tutorial Site | Učna stran: vadnica.org
    Blog | Blog: blog.vadnica.org
