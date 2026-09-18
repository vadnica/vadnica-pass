<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

//enkripcija
function encryptthis($data, $key = null) {
    if ($key === null) {
        $key = isset($_SESSION['dek']) ? $_SESSION['dek'] : ENCRYPTION_KEY;
    }
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}
//dekripcija
function decryptthis($data, $custom_key = null) {
    if ($custom_key === null) {
        $encryption_key_b64 = isset($_SESSION['dek']) ? $_SESSION['dek'] : ENCRYPTION_KEY;
        $encryption_key = base64_decode($encryption_key_b64);
    } else {
        $encryption_key = base64_decode($custom_key);
    }
    list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

/**
 * Pripravi varno geslo za prikaz v kartici
 */
function renderPasswordCard($row, $category) {
    $user = decryptthis($row['user']);
    $pass = decryptthis($row['pass']);
    $email = isset($row['email']) ? decryptthis($row['email']) : '';
    $url = isset($row['url']) ? $row['url'] : '';
    $card_number = !empty($row['card_number']) ? decryptthis($row['card_number']) : '';
    $card_pin = !empty($row['card_pin']) ? decryptthis($row['card_pin']) : '';
    $card_cvv = !empty($row['card_cvv']) ? decryptthis($row['card_cvv']) : '';
    $wifi_5g = !empty($row['wifi_5g']) ? decryptthis($row['wifi_5g']) : '';
    $wifi_2_4g = !empty($row['wifi_2_4g']) ? decryptthis($row['wifi_2_4g']) : '';
    $router_ip = !empty($row['router_ip']) ? decryptthis($row['router_ip']) : '';
    $auth_code = !empty($row['auth_code']) ? decryptthis($row['auth_code']) : '';
    $name = $row['name'];
    $id = $row['id'];
    $favicon = getFavicon($url);

    $delete_url = ($category == 'trash') ? "delete.php?del={$id}&cat=trash&perm=1" : "delete.php?del={$id}&cat={$category}";
    $delete_confirm = ($category == 'trash') ? __('confirm_permanent_delete') : __('confirm_delete');
    $delete_icon = ($category == 'trash') ? 'delete_forever' : 'delete';
    
    echo "
<div class='password-card' data-id='{$id}'>
    <div class='card-header'>
        <h3>" . htmlspecialchars($name) . "</h3>
        <div class='actions'>
            " . ($url ? "<img src='{$favicon}' alt='icon' class='website-icon-mini' onerror=\"this.style.display='none'\">" : "<span style='margin-right: 8px;'>🤔</span>") . "
            ";
    
    if ($category == 'trash') {
        echo "
            <a href='delete.php?restore={$id}&cat=trash' class='btn-edit' title='" . __('restore') . "'>
                <span class='material-icons-outlined'>restore</span>
            </a>";
    } else {
        echo "
            <a href='edit.php?id={$id}&cat={$category}' class='btn-edit' title='" . __('edit') . "'>
                <span class='material-icons-outlined'>edit</span>
            </a>";
    }
    
    echo "
            <a href='{$delete_url}' class='btn-delete' title='" . __('show_card_remove') . "' onclick='return confirm(\"{$delete_confirm}\")'>
                <span class='material-icons-outlined'>{$delete_icon}</span>
            </a>
        </div>
    </div>
    <div class='card-body'>
        <div class='field'>
            <label>" . __('show_card_username') . "</label>
            <div class='input-group'>
                <input type='text' value='" . htmlspecialchars($user) . "' readonly class='copyable'>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
        </div>
        <div class='field'>
            <label>" . __('show_card_password') . "</label>
            <div class='input-group'>
                <input type='password' value='*****' data-password='" . htmlspecialchars($pass) . "' readonly class='copyable password-field'>
                <button class='btn-toggle' title='" . __('show_card_show') . "'>" . __('show_card_show') . "</button>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
        </div>";

    if ($email != '') {
        echo "
        <div class='field'>
            <label>" . __('show_card_email') . "</label>
            <div class='input-group'>
                <input type='text' value='" . htmlspecialchars($email) . "' readonly class='copyable'>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
        </div>";
    }

    if ($url != '') {
        $display_url = strlen($url) > 30 ? substr($url, 0, 27) . '...' : $url;
        echo "
        <div class='field'>
            <label>" . __('show_card_web_adress') . "</label>
            <a href='" . htmlspecialchars($url) . "' target='_blank' class='url-link'>" . htmlspecialchars($display_url) . "</a>
        </div>";
    }

    if ($card_number != '') {
        echo "
        <div class='field'>
            <label>" . __('card_number') . "</label>
            <div class='input-group'>
                <input type='text' value='" . htmlspecialchars($card_number) . "' readonly class='copyable'>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
        </div>";
    }

    if ($card_pin != '') {
        echo "
        <div class='field'>
            <label>" . __('card_pin') . "</label>
            <div class='input-group'>
                <input type='password' value='*****' data-password='" . htmlspecialchars($card_pin) . "' readonly class='copyable password-field'>
                <button class='btn-toggle' title='" . __('show_card_show') . "'>" . __('show_card_show') . "</button>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
        </div>";
    }

    if ($card_cvv != '') {
        echo "
        <div class='field'>
            <label>" . __('card_cvv') . "</label>
            <div class='input-group'>
                <input type='password' value='*****' data-password='" . htmlspecialchars($card_cvv) . "' readonly class='copyable password-field'>
                <button class='btn-toggle' title='" . __('show_card_show') . "'>" . __('show_card_show') . "</button>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
        </div>";
    }

    if ($wifi_5g != '') {
        echo "
        <div class='field'>
            <label>" . __('wifi_5g') . "</label>
            <div class='input-group'>
                <input type='password' value='*****' data-password='" . htmlspecialchars($wifi_5g) . "' readonly class='copyable password-field'>
                <button class='btn-toggle' title='" . __('show_card_show') . "'>" . __('show_card_show') . "</button>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
        </div>";
    }

    if ($wifi_2_4g != '') {
        echo "
        <div class='field'>
            <label>" . __('wifi_2_4g') . "</label>
            <div class='input-group'>
                <input type='password' value='*****' data-password='" . htmlspecialchars($wifi_2_4g) . "' readonly class='copyable password-field'>
                <button class='btn-toggle' title='" . __('show_card_show') . "'>" . __('show_card_show') . "</button>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
        </div>";
    }

    if ($router_ip != '') {
        echo "
        <div class='field'>
            <label>" . __('router_ip') . "</label>
            <div class='input-group'>
                <input type='text' value='" . htmlspecialchars($router_ip) . "' readonly class='copyable'>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
        </div>";
    }

    if ($auth_code != '') {
        $totp_code = getTOTPCode($auth_code);
        echo "
        <div class='field'>
            <label>" . __('auth_code') . "</label>
            <div class='input-group'>
                <input type='text' value='" . $totp_code . "' readonly class='copyable totp-field' data-secret='" . htmlspecialchars($auth_code) . "'>
                <button class='btn-copy' title='" . __('show_card_copy') . "'>" . __('show_card_copy') . "</button>
            </div>
            <div class='totp-timer'><div class='totp-progress'></div></div>
        </div>";
    }

    if ($category == 'trash') {
        echo "
        <div class='field'>
            <label>Kategorija</label>
            <div class='input-group'>
                <input type='text' value='" . htmlspecialchars(__($row['category'])) . "' readonly>
            </div>
        </div>";
    }

    echo "
    </div>
</div>";
}

