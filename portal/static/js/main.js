/*
 * Portal Biblioteca Digital de Logística Pública (BDLP)
 * Laboratório de Inovação em Logística Pública (LILP) | SGGD
 *
 * main.js — controles do lado cliente:
 *   - A11y:    fonte +/-, alto contraste, persistência localStorage
 *   - Atalhos: Alt+1..4 navegando para conteúdo/menu/busca/rodapé
 *   - Menu:    hambúrguer mobile com aria-expanded e ESC fecha
 *   - Cookies: banner LGPD (carregado em commit posterior)
 *
 * Conformidade: eMAG 3.1 (R6.1 atalhos, R5.4 foco visível),
 *               WCAG 2.0 AA (1.4.3 contraste, 1.4.4 redimensionar texto),
 *               Lei 13.709/2018 (LGPD — cookies).
 *
 * Arquivo único IIFE — sem build tooling. Compatível com CSP
 * script-src 'self' (sem inline handlers, apenas addEventListener).
 */

(function () {
    'use strict';

    /* ========================================================
       Constantes e estado
       ======================================================== */
    var STORAGE_KEYS = {
        contraste: 'sp-a11y:contraste',
        fonte:     'sp-a11y:fonte-escala',
        cookies:   'sp-lgpd-consent'
    };

    var FONTE_MIN = -2;  // escala mínima (12.5% menor)
    var FONTE_MAX = 4;   // escala máxima (50% maior)

    /* ========================================================
       Módulo: Acessibilidade (controles de fonte e contraste)
       ======================================================== */
    var A11y = {
        init: function () {
            this.applyStoredPrefs();
            this.bindFonteControls();
            this.bindContrasteControl();
        },

        bindFonteControls: function () {
            var btns = document.querySelectorAll('[data-a11y-action="font-up"], [data-a11y-action="font-down"]');
            for (var i = 0; i < btns.length; i++) {
                btns[i].addEventListener('click', this.onFontClick.bind(this));
            }
        },

        bindContrasteControl: function () {
            var btn = document.querySelector('[data-a11y-action="contrast"]');
            if (!btn) return;
            btn.addEventListener('click', this.onContrasteClick.bind(this));
        },

        onFontClick: function (event) {
            var action = event.currentTarget.getAttribute('data-a11y-action');
            var atual = this.getFonteEscala();
            var novo = action === 'font-up' ? atual + 1 : atual - 1;
            if (novo < FONTE_MIN) novo = FONTE_MIN;
            if (novo > FONTE_MAX) novo = FONTE_MAX;
            this.setFonteEscala(novo);
        },

        onContrasteClick: function (event) {
            var btn = event.currentTarget;
            var ativo = document.body.classList.toggle('sp-alto-contraste');
            btn.setAttribute('aria-pressed', ativo ? 'true' : 'false');
            try {
                localStorage.setItem(STORAGE_KEYS.contraste, ativo ? '1' : '0');
            } catch (e) { /* localStorage indisponível */ }
        },

        getFonteEscala: function () {
            try {
                var v = parseInt(localStorage.getItem(STORAGE_KEYS.fonte) || '0', 10);
                return isNaN(v) ? 0 : v;
            } catch (e) {
                return 0;
            }
        },

        setFonteEscala: function (escala) {
            try {
                localStorage.setItem(STORAGE_KEYS.fonte, String(escala));
            } catch (e) { /* localStorage indisponível */ }
            this.applyFonteEscala(escala);
        },

        applyFonteEscala: function (escala) {
            var html = document.documentElement;
            // Limpa classes anteriores
            for (var i = FONTE_MIN; i <= FONTE_MAX; i++) {
                if (i > 0) html.classList.remove('sp-fonte-aumentada-' + i);
                if (i < 0) html.classList.remove('sp-fonte-diminuida-' + Math.abs(i));
            }
            if (escala > 0) html.classList.add('sp-fonte-aumentada-' + escala);
            if (escala < 0) html.classList.add('sp-fonte-diminuida-' + Math.abs(escala));
        },

        applyStoredPrefs: function () {
            // Fonte
            this.applyFonteEscala(this.getFonteEscala());
            // Contraste
            try {
                var contraste = localStorage.getItem(STORAGE_KEYS.contraste) === '1';
                if (contraste) {
                    document.body.classList.add('sp-alto-contraste');
                    var btn = document.querySelector('[data-a11y-action="contrast"]');
                    if (btn) btn.setAttribute('aria-pressed', 'true');
                }
            } catch (e) { /* ignora */ }
        }
    };

    /* ========================================================
       Módulo: Atalhos de teclado (eMAG R6.1)
       Alt+1: conteúdo principal
       Alt+2: menu principal
       Alt+3: busca
       Alt+4: rodapé
       Implementado via JS além do accesskey HTML porque alguns
       browsers não respeitam accesskey em <main>/<nav>/<footer>.
       ======================================================== */
    var Atalhos = {
        init: function () {
            document.addEventListener('keydown', this.onKey.bind(this));
        },

        onKey: function (event) {
            // Alt + dígito (sem Ctrl/Meta para não conflitar com encurtadores do browser)
            if (!event.altKey || event.ctrlKey || event.metaKey) return;
            var alvo = null;
            switch (event.key) {
                case '1': alvo = document.getElementById('sp-conteudo'); break;
                case '2': alvo = document.getElementById('sp-menu'); break;
                case '3': alvo = document.getElementById('sp-busca-input') || document.getElementById('sp-busca'); break;
                case '4': alvo = document.getElementById('sp-rodape'); break;
                default: return;
            }
            if (!alvo) return;
            event.preventDefault();
            // Se o alvo não é tabable nativamente, garante tabindex temporário
            if (!alvo.hasAttribute('tabindex')) {
                alvo.setAttribute('tabindex', '-1');
            }
            alvo.focus();
            alvo.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    /* ========================================================
       Módulo: Menu hambúrguer mobile
       Toggle com aria-expanded; ESC fecha; clique fora fecha;
       primeiro link recebe foco ao abrir.
       ======================================================== */
    var Menu = {
        init: function () {
            this.toggleBtn = document.querySelector('.sp-menu-principal__toggle');
            this.container = document.querySelector('.sp-menu-principal');
            this.lista = document.getElementById('sp-menu-lista');
            if (!this.toggleBtn || !this.container || !this.lista) return;

            this.toggleBtn.addEventListener('click', this.toggle.bind(this));
            document.addEventListener('keydown', this.onKey.bind(this));
            document.addEventListener('click', this.onDocClick.bind(this));
        },

        abrir: function () {
            this.container.setAttribute('data-aberto', 'true');
            this.toggleBtn.setAttribute('aria-expanded', 'true');
            this.toggleBtn.setAttribute('aria-label', 'Fechar menu de navegação');
            var primeiro = this.lista.querySelector('a');
            if (primeiro) primeiro.focus();
        },

        fechar: function (devolverFoco) {
            this.container.removeAttribute('data-aberto');
            this.toggleBtn.setAttribute('aria-expanded', 'false');
            this.toggleBtn.setAttribute('aria-label', 'Abrir menu de navegação');
            if (devolverFoco) this.toggleBtn.focus();
        },

        toggle: function () {
            var aberto = this.container.getAttribute('data-aberto') === 'true';
            if (aberto) {
                this.fechar(false);
            } else {
                this.abrir();
            }
        },

        onKey: function (event) {
            if (event.key === 'Escape' && this.container.getAttribute('data-aberto') === 'true') {
                this.fechar(true);
            }
        },

        onDocClick: function (event) {
            if (this.container.getAttribute('data-aberto') !== 'true') return;
            if (this.container.contains(event.target)) return;
            this.fechar(false);
        }
    };

    /* ========================================================
       Inicialização
       ======================================================== */
    function init() {
        try { A11y.init(); }    catch (e) { console.error('[BDLP] A11y init falhou:', e); }
        try { Atalhos.init(); } catch (e) { console.error('[BDLP] Atalhos init falhou:', e); }
        try { Menu.init(); }    catch (e) { console.error('[BDLP] Menu init falhou:', e); }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
