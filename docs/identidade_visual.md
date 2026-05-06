# Identidade Visual — Portal BDLP

Este documento registra as decisões de design e os fundamentos normativos
da reforma de identidade visual aplicada ao Portal da Biblioteca Digital
de Logística Pública (BDLP).

## 1. Fundamento normativo

A reforma observa os seguintes instrumentos:

| Norma | O que estabelece |
|---|---|
| Manual GESP v1.6 (abril/2023) | Identidade visual do Governo do Estado de São Paulo. |
| Decreto Estadual nº 67.799/2023 | Estratégia de Governo Digital de SP. |
| Decreto Estadual nº 69.056/2024 | Portal único SP.GOV.BR e barra de identidade unificada. |
| Resolução SGGD nº 38/2024 | Institui o LILP no âmbito da SGGD. |
| Convênio SGGD nº 02/2025 | Parceria SGGD ↔ SBU/Unicamp para construção do acervo. |
| eMAG 3.1 (Portaria nº 3 de 7/maio/2007 — SISP) | Modelo de Acessibilidade em Governo Eletrônico. |
| WCAG 2.0 AA (W3C/WAI) | Padrão internacional de acessibilidade web. |
| Lei nº 12.527/2011 (LAI) | Acesso à informação. |
| Lei nº 13.709/2018 (LGPD) | Tratamento de dados pessoais. |
| Lei nº 12.965/2014 (Marco Civil) | Princípios de uso da internet. |
| Lei nº 13.460/2017 | Direitos do usuário de serviços públicos. |

## 2. Paleta de cores

Definida em `:root` de [portal/static/css/style.css](../portal/static/css/style.css)
como CSS custom properties:

| Token | HEX | Pantone | Uso recomendado |
|---|---|---|---|
| `--sp-vermelho` | `#ED1C24` | 485 C | Destaques, CTAs, alertas, banner LAI (borda). |
| `--sp-amarelo` | `#FBB900` | 123 C | Outline de foco, indicador de página atual, banner de cookies. |
| `--sp-verde` | `#0B9247` | 347 C | Estado de sucesso, ODS. |
| `--sp-azul` | `#034EA2` | 2955 C | Cor institucional principal — header, links, botões primários. |
| `--sp-azul-escuro` | `#023A7A` | derivado | Hover do azul institucional. |
| `--sp-fundo-claro` | `#F5F5F5` | — | Fundo de seções alternadas. |
| `--sp-foco` | `#FFB800` | derivado | Outline de foco visível (3px). |

Tons neutros (branco, preto, cinza 25%, cinza 50%) e aliases legados
(`--primary`, `--accent`, `--gray-*`) preservam as classes existentes
(`.header`, `.btn-primary`, `.badge`) sem reescrita massiva.

## 3. Tipografia

- **Fonte principal**: Roboto (Google Fonts), pesos 400/500/700.
- **Fallback**: Verdana (especificada no Manual GESP para sistema), depois
  `system-ui` e Arial.
- **Carregamento**: `<link>` no `<head>` de [base.html](../portal/templates/base.html)
  com `preconnect` para `fonts.googleapis.com` e `fonts.gstatic.com` e
  `display=swap`.

Escala (rem, base 16px): h1 36px / h2 28px / h3 22px / h4 18px /
corpo 16px / legenda 14px. Line-height padrão 1.6 no corpo.

## 4. Grid e breakpoints

12 colunas, gutter 30px, container central com `max-width: 1320px`,
`margin-inline: auto` e `padding: 0 1.5rem`.

```
--bp-xs:  576px   (mobile)
--bp-sm:  768px   (tablet vertical)
--bp-md:  992px   (desktop pequeno)
--bp-lg:  1200px  (desktop)
--bp-xl:  1400px  (desktop grande)
```

Mobile-first: cada breakpoint adiciona, não substitui.

## 5. Arquitetura de templates

```
portal/templates/
├── base.html                       (orquestrador)
├── home.html
├── search.html  document_detail.html
├── collection_list.html  collection_detail.html
├── about.html
├── _partials/
│   ├── _icons_sprite.html
│   ├── _skip_links.html
│   ├── _barra_govsp.html
│   ├── _header_institucional.html
│   ├── _menu_principal.html
│   ├── _rodape_institucional.html
│   ├── _banner_lai.html
│   ├── _banner_cookies.html
│   └── _aviso_rascunho.html
└── legal/
    ├── transparencia.html  acessibilidade.html
    ├── politica_privacidade.html  politica_cookies.html
    ├── mapa_site.html  fale_conosco.html
```

Todos os partials começam com `_` (convenção de inclusão). Páginas
legais isoladas em `legal/` para facilitar inclusão do aviso de
rascunho compartilhado.

## 6. CSS em duas camadas

1. [portal/static/css/sp-design-system.css](../portal/static/css/sp-design-system.css)
   — componentes novos (prefixo `.sp-`).
2. [portal/static/css/style.css](../portal/static/css/style.css) — paleta,
   tipografia, classes legadas atualizadas.

A ordem de carregamento em `base.html` é `sp-design-system.css` ANTES
de `style.css`, para que regras de página tenham precedência em caso
de colisão.

## 7. JavaScript — `main.js` único

[portal/static/js/main.js](../portal/static/js/main.js) — IIFE única
sem build tooling, com 4 módulos nomeados:

- **A11y** — controles A+/A− e alto contraste, persistidos em
  localStorage (chaves `sp-a11y:fonte-escala` e `sp-a11y:contraste`).