/**
 * Pridobi ikono spletne strani (favicon)
 */
function getFavicon($url) {
    if (empty($url)) return "";
    
    $domain = parse_url($url, PHP_URL_HOST);
    if (!$domain) {
        $domain = str_replace(['http://', 'https://'], '', $url);
        $domain = explode('/', $domain)[0];
    }
    $domain = strtolower($domain);
    
    // Ime brez TLD (npr. 'vadnica' iz 'vadnica.org')
    $parts = explode('.', $domain);
    $nameOnly = (count($parts) > 1) ? $parts[count($parts) - 2] : $parts[0];
    // Če je poddomena (npr. blog.vadnica.org), poskusimo najti del, ki ni 'www' ali 'blog'
    if (count($parts) > 2 && $parts[0] !== 'www') {
        $nameOnly = $parts[0] . '-' . $parts[1];
    }

    $extensions = ['png', 'jpg', 'ico', 'svg'];
    $folders = ['icons', 'img/icons'];

    foreach ($folders as $folder) {
        foreach ($extensions as $ext) {
            // Seznam možnih imen datotek
            $patterns = [
                $domain, // vadnica.org
                $nameOnly, // vadnica
                $domain . "-icon", // vadnica.org-icon
                $nameOnly . "-icon" // vadnica-icon
            ];

            foreach ($patterns as $pattern) {
                $localFile = "{$folder}/{$pattern}.{$ext}";
                if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $localFile)) {
                    return $localFile;
                }
            }
        }
    }

    return "https://icons.duckduckgo.com/ip3/{$domain}.ico";
}

