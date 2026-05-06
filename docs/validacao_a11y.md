# Validação de Acessibilidade — Portal BDLP

Este documento registra os procedimentos de validação automatizada de
acessibilidade aplicáveis ao portal e os resultados de cada execução.

## Padrões de referência

- **WCAG 2.0 nível AA** (Web Content Accessibility Guidelines)
- **eMAG 3.1** (Modelo de Acessibilidade em Governo Eletrônico)

## Ferramentas

| Ferramenta | Tipo | Como executar |
|---|---|---|
| Lighthouse (Chrome DevTools) | Manual, no browser | DevTools → aba Lighthouse → Accessibility |
| axe-core (DevTools extension ou CLI) | Manual ou semi-automatizado | `npx @axe-core/cli http://localhost:8000` |
| pa11y | CLI automatizado | `make a11y-check` (alvo no Makefile) |
| ASES (governo.gov.br) | Manual, online | Cole o HTML e gere relatório |

## Alvos a validar

13 páginas distintas:

1. `/` — Home
2. `/busca/` — Busca facetada (com e sem termo)
3. `/colecoes/` — Lista de coleções
4. `/colecao/<id>/` — Detalhe de coleção
5. `/documento/<code>/` — Detalhe de documento
6. `/sobre/` — Página institucional
7. `/transparencia/` — Página legal
8. `/acessibilidade/` — Página legal
9. `/politica-de-privacidade/` — Página legal
10. `/politica-de-cookies/` — Página legal
11. `/mapa-do-site/` — Página legal
12. `/fale-conosco/` — Página legal
13. `/admin/` (se aplicável) — não obrigatório (interno)

## Critérios de aceite

| # | Critério | Limite |
|---|---|---|
| 1 | Lighthouse Accessibility | ≥ 95 em todas as páginas |
| 2 | axe-core violações sérias/críticas | 0 |
| 3 | pa11y WCAG2AA erros | 0 |
| 4 | Navegação por teclado funcional | Todas as 13 páginas |
| 5 | Contraste de texto (cor/fundo) | ≥ 4.5:1 em todos os elementos textuais |
| 6 | Skip-links visíveis ao foco | Sim |
| 7 | Atalhos Alt+1..4 funcionais | Sim |

## Procedimento

```bash
# 1. Subir o portal
make up

# 2. Esperar containers ficarem saudáveis
make logs                 # aguardar até "Listening at: http://0.0.0.0:8000"

# 3. Smoke test manual em browser
#    Abrir cada uma das 13 URLs em http://localhost:8000/...
#    Confirmar HTTP 200 e ausência de erros JS no Console

# 4. pa11y automatizado
make a11y-check           # roda pa11y --standard WCAG2AA contra todas

# 5. Lighthouse manual
#    Chrome → DevTools → Lighthouse → Accessibility (Desktop e Mobile)
#    Anotar score e violações em cada página

# 6. axe-core manual
#    Chrome → DevTools → axe DevTools (extensão) → Scan ALL of my page
#    Ou via CLI: npx @axe-core/cli http://localhost:8000/
```

## Resultados das execuções

> Esta seção é preenchida em cada execução. Use o template abaixo.

### Execução 1 — `[YYYY-MM-DD]` (preencher após primeira validação)

**Ambiente**:
- Branch: `feat/identidade-visual-sp`
- Commit: `[hash]`
- Browser: Chrome [versão]

**Lighthouse (Acessibilidade)**:

| URL | Desktop | Mobile | Notas |
|---|---|---|---|
| `/` | TBD | TBD | — |
| `/busca/` | TBD | TBD | — |
| `/colecoes/` | TBD | TBD | — |
| `/sobre/` | TBD | TBD | — |
| `/transparencia/` | TBD | TBD | — |
| `/acessibilidade/` | TBD | TBD | — |
| `/politica-de-privacidade/` | TBD | TBD | — |
| `/politica-de-cookies/` | TBD | TBD | — |
| `/mapa-do-site/` | TBD | TBD | — |
| `/fale-conosco/` | TBD | TBD | — |

**axe-core (violações sérias/críticas)**:

| URL | Críticas | Sérias | Moderadas | Notas |
|---|---|---|---|---|
| `/` | TBD | TBD | TBD | — |

**pa11y (WCAG 2.0 AA)**:

```
Cole aqui a saída de `make a11y-check`
```

**Smoke test manual**:

- [ ] `/` carrega HTTP 200
- [ ] `/busca/` carrega HTTP 200 (sem termo)
- [ ] `/busca/?q=licitacao` retorna resultados
- [ ] `/colecoes/` lista coleções
- [ ] `/colecao/1/` detalha 1ª coleção
- [ ] `/documento/<code>/` exibe metadados
- [ ] `/sobre/` exibe conteúdo institucional
- [ ] `/transparencia/` exibe RASCUNHO
- [ ] `/acessibilidade/` exibe RASCUNHO
- [ ] `/politica-de-privacidade/` exibe RASCUNHO
- [ ] `/politica-de-cookies/` exibe RASCUNHO
- [ ] `/mapa-do-site/` lista hierarquia
- [ ] `/fale-conosco/` exibe canais
- [ ] Skip-links aparecem no Tab inicial
- [ ] Alt+1..4 funcionam (testar Alt+Shift no Linux/Win)
- [ ] Banner de cookies aparece sem `localStorage`
- [ ] Banner de cookies persiste após aceite
- [ ] Controles A+/A−/contraste persistem entre páginas

**Pendências e correções**:

- (preencher conforme as execuções)

## Histórico

- **`[YYYY-MM-DD]`** — Primeira execução completa após branch
  `feat/identidade-visual-sp` ser mergeada.