- **Atalhos** — Alt+1..4 para foco em conteúdo/menu/busca/rodapé
  (eMAG R6.1).
- **Menu** — hambúrguer mobile com `aria-expanded`, ESC fecha,
  clique fora fecha, foco vai para primeiro link.
- **Cookies** — banner LGPD com persistência JSON em
  localStorage `sp-lgpd-consent` (versão, timestamp, categorias).

Carregado com `<script defer>`. Compatível com CSP `script-src 'self'`
(zero inline handlers — apenas `addEventListener`).

## 8. Acessibilidade

Implementadas as seguintes recomendações do eMAG 3.1:

- Skip-links (R3.1) para conteúdo, menu, busca e rodapé.
- Hierarquia semântica HTML5 (`<header>`, `<nav>`, `<main>`, `<article>`,
  `<section>`, `<footer>`) + roles ARIA explícitos quando necessário.
- `<html lang="pt-BR" dir="ltr">` em todas as páginas.
- `<title>` único e descritivo por página.
- Alt descritivo em todas as imagens informativas; `alt=""` em
  decorativas.
- Foco visível 3px amarelo (Pantone 123) com offset 2px — global.
- Atalhos Alt+1..4 padronizados (eMAG R6.1) com fallback JS para
  navegadores que não respeitam `accesskey` em `<main>`/`<nav>`.
- Modo alto contraste WCAG AAA (preto/amarelo/branco) ativável pela
  barra GovSP, persistido entre páginas.
- Escala de fonte ajustável (87.5% até 150%), persistida.
- Tabelas de dados com `<th scope>`, `<caption>`, `<thead>`, `<tbody>`.
- Sem inline event handlers — JS estritamente desplugado.
- Site navegável por teclado em todas as páginas (Tab, Shift+Tab, Enter,
  Espaço, ESC, setas em select).

## 9. LGPD e LAI

- **Banner LGPD** ([_banner_cookies.html](../portal/templates/_partials/_banner_cookies.html))
  com 3 ações (aceitar todos, apenas essenciais, personalizar) e modal
  com 3 categorias de cookies. Persistência em localStorage. Nenhum
  cookie de análise é disparado antes do consentimento explícito.
- **Banner LAI** ([_banner_lai.html](../portal/templates/_partials/_banner_lai.html))
  na home apontando para `/transparencia/` (Decreto Federal 7.724/2012,
  art. 7º).
- **6 páginas legais** em [portal/templates/legal/](../portal/templates/legal/)
  — todas com aviso amarelo de rascunho aguardando validação jurídica.

## 10. Headers de segurança

Em produção (`DEBUG=False`):

- Existentes: `SECURE_BROWSER_XSS_FILTER`, `SECURE_CONTENT_TYPE_NOSNIFF`,
  `SESSION_COOKIE_SECURE`, `CSRF_COOKIE_SECURE`, `SECURE_SSL_REDIRECT`,
  `SECURE_HSTS_*` (1 ano + subdomains + preload), `X_FRAME_OPTIONS=DENY`.
- Adicionados: `SECURE_REFERRER_POLICY=strict-origin-when-cross-origin`.
- **CSP via django-csp**: `default-src 'self'`, `img-src` libera
  `saopaulo.sp.gov.br`/`compras.sp.gov.br`/`data:`, `font-src` libera
  `fonts.gstatic.com`, `style-src` libera `fonts.googleapis.com`+
  `'unsafe-inline'`, `script-src 'self'` (sem unsafe-inline graças ao
  desenho de `main.js`), `frame-ancestors 'none'`.

## 11. Performance

- Imagens com `width`/`height` fixos (evita CLS).
- `loading="eager"` + `fetchpriority="high"` no logo da barra GovSP
  (acima da dobra).
- `loading="lazy"` em logos do rodapé e da página Sobre.
- `decoding="async"` em todas as PNGs.
- SVG sprite inline (`_icons_sprite.html`) — uma requisição para
  todos os ícones, herda `currentColor` (sem CSS extra).
- Total CSS+JS minificável ≤ 200KB (alvo).

## 12. Decisões de design por divergência

Algumas adaptações foram necessárias entre o prompt original e a
realidade do repositório:

| Item | Esperado | Decidido |
|---|---|---|
| App principal | `catalog/` na raiz | `portal/catalog/` (sem alteração estrutural). |
| Templates | `catalog/templates/` | `portal/templates/`. |
| Project Django | `bdlp/` | `portal/portal/`. |
| Template "sobre" | `sobre.html` | `about.html` (preservado para não quebrar `catalog:about`). |
| Template "coleções" | `collections.html` | `collection_list.html` (já existente). |

## 13. TODOs

Pendências documentadas em [docs/referencias/TODOS_JURIDICO.md](referencias/TODOS_JURIDICO.md):

- SVG vetorial original do logo LILP.
- Designação formal do DPO da SGGD.
- E-mail institucional do BDLP (`bdlp@sggd.sp.gov.br` é sugestão).
- Validação jurídica dos textos das 6 páginas legais.
- Parceiros acadêmicos (USP, UNESP, PNUD) — logos organizados mas não
  exibidos sem instrumento jurídico formal.
- Sitemap.xml programático via `django.contrib.sitemaps` (atualmente
  apenas `/mapa-do-site/` HTML).
- Migração django-csp 3.x → 4.x (API dict).