/**
 * Pomožna funkcija za izris generatorja gesel
 */
function renderPasswordGenerator($uid) {
    return '
    <div class="password-gen-icon" title="' . __('gen_password') . '">
        <span class="material-icons-outlined">auto_fix_high</span>
    </div>
    <div class="password-generator-ui">
        <div class="gen-setting">
            <label>' . __('gen_length') . '</label>
            <div class="gen-slider-container">
                <input type="range" class="gen-slider" min="16" max="39" value="20">
                <span class="gen-length-val">20</span>
            </div>
        </div>
        <div class="gen-options">
            <label class="gen-option">
                <input type="radio" name="gen-type-' . $uid . '" value="all" checked>
                ' . __('gen_all_chars') . '
            </label>
            <label class="gen-option">
                <input type="radio" name="gen-type-' . $uid . '" value="alphanum">
                ' . __('gen_alphanum') . '
            </label>
        </div>
        <div class="gen-preview" style="background: var(--input-bg); padding: 8px; border-radius: 4px; 
        font-family: monospace; font-size: 0.8rem; word-break: break-all; margin-top: 5px; text-align: center; 
        color: var(--text-main); min-height: 2.4em; display: flex; align-items: center; justify-content: center;"></div>
        <button type="button" class="btn-primary btn-gen-apply">' . __('gen_add_btn') . '</button>
    </div>';
}

/**
 * Izriše vrstico v tabeli gesel
 */
