<div id="ctb" class="ctb" aria-live="polite">
    <div class="ctb-bubble-wrap">
        <button type="button" id="ctb-bubble" class="ctb-bubble" aria-label="Ouvrir l'assistant">
            <img src="{{ asset('assets/img/Avatar.webp') }}" alt="Assistant" class="ctb-bubble-img">
        </button>
        <span class="ctb-badge"><i class="fa-solid fa-comment"></i></span>
        <span class="ctb-bubble-tip">Assistant SAMA TERRITOIRE</span>
    </div>

    <div id="ctb-panel" class="ctb-panel" role="dialog" aria-label="Assistant SAMA TERRITOIRE" hidden>
        <div class="ctb-header">
            <div class="ctb-header-left">
                <img src="{{ asset('assets/img/Avatar.webp') }}" alt="Assistant" class="ctb-avatar-img">
                <div>
                    <div class="ctb-title">Assistant SAMA TERRITOIRE</div>
                    <div class="ctb-status"><span class="ctb-dot"></span> En ligne</div>
                </div>
            </div>
            <button type="button" id="ctb-close" class="ctb-close" aria-label="Fermer l'assistant">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="ctb-messages" class="ctb-messages">
            <div class="ctb-msg ctb-bot">
                <div class="ctb-bubble-bot">
                    Bonjour ! 👋 Je suis l'assistant de <strong>SAMA TERRITOIRE</strong>.<br>
                    Posez-moi une question sur le territoire de Kaolack : infrastructures, communes, secteurs, actualités…
                </div>
            </div>
        </div>

        <div class="ctb-input-row">
            <input type="text" id="ctb-input" class="ctb-input" placeholder="Tapez votre question…" maxlength="500" autocomplete="off">
            <button type="button" id="ctb-send" class="ctb-send" aria-label="Envoyer">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
    #ctb { position: fixed; bottom: 24px; right: 24px; z-index: 9999; font-family: 'DM Sans', sans-serif; }

    .ctb-bubble {
        width: 78px; height: 78px; border-radius: 50%;
        background: #1a5d3a; border: 3.5px solid #fff; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; padding: 0;
        box-shadow: 0 0 0 5px rgba(26, 93, 58, 0.35), 0 8px 26px rgba(26, 93, 58, 0.45);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        animation: ctb-pulse 2.5s infinite;
    }
    .ctb-bubble-img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .ctb-bubble:hover { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(26, 93, 58, 0.4), 0 10px 32px rgba(26, 93, 58, 0.5); }

    .ctb-bubble-wrap { position: relative; }
    .ctb-bubble-tip {
        position: absolute; right: calc(100% + 12px); top: 50%; transform: translateY(-50%);
        background: #1a5d3a; color: #fff; font-size: 13px; font-weight: 600; letter-spacing: 0.3px;
        padding: 8px 14px; border-radius: 10px; white-space: nowrap;
        box-shadow: 0 4px 14px rgba(26, 93, 58, 0.3);
        opacity: 0; visibility: hidden; transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .ctb-bubble-tip::after {
        content: ''; position: absolute; right: -6px; top: 50%; transform: translateY(-50%);
        border: 6px solid transparent; border-left-color: #1a5d3a;
    }
    .ctb-bubble-wrap:hover .ctb-bubble-tip { opacity: 1; visibility: visible; }

    .ctb-badge {
        position: absolute; top: -2px; right: -2px; width: 20px; height: 20px;
        background: #ffd166; border: 2.5px solid #fff; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; color: #1a5d3a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.18);
    }

    @keyframes ctb-pulse {
        0%, 100% { box-shadow: 0 0 0 5px rgba(26, 93, 58, 0.35), 0 8px 26px rgba(26, 93, 58, 0.45); }
        50% { box-shadow: 0 0 0 9px rgba(26, 93, 58, 0.15), 0 8px 26px rgba(26, 93, 58, 0.45); }
    }

    #ctb [hidden] { display: none !important; }

    .ctb-panel {
        position: fixed; bottom: 24px; right: 24px; width: 370px; max-width: calc(100vw - 32px);
        height: 520px; max-height: calc(100vh - 48px);
        background: #ffffff; border-radius: 18px; overflow: hidden;
        box-shadow: 0 12px 44px rgba(0, 0, 0, 0.18);
        display: flex; flex-direction: column;
        border: 1px solid rgba(26, 93, 58, 0.12);
        animation: ctb-pop 0.25s ease;
    }
    @keyframes ctb-pop {
        from { opacity: 0; transform: translateY(16px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .ctb-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px; background: #1a5d3a; color: #fff;
    }
    .ctb-header-left { display: flex; align-items: center; gap: 10px; }
    .ctb-avatar-img {
        width: 44px; height: 44px; border-radius: 50%; object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.4);
    }
    .ctb-title { font-weight: 700; font-size: 14px; letter-spacing: 0.2px; }
    .ctb-status { font-size: 11px; opacity: 0.85; display: flex; align-items: center; gap: 5px; }
    .ctb-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; display: inline-block; animation: ctb-blink 1.6s infinite; }
    @keyframes ctb-blink { 50% { opacity: 0.4; } }

    .ctb-close {
        background: rgba(255, 255, 255, 0.12); border: none; color: #fff;
        width: 30px; height: 30px; border-radius: 50%; cursor: pointer;
        display: flex; align-items: center; justify-content: center; font-size: 15px;
        transition: background 0.2s;
    }
    .ctb-close:hover { background: rgba(255, 255, 255, 0.28); }

    .ctb-messages {
        flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 10px;
        background: #f5f8f6;
    }
    .ctb-messages::-webkit-scrollbar { width: 5px; }
    .ctb-messages::-webkit-scrollbar-thumb { background: rgba(26, 93, 58, 0.25); border-radius: 4px; }

    .ctb-msg { display: flex; max-width: 85%; }
    .ctb-bot { align-self: flex-start; }
    .ctb-user { align-self: flex-end; }

    .ctb-bubble-bot, .ctb-bubble-user {
        padding: 10px 14px; border-radius: 14px; line-height: 1.5; font-size: 13.5px; word-break: break-word;
    }
    .ctb-bubble-bot {
        background: #ffffff; color: #22352b; border: 1px solid #e2ede7;
        border-bottom-left-radius: 4px;
    }
    .ctb-bubble-user {
        background: #1a5d3a; color: #fff;
        border-bottom-right-radius: 4px;
    }

    .ctb-typing { display: flex; gap: 4px; padding: 6px 2px; }
    .ctb-typing span {
        width: 7px; height: 7px; background: #1a5d3a; border-radius: 50%;
        animation: ctb-typing-bounce 1.2s infinite;
    }
    .ctb-typing span:nth-child(2) { animation-delay: 0.15s; }
    .ctb-typing span:nth-child(3) { animation-delay: 0.3s; }
    @keyframes ctb-typing-bounce { 0%, 60%, 100% { transform: translateY(0); opacity: 0.4; } 30% { transform: translateY(-4px); opacity: 1; } }

    .ctb-input-row { display: flex; gap: 8px; padding: 12px; border-top: 1px solid #eef3f0; background: #fff; }
    .ctb-input {
        flex: 1; border: 1px solid #dde8e1; border-radius: 24px; padding: 10px 16px;
        font-size: 13.5px; font-family: inherit; outline: none; transition: border-color 0.2s;
        background: #f7faf8;
    }
    .ctb-input:focus { border-color: #1a5d3a; background: #fff; }
    .ctb-send {
        width: 42px; height: 42px; border-radius: 50%; border: none; cursor: pointer;
        background: #1a5d3a; color: #fff; font-size: 15px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s, transform 0.15s;
    }
    .ctb-send:hover { background: #147a4a; }
    .ctb-send:active { transform: scale(0.92); }
    .ctb-send:disabled { background: #b6cec0; cursor: not-allowed; }

    @media (min-width: 601px) and (max-width: 1024px) {
        .ctb-panel {
            bottom: 24px; right: 24px; width: 500px; max-width: calc(100vw - 48px);
            height: 650px; max-height: calc(100vh - 48px);
        }
        .ctb-bubble { width: 86px; height: 86px; border: 4px solid #fff; }
        .ctb-title { font-size: 16px; }
        .ctb-status { font-size: 12px; }
        .ctb-avatar-img { width: 48px; height: 48px; }
        .ctb-bubble-bot, .ctb-bubble-user { font-size: 15px; padding: 12px 16px; }
        .ctb-input { font-size: 15px; padding: 12px 18px; }
        .ctb-send { width: 46px; height: 46px; font-size: 16px; }
        .ctb-close { width: 34px; height: 34px; font-size: 17px; }
    }

    @media (max-width: 600px) {
        #ctb { bottom: 20px; right: 20px; }
        .ctb-bubble { width: 62px; height: 62px; border: 3px solid #fff; }
        .ctb-bubble-tip { display: none; }
        .ctb-panel {
            bottom: 0; right: 0; width: 100%; max-width: 100%;
            height: 100%; max-height: 100vh; border-radius: 0; border: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var bubble = document.getElementById('ctb-bubble');
    var bubbleWrap = bubble ? bubble.closest('.ctb-bubble-wrap') : null;
    var panel = document.getElementById('ctb-panel');
    var closeBtn = document.getElementById('ctb-close');
    var messages = document.getElementById('ctb-messages');
    var input = document.getElementById('ctb-input');
    var sendBtn = document.getElementById('ctb-send');
    if (!bubble || !panel) return;

    var historique = [];

    function ouvrir() {
        panel.hidden = false;
        if (bubbleWrap) bubbleWrap.style.display = 'none';
        setTimeout(function () { input.focus(); }, 200);
    }

    function fermer() {
        panel.hidden = true;
        if (bubbleWrap) bubbleWrap.style.display = '';
    }

    bubble.addEventListener('click', ouvrir);
    closeBtn.addEventListener('click', fermer);

    function ajouterMessage(role, texte) {
        var div = document.createElement('div');
        div.className = 'ctb-msg ' + (role === 'user' ? 'ctb-user' : 'ctb-bot');
        var contenant = document.createElement('div');
        contenant.className = role === 'user' ? 'ctb-bubble-user' : 'ctb-bubble-bot';
        contenant.innerHTML = texte;
        div.appendChild(contenant);
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
        historique.push({ role: role === 'user' ? 'user' : 'assistant', texte: texte });
    }

    function afficherTyping() {
        var div = document.createElement('div');
        div.className = 'ctb-msg ctb-bot';
        var contenant = document.createElement('div');
        contenant.className = 'ctb-bubble-bot';
        contenant.innerHTML = '<div class="ctb-typing"><span></span><span></span><span></span></div>';
        div.appendChild(contenant);
        div.id = 'ctb-typing';
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
        return div;
    }

    function retirerTyping() {
        var t = document.getElementById('ctb-typing');
        if (t) t.remove();
    }

    function echapperHTML(texte) {
        var div = document.createElement('div');
        div.textContent = texte;
        return div.innerHTML;
    }

    function formaterReponse(texte) {
        texte = echapperHTML(texte);
        texte = texte.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
        texte = texte.replace(/`([^`\n]+)`/g, '<code>$1</code>');
        return texte.replace(/\n/g, '<br>');
    }

    function envoyer() {
        var texte = input.value.trim();
        if (!texte || sendBtn.disabled) return;
        input.value = '';
        ajouterMessage('user', echapperHTML(texte));
        var typing = afficherTyping();
        sendBtn.disabled = true;

        var token = null;
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) token = meta.content;

        fetch('{{ route('chatbot.message') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ message: texte, historique: historique.slice(-10) })
        })
        .then(function (reponse) {
            return reponse.json().then(function (data) {
                return { status: reponse.status, data: data };
            });
        })
        .then(function (resultat) {
            retirerTyping();
            var message = (resultat.data && resultat.data.response)
                ? resultat.data.response
                : "Je n'ai pas pu traiter votre demande pour le moment. Veuillez réessayer. 🙏";
            ajouterMessage('bot', formaterReponse(message));
        })
        .catch(function () {
            retirerTyping();
            ajouterMessage('bot', "L'assistant est momentanément indisponible. Vérifiez votre connexion et réessayez dans un instant. 🙏");
        })
        .finally(function () {
            sendBtn.disabled = false;
            input.focus();
        });
    }

    sendBtn.addEventListener('click', envoyer);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') envoyer();
    });
});
</script>