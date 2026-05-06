# TODOs jurídicos e institucionais — Portal BDLP

Pendências identificadas durante a reforma de identidade visual que
**não bloqueiam a entrega técnica**, mas precisam ser resolvidas antes
do **lançamento público** do portal. Cada item indica o responsável
sugerido e a evidência atual.

---

## 1. Validação jurídica dos textos das 6 páginas legais

**Status**: ⛔ pendente
**Responsável sugerido**: Encarregado de Proteção de Dados (DPO) da
SGGD + Procuradoria Geral do Estado / Assessoria Jurídica da SGGD.
**Evidência atual**: cada página em
[portal/templates/legal/](../../portal/templates/legal/) abre com a
faixa amarela "RASCUNHO — sujeito a validação pelo Encarregado de
Dados (DPO) e pela Assessoria Jurídica" via partial
[_aviso_rascunho.html](../../portal/templates/_partials/_aviso_rascunho.html).

Páginas a validar:
- `transparencia.html` — estrutura LILP, Convênio, legislação aplicável.
- `acessibilidade.html` — declaração de conformidade eMAG/WCAG.
- `politica_privacidade.html` — bases legais, direitos do titular.
- `politica_cookies.html` — categorias, cookies utilizados, retenção.
- `fale_conosco.html` — canais e prazos.

**Como remover o aviso**: após validação, remover a inclusão de
`{% include "_partials/_aviso_rascunho.html" %}` no topo de cada
template.

---

## 2. Designação formal do DPO da SGGD

**Status**: ⛔ pendente
**Responsável sugerido**: Gabinete da SGGD.
**Evidência atual**:
[politica_privacidade.html](../../portal/templates/legal/politica_privacidade.html#L18-L22)
contém placeholder `[A SER PREENCHIDO — nome e contato do Encarregado
de Proteção de Dados Pessoais designado pela SGGD, conforme art. 41 da
LGPD.]` na seção "2. Encarregado de Proteção de Dados (DPO)".

Quando designado, atualizar:
1. Nome do DPO.
2. Forma de contato (e-mail dedicado ou telefone).
3. Data da designação (ato administrativo).

---

## 3. E-mail institucional do BDLP

**Status**: ⛔ pendente
**Responsável sugerido**: Setor de TI da SGGD.
**Evidência atual**: várias páginas referenciam
`mailto:lilp@sggd.sp.gov.br` como sugestão, mas o endereço precisa ser
provisionado:

- [_rodape_institucional.html](../../portal/templates/_partials/_rodape_institucional.html)
- [transparencia.html](../../portal/templates/legal/transparencia.html)
- [acessibilidade.html](../../portal/templates/legal/acessibilidade.html)
- [fale_conosco.html](../../portal/templates/legal/fale_conosco.html)
- [about.html](../../portal/templates/about.html)

**Sugestão**: criar `bdlp@sggd.sp.gov.br` ou `lilp@sggd.sp.gov.br`. Se
o endereço final for diferente, fazer search-and-replace.

---

## 4. SVG vetorial original do logo LILP

**Status**: 🟡 desejável (não impeditivo)
**Responsável sugerido**: equipe de design da SGGD que produziu o
Portfólio LILP 2026 v4.
**Evidência atual**:
[portal/static/img/logos/lilp/lilp.png](../../portal/static/img/logos/lilp/lilp.png)
é PNG raster 543×472, extraído do PDF do portfólio. Resolução adequada
para uso web atual, mas pixela em uso impresso ou em peças de larga
escala.

**Ação**: solicitar `.svg` ou `.ai` ao designer original.

---

## 5. Parceiros acadêmicos sem instrumento jurídico formal

**Status**: 🟢 organizado, não exibido
**Responsável sugerido**: gestão do LILP.
**Evidência atual**: logos da USP, UNESP, PNUD e versão alternativa
da Unicamp estão organizados em
[portal/static/img/logos/parceiros-academicos/](../../portal/static/img/logos/parceiros-academicos/),
mas **não são exibidos** em nenhum template. O Convênio SGGD nº
02/2025 só formaliza parceria com SBU/Unicamp.

**Ação**: se houver convênio futuro, atualizar
[_rodape_institucional.html](../../portal/templates/_partials/_rodape_institucional.html)
faixa de selos para incluir o respectivo logo.

---

## 6. Sitemap.xml programático

**Status**: 🟡 desejável (não impeditivo)
**Responsável sugerido**: equipe técnica.
**Evidência atual**: existe apenas o mapa do site HTML em
[mapa_site.html](../../portal/templates/legal/mapa_site.html). Robôs
de busca preferem `sitemap.xml`.

**Ação**: integrar `django.contrib.sitemaps` em iteração futura para
gerar `/sitemap.xml` com URLs de documentos e coleções.

---

## 7. Cláusula de uso de IA na Política de Privacidade

**Status**: 🟡 a avaliar
**Responsável sugerido**: DPO + comando técnico.
**Evidência atual**: o portal não usa IA em runtime. Porém o comando
de gestão `enrich_metadata` (em `portal/catalog/management/commands/`)
usa OpenAI para enriquecer metadados durante a ingestão. Se algum
dado pessoal de autores pessoa física for processado, deve ser
declarado em [politica_privacidade.html](../../portal/templates/legal/politica_privacidade.html).

**Ação**: levantar com o DPO se há tratamento de dados pessoais nas
chamadas à OpenAI; caso afirmativo, descrever a base legal e o
fornecedor (operador) na política.

---

## 8. Migração django-csp 3.x → 4.x

**Status**: 🟢 manutenção futura
**Responsável sugerido**: equipe técnica.
**Evidência atual**:
[pyproject.toml](../../pyproject.toml) pinou `django-csp>=3.8,<4.0`
porque a API CSP_* é mais documentada e estável. A versão 4.0 mudou
para `CONTENT_SECURITY_POLICY` dict.

**Ação**: quando 4.x estabilizar, atualizar pyproject.toml para
`>=4.0,<5.0` e migrar as configurações em
[settings.py](../../portal/portal/settings.py) para o formato dict.

---

## 9. Cookies de análise

**Status**: 🟢 reservado, não disparado
**Responsável sugerido**: gestão do LILP + DPO.
**Evidência atual**: o banner LGPD oferece a categoria "Análise de
uso", mas o portal não dispara nenhum cookie de analytics. O usuário
pode "consentir" mas nada é coletado.

**Ação**: se for decidido medir uso de forma agregada, instalar
**Matomo self-hosted** no domínio do Governo SP (com IP truncado)
respeitando o consentimento. Atualizar a tabela de cookies em
[politica_cookies.html](../../portal/templates/legal/politica_cookies.html).
**NÃO usar Google Analytics** ou similar — viola autonomia do dado
em portal governamental.

---

## 10. Endurecimento de CSP — remover `'unsafe-inline'` em styles

**Status**: 🟡 endurecimento futuro
**Responsável sugerido**: equipe técnica.
**Evidência atual**: `CSP_STYLE_SRC` em
[settings.py](../../portal/portal/settings.py) inclui `'unsafe-inline'`
por compatibilidade com `style=""` em formulários e CSRF tokens.

**Ação**: usar nonces (`django-csp` suporta) ou refatorar para não
ter inline styles. Avaliar custo/benefício.

---

## Como atualizar este arquivo

Quando um TODO for resolvido, **mantenha o item** mas troque o status
para ✅ e adicione a data e hash do commit que resolveu. Isso preserva
auditoria. Apenas remova itens que se tornarem obsoletos
(ex.: cookies de análise descartados definitivamente).