function renderPasswordRow($row, $category) {
    $user = decryptthis($row['user']);
    $pass = decryptthis($row['pass']);
    $email = isset($row['email']) ? decryptthis($row['email']) : '';
    $url = isset($row['url']) ? $row['url'] : '';
    $card_number = !empty($row['card_number']) ? decryptthis($row['card_number']) : '';
    $card_pin = !empty($row['card_pin']) ? decryptthis($row['card_pin']) : '';
    $card_cvv = !empty($row['card_cvv']) ? decryptthis($row['card_cvv']) : '';
    $wifi_5g = !empty($row['wifi_5g']) ? decryptthis($row['wifi_5g']) : '';
    $wifi_2_4g = !empty($row['wifi_2_4g']) ? decryptthis($row['wifi_2_4g']) : '';
    $router_ip = !empty($row['router_ip']) ? decryptthis($row['router_ip']) : '';
    $auth_code = !empty($row['auth_code']) ? decryptthis($row['auth_code']) : '';
    $name = $row['name'];
    $id = $row['id'];
    $favicon = getFavicon($url);

    $delete_url = ($category == 'trash') ? "delete.php?del={$id}&cat=trash&perm=1" : "delete.php?del={$id}&cat={$category}";
    $delete_confirm = ($category == 'trash') ? __('confirm_permanent_delete') : __('confirm_delete');
    $delete_icon = ($category == 'trash') ? 'delete_forever' : 'delete';

    echo "
<tr data-id='{$id}'>
    <td class='td-icon'>
        " . ($url ? "<img src='{$favicon}' alt='icon' class='website-icon' onerror=\"this.style.display='none'\">" : "<span style='font-size: 24px;'>🤔</span>") . "
    </td>
    <td class='td-name' data-label='" . __('add_new_password_title') . "'><strong>" . htmlspecialchars($name) . "</strong></td>
    <td class='td-user' data-label='" . __('show_card_username') . "'>
        " . ($user ? "
        <div class='table-input-group'>
            <input type='text' value='" . htmlspecialchars($user) . "' readonly class='copyable-table'>
            <button class='btn-copy-mini' title='" . __('show_card_copy') . "'><span class='material-icons-outlined'>content_copy</span></button>
        </div>" : "") . "
    </td>
    <td class='td-pass' data-label='" . __('show_card_password') . "'>
        " . ($pass ? "
        <div class='table-input-group'>
            <input type='password' value='*****' data-password='" . htmlspecialchars($pass) . "' readonly class='copyable-table password-field'>
            <button class='btn-toggle-mini' title='" . __('show_card_show') . "'><span class='material-icons-outlined'>visibility</span></button>
            <button class='btn-copy-mini' title='" . __('show_card_copy') . "'><span class='material-icons-outlined'>content_copy</span></button>
        </div>" : "") . "
    </td>";

    if ($category == 'bank') {
        echo "
    <td class='td-card-num' data-label='" . __('card_number') . "'>
        " . ($card_number ? "
        <div class='table-input-group'>
            <input type='text' value='" . htmlspecialchars($card_number) . "' readonly class='copyable-table'>
            <button class='btn-copy-mini' title='" . __('show_card_copy') . "'><span class='material-icons-outlined'>content_copy</span></button>
        </div>" : "") . "
    </td>
    <td class='td-card-pin' data-label='" . __('card_pin') . "'>
        " . ($card_pin ? "
        <div class='table-input-group'>
            <input type='password' value='*****' data-password='" . htmlspecialchars($card_pin) . "' readonly class='copyable-table password-field'>
            <button class='btn-toggle-mini' title='" . __('show_card_show') . "'><span class='material-icons-outlined'>visibility</span></button>
            <button class='btn-copy-mini' title='" . __('show_card_copy') . "'><span class='material-icons-outlined'>content_copy</span></button>
        </div>" : "") . "
    </td>
    <td class='td-card-cvv' data-label='CVV'>
        " . ($card_cvv ? "
        <div class='table-input-group'>
            <input type='password' value='*****' data-password='" . htmlspecialchars($card_cvv) . "' readonly class='copyable-table password-field'>
            <button class='btn-toggle-mini' title='" . __('show_card_show') . "'><span class='material-icons-outlined'>visibility</span></button>
            <button class='btn-copy-mini' title='" . __('show_card_copy') . "'><span class='material-icons-outlined'>content_copy</span></button>
        </div>" : "") . "
    </td>";
    } elseif ($category == 'network') {
        echo "
    <td class='td-wifi-5g' data-label='" . __('wifi_5g') . "'>
        " . ($wifi_5g ? "
        <div class='table-input-group'>
            <input type='password' value='*****' data-password='" . htmlspecialchars($wifi_5g) . "' readonly class='copyable-table password-field'>
            <button class='btn-toggle-mini' title='" . __('show_card_show') . "'><span class='material-icons-outlined'>visibility</span></button>
            <button class='btn-copy-mini' title='" . __('show_card_copy') . "'><span class='material-icons-outlined'>content_copy</span></button>
        </div>" : "") . "
    </td>
    <td class='td-wifi-24g' data-label='" . __('wifi_2_4g') . "'>
        " . ($wifi_2_4g ? "
        <div class='table-input-group'>
            <input type='password' value='*****' data-password='" . htmlspecialchars($wifi_2_4g) . "' readonly class='copyable-table password-field'>
            <button class='btn-toggle-mini' title='" . __('show_card_show') . "'><span class='material-icons-outlined'>visibility</span></button>
            <button class='btn-copy-mini' title='" . __('show_card_copy') . "'><span class='material-icons-outlined'>content_copy</span></button>
        </div>" : "") . "
    </td>
    <td class='td-router' data-label='" . __('router_ip') . "'>" . ($router_ip ? htmlspecialchars($router_ip) : '') . "</td>";
    } elseif ($category == 'trash') {
        echo "
    <td class='td-email' data-label='" . __('show_card_email') . "'>" . ($email ? htmlspecialchars($email) : '') . "</td>
    <td class='td-category' data-label='Kategorija'>" . htmlspecialchars(__($row['category'])) . "</td>";
    } else {
        // Za email, stores, desktop, game, misc, net
        echo "
    <td class='td-email' data-label='" . __('show_card_email') . "'>" . ($email ? htmlspecialchars($email) : '') . "</td>";
        
        if ($category != 'game') {
            echo "
    <td class='td-url' data-label='" . __('show_card_web_adress') . "'>
        " . ($url ? "<a href='" . htmlspecialchars($url) . "' target='_blank' class='table-url-link'><span class='material-icons-outlined'>open_in_new</span></a>" : "❌") . "
    </td>";
        }
    }

    // Dodajanje TOTP stolpca za vse razen trash
    if ($category != 'trash') {
        echo "
    <td class='td-auth' data-label='" . __('auth_code') . "'>";
        
        if (!empty($auth_code)) {
            $totp_code = getTOTPCode($auth_code);
            echo "
        <div class='table-input-group'>
            <input type='text' value='" . $totp_code . "' readonly class='copyable-table totp-field' data-secret='" . htmlspecialchars($auth_code) . "'>
            <button class='btn-copy-mini' title='" . __('show_card_copy') . "'><span class='material-icons-outlined'>content_copy</span></button>
        </div>
        <div class='totp-timer'><div class='totp-progress'></div></div>";
        }
        
        echo "
    </td>";
    }

    echo "
    <td class='td-actions'>
        <div class='td-actions-inner'>";
    
    if ($category == 'trash') {
        echo "
            <a href='delete.php?restore={$id}&cat=trash' class='btn-edit-mini' title='" . __('restore') . "'>
                <span class='material-icons-outlined'>restore</span>
            </a>
            <div style='width: 10px;'></div>";
    } else {
        echo "
            <a href='edit.php?id={$id}&cat={$category}' class='btn-edit-mini' title='" . __('edit') . "'>
                <span class='material-icons-outlined'>edit</span>
            </a>
            <div style='width: 20px;'></div>";
    }
    
    echo "
            <a href='{$delete_url}' class='btn-delete-mini' title='" . __('show_card_remove') . "' onclick='return confirm(\"{$delete_confirm}\")'>
                <span class='material-icons-outlined'>{$delete_icon}</span>
            </a>
        </div>
    </td>
</tr>";
}

/**
 * Izriše postavko v seznamu gesel (sredinski panel)
 */
function renderPasswordListItem($row, $active = false) {
    $url = isset($row['url']) ? $row['url'] : '';
    $favicon = getFavicon($url);
    $activeClass = $active ? 'active' : '';
    
    echo "
    <div class='password-item {$activeClass}' data-id='{$row['id']}'>
        <div class='item-icon'>
            " . ($url ? "<img src='{$favicon}' alt='icon' class='website-icon-mini' onerror=\"this.style.display='none'\">" : "🤔") . "
        </div>
        <div class='item-info'>
            <div class='item-name'>" . htmlspecialchars($row['name']) . "</div>
            <div class='item-meta'>" . htmlspecialchars(decryptthis($row['user'])) . "</div>
        </div>
    </div>";
}
function sendActivationEmail($to, $link) {
    $mail = new PHPMailer(true);

    try {
        // Nastavitve strežnika
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = SMTP_PORT;
        
        $mail->CharSet = 'UTF-8';

        // Prejemniki
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to);

        // Vsebina
        $mail->isHTML(true);
        $mail->Subject = 'Aktivacija vašega SafePass računa';
        
        $body = "<h2>Pozdravljeni!</h2>";
        $body .= "<p>Hvala za registracijo v SafePass Manager. Za aktivacijo vašega računa kliknite na spodnji gumb:</p>";
        $body .= "<p><a href='{$link}' style='display:inline-block; padding:10px 20px; background-color:#3498db; color:white; text-decoration:none; border-radius:5px;'>Aktiviraj račun</a></p>";
        $body .= "<p>Če gumb ne deluje, kopirajte to povezavo v brskalnik:<br>{$link}</p>";
        $body .= "<p>Povezava je veljavna 10 minut.</p>";
        
        $mail->Body = $body;
        $mail->AltBody = "Hvala za registracijo. Aktivirajte račun tukaj: " . $link;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // V primeru napake (npr. napačni SMTP podatki) vrnemo false
        // Za produkcijo bi bilo dobro beležiti napake v log: mail->ErrorInfo
        return false;
    }
}

/**
 * Preveri TOTP kodo (2FA)
 */
function verifyTOTP($secret, $code) {
    if (empty($secret)) return true;
    
    // Trenutno časovno okno (30 sekund)
    $timeStep = floor(time() / 30);
    
    // Preverimo trenutno okno in eno okno nazaj/naprej za toleranco
    for ($i = -1; $i <= 1; $i++) {
        if (getTOTPCode($secret, $timeStep + $i) === $code) return true;
    }
    
    return false;
}

/**
 * Generira trenutno TOTP kodo
 */
function getTOTPCode($secret, $timeStep = null) {
    if (empty($secret)) return "";
    
    // Odstrani presledke in pretvori v velike črke
    $secret = str_replace(' ', '', strtoupper($secret));
    
    // Base32 abeceda
    $base32chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
    $base32charsFlipped = array_flip(str_split($base32chars));
    
    // Dekodiranje Base32 skrivnosti
    $secretBinary = "";
    $bytes = str_split($secret);
    $buffer = 0;
    $bufferSize = 0;
    
    foreach ($bytes as $b) {
        if (!isset($base32charsFlipped[$b])) continue;
        $buffer = ($buffer << 5) | $base32charsFlipped[$b];
        $bufferSize += 5;
        if ($bufferSize >= 8) {
            $bufferSize -= 8;
            $secretBinary .= chr(($buffer >> $bufferSize) & 0xFF);
        }
    }
    
    if ($timeStep === null) {
        $timeStep = floor(time() / 30);
    }
    
    // Pretvori čas v 8-bajtni binarni niz (big-endian)
    $timeBinary = pack('N', 0) . pack('N', $timeStep);
    
    // Izračunaj HMAC-SHA1
    $hash = hash_hmac('sha1', $timeBinary, $secretBinary, true);
    
    // Dinamično odrezanje (Dynamic Truncation)
    $offset = ord($hash[19]) & 0xf;
    $otp = (
        ((ord($hash[$offset+0]) & 0x7f) << 24) |
        ((ord($hash[$offset+1]) & 0xff) << 16) |
        ((ord($hash[$offset+2]) & 0xff) << 8) |
        (ord($hash[$offset+3]) & 0xff)
    ) % 1000000;
    
    return str_pad($otp, 6, '0', STR_PAD_LEFT);
}
