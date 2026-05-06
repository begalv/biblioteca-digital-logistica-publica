# PACOTE DE LOGOS E REFERÊNCIAS — PORTAL BDLP

Este pacote reúne os arquivos visuais e os materiais de referência oficiais necessários para que o Claude Code aplique a identidade visual do Governo do Estado de São Paulo ao portal Django da **Biblioteca Digital de Logística Pública (BDLP)**.

Todos os arquivos aqui foram baixados de fontes públicas oficiais entre os domínios `saopaulo.sp.gov.br`, `sggd.sp.gov.br`, `compras.sp.gov.br`, `unicamp.br`, `sbu.unicamp.br` e `sigam.ambiente.sp.gov.br`. As URLs de origem estão registradas em cada seção abaixo, junto com observações técnicas (dimensões, formato, qualidade) que ajudam a decidir o uso correto de cada arquivo.

---

## 1. Pasta `governo-sp/` — Marca do Governo do Estado de SP

A marca do Governo SP é a peça gráfica mais importante do portal porque é o que comunica oficialidade institucional ao visitante. Esta pasta contém duas versões da mesma marca (a "GOV.SP.BR" com o balão de diálogo vermelho típico da identidade pós-2023), em PNG transparente de baixa resolução, conforme oferecidas no diretório oficial `saopaulo.sp.gov.br/barra-govsp/img/`.

| Arquivo | Dimensões | Origem | Uso recomendado |
|---|---|---|---|
| `logo-governo-sp-branco.png` | 206×38 px | `https://saopaulo.sp.gov.br/barra-govsp/img/logo-governo-do-estado-sp.png` | Barra superior preta do portal (a barra GovSP), onde o logo aparece em branco sobre fundo escuro |
| `logo-governo-sp-rodape.png` | 206×38 px | `https://saopaulo.sp.gov.br/barra-govsp/img/logo-rodape-governo-do-estado-sp.png` | Rodapé do portal, na faixa final preta |

**Aviso técnico importante:** essas duas imagens têm apenas 206×38 pixels, o que é ótimo para a barra de identidade no topo do portal (onde a altura visual é de cerca de 32 pixels), mas **não escala bem** se o Claude Code tentar usá-las em tamanho maior — ficarão pixeladas. Se você precisar de versões em alta resolução ou em SVG vetorial, peça ao setor de comunicação interno da SGGD acesso ao kit oficial completo de marcas, ou solicite à Secretaria Especial de Comunicação (SECOM) por meio do canal `https://www.comunicacao.sp.gov.br/secom/publicidade/materiais/manuais`. Para a maior parte dos usos previstos no portal (barra superior e rodapé), o que está aqui já basta.

---

## 2. Pasta `sggd/` — Marca do site da SGGD

A SGGD não publica um logotipo próprio com isenção do logotipo do Governo SP. O que ela usa em seu site oficial `sggd.sp.gov.br` é justamente o logotipo "SP.GOV.BR" do Governo, complementado pelo nome textual "Secretaria de **Gestão e Governo Digital**" em tipografia. Essa é uma escolha consciente da nova identidade unificada (Decreto 69.056/2024): as secretarias não têm marcas próprias, e sim assinam com o logotipo do Governo SP somado ao nome da pasta em texto.

| Arquivo | Dimensões | Origem | Uso recomendado |
|---|---|---|---|
| `logo-gov-branco.png` | 90×12 px | `https://www.sggd.sp.gov.br/statics/img/logo-gov-branco.png` | Header escuro do site SGGD (versão muito pequena, só serve para barras compactas) |
| `logo-gov.png` | 206×29 px | `https://www.sggd.sp.gov.br/statics/img/logo-gov.png` | Header claro do site SGGD (mostra a marca SP.GOV.BR em preto) |
| `logo-footer.png` | 272×60 px | `https://www.sggd.sp.gov.br/statics/img/logo-footer.png` | Rodapé do site SGGD (composição maior com o logo + nome da Secretaria) |

A maneira correta de assinar a SGGD na bio do header do portal BDLP, portanto, é usar o `logo-gov.png` ou (preferencialmente) `logo-footer.png` acompanhado da composição textual **"Governo do Estado de São Paulo / Secretaria de Gestão e Governo Digital"** em tipografia Roboto ou Verdana, conforme o Manual GESP. O Claude Code já está instruído a fazer essa composição via HTML/CSS no prompt que entreguei anteriormente.

---

## 3. Pasta `lilp/` — Logo do Laboratório de Inovação em Logística Pública

Esta foi a descoberta mais valiosa da busca. O LILP **possui sim** um logotipo gráfico próprio, e ele é bonito: um erlenmeyer (frasco de laboratório) preto e branco em destaque, com elementos vermelhos de rede neural pairando sobre ele, simbolizando a junção entre experimentação científica (laboratório) e inovação tecnológica (rede). Abaixo da figura, em tipografia bold, lê-se "LABORATÓRIO DE INOVAÇÃO EM LOGÍSTICA PÚBLICA - ESTADO DE SÃO PAULO".

Esse logo não está disponível como arquivo isolado em nenhum diretório público que eu tenha encontrado. Eu o extraí da capa do **Portfólio Oficial do LILP, edição 2026, versão 4**, que é um documento institucional publicado pela própria SGGD em fevereiro de 2026 e está hospedado em `https://compras.sp.gov.br/wp-content/uploads/2026/02/Portfolio-Laboratorio-Inovacao-2026_V4-1.pdf`.

| Arquivo | Dimensões | Observação |
|---|---|---|
| `logo-lilp-original.png` | 543×472 px | Logo extraído da capa do Portfólio LILP 2026 v4. Resolução suficiente para uso no header e rodapé do portal, mas para impressão ou banners gigantes seria ideal pedir uma versão vetorial à equipe que produziu o portfólio |
| `capa-portfolio-referencia.jpg` | A4 a 150 DPI | Captura visual da capa inteira do portfólio, para o Claude Code referenciar como exemplo do uso correto da marca em conjunto com o logotipo do Governo SP |

**Recomendação prática:** este logo extraído já cumpre bem o seu papel no portal. Mas como o LILP é o "dono" funcional do projeto BDLP, vale a pena, em paralelo, abrir um pedido formal à equipe que produziu o portfólio (provavelmente da Subsecretaria de Gestão da SGGD ou de uma agência contratada) para te repassar o **arquivo SVG original** ou ao menos um PNG em resolução superior a 2000×2000 pixels. Essa é uma diligência institucional simples que evita que você dependa de extração de PDF para um asset central.

---

## 4. Pasta `sbu-unicamp/` — Marcas da parceria acadêmica

Como o Convênio SGGD nº 02/2025 firma o SBU/Unicamp como parceiro técnico-consultivo do projeto BDLP, é fundamental que ambas as marcas (SBU e Unicamp) apareçam no portal — provavelmente no rodapé ou na página "Sobre", em uma régua de logos institucionais.

| Arquivo | Dimensões | Formato | Origem |
|---|---|---|---|
| `sbu-positivo.png` | 1808×1040 px | PNG colormap | `https://www.sbu.unicamp.br/wp-content/uploads/sites/85/2024/12/SBU_Transparente.png` |
| `sbu-negativo.png` | 1583×841 px | PNG RGBA | `https://www.sbu.unicamp.br/wp-content/uploads/sites/85/2026/03/SBU_Transparente_Branco.png` |
| `unicamp-positivo.svg` | vetorial | SVG nativo | `https://www.unicamp.br/wp-content/themes/bx-unicamp-multisite/assets/img/logo-unicamp.svg` |
| `unicamp-negativo.svg` | vetorial | SVG nativo | `https://www.unicamp.br/wp-content/themes/bx-unicamp-multisite/assets/img/logo-unicamp-fundo-escuro.svg` |
| `unicamp-icone-512.png` | 192×192 px | PNG quadrado | `https://www.unicamp.br/wp-content/uploads/sites/33/2024/03/cropped-logo_unicamp_512-192x192.png` |
| `unicamp-60-anos.png` | 134×80 px | PNG transparente | `https://www.sbu.unicamp.br/wp-content/uploads/sites/85/2026/03/selo-60-anos-unicamp-conjunto-preferencial.png` |

A marca do **SBU** veio em ótima resolução, com o logotipo completo "SBU - SISTEMA DE BIBLIOTECAS DA UNICAMP" e o elemento gráfico do átomo. Use a versão positiva para fundos claros e a negativa para fundos escuros.

A marca da **Unicamp** veio em SVG vetorial, o que é o melhor formato possível: escala sem perda de qualidade para qualquer tamanho. Aqui acontece uma coincidência curiosa que vai te economizar trabalho de paleta: o vermelho da Unicamp é exatamente `#ED1C24`, o mesmo Pantone 485 que o vermelho oficial do Governo SP, então as marcas vão dialogar cromaticamente sem esforço.

O **selo dos 60 anos da Unicamp** é um adereço opcional, mas se a parceria for divulgada com destaque na página "Sobre" o selo enriquece a comunicação visual e demonstra contemporaneidade.

---

## 5. Pasta `parceiros-academicos/` — Logos adicionais do ecossistema LILP

Esses arquivos vieram da página "Laboratório de Logística" do Portal de Compras e indicam que o LILP mantém parcerias com outras instituições além da Unicamp. Eles estão aqui apenas para referência — você decide com a coordenação do projeto se devem aparecer no portal BDLP ou se é uma comunicação restrita ao Portal de Compras.

| Arquivo | O que é | Quando usar |
|---|---|---|
| `unicamp-alternativo.jpg` | Logo Unicamp em formato JPG (versão alternativa) | Caso o SVG cause problema técnico em algum navegador antigo |
| `unesp.jpg` | Logo da Universidade Estadual Paulista | Apenas se a Unesp aparecer formalmente como parceira do BDLP |
| `usp.jpg` | Logo da Universidade de São Paulo | Apenas se a USP aparecer formalmente como parceira do BDLP |
| `pnud.jpg` | Logo do Programa das Nações Unidas para o Desenvolvimento | Apenas se o PNUD aparecer formalmente como parceiro do BDLP |

A regra de ouro aqui é **não inflacionar a régua de parceiros institucionais com logos sem vinculação formal ao BDLP**, porque isso pode gerar mal-entendido jurídico. O Convênio SGGD nº 02/2025 menciona apenas SGGD ↔ SBU/Unicamp; outras parcerias precisam de instrumento próprio para serem comunicadas como tal.

---

## 6. Pasta `compras-sp/` — Marca do Portal de Compras (referência visual)

O arquivo `logo-portal-compras.png` (132×42 pixels) é a logo do portal `compras.sp.gov.br` que você indicou como referência visual. Não é para usar no portal BDLP — está aqui apenas como **referência cromática e tipográfica** para o Claude Code consultar quando precisar tomar decisões de coerência com o ecossistema visual da SGGD.

---

## 7. Pasta `icones-redes-sociais/` — Ícones para a barra superior

Sete arquivos PNG de 26×25 pixels cada, todos baixados do diretório oficial `saopaulo.sp.gov.br/barra-govsp/img/`. São pequenos por design — a barra superior tem apenas cerca de 32 pixels de altura, então ícones grandes seriam excessivos.

| Arquivo | Plataforma | URL oficial associada |
|---|---|---|
| `flickr.png` | Flickr | `https://www.flickr.com/governosp/` |
| `linkedin.png` | LinkedIn | `https://www.linkedin.com/company/governosp/` |
| `tiktok.png` | TikTok | `https://www.tiktok.com/@governosp` |
| `youtube.png` | YouTube | `https://www.youtube.com/governosp/` |
| `twitter.png` | Twitter/X | `https://www.twitter.com/governosp/` |
| `instagram.png` | Instagram | `https://www.instagram.com/governosp/` |
| `facebook.png` | Facebook | `https://www.facebook.com/governosp/` |

Como já recomendei no prompt que te entreguei, **uma alternativa tecnicamente superior é o Claude Code usar SVGs inline da biblioteca Lucide ou Heroicons** (livres, leves, escaláveis, modernos), o que evita dependência de PNGs externos e melhora performance. Esses arquivos ficam aqui como plano B caso você prefira manter total fidelidade visual com `compras.sp.gov.br` e o site do Governo SP.

---

## 8. Pasta `icones-acessibilidade/` — Ícones de controles de acessibilidade

Quatro arquivos PNG de aproximadamente 24×23 pixels cada, também do diretório oficial `saopaulo.sp.gov.br/barra-govsp/img/`. Eles formam o conjunto canônico de controles de acessibilidade que aparece na barra superior de todos os portais do Governo SP.

| Arquivo | Função | Comportamento esperado |
|---|---|---|
| `aumentar-fonte.png` | "A+" — Aumentar fonte | Botão que soma 12,5% ao tamanho base do `<html>` |
| `diminuir-fonte.png` | "A−" — Diminuir fonte | Botão que subtrai 12,5% do tamanho base do `<html>` |
| `contraste.png` | Alto contraste | Toggle que aplica paleta amarelo/preto/branco WCAG AAA |
| `comunicar-erro.png` | Comunicar erro de acessibilidade | Link para `https://www.saopaulo.sp.gov.br/fale-conosco/comunicar-erros/` |

A mesma observação dos ícones de redes sociais vale aqui: o Claude Code pode preferir usar SVGs do Lucide/Heroicons. Mas se você quiser fidelidade visual absoluta ao padrão sp.gov.br, esses arquivos servem.

---

## 9. Pasta `manuais/` — Documentos de referência

Esses dois PDFs são os documentos institucionais primários que dão fundamento normativo e descritivo a toda a identidade visual do projeto. **Anexe ambos ao repositório** em `docs/referencias/` (caminho que o prompt já prevê) para que o Claude Code possa consultá-los quando precisar tomar decisões finas durante a implementação.

### `GESP_Manual_Identidade_Visual_v1.6_abr2023.pdf`

É o **Manual de Identidade Visual do Governo do Estado de São Paulo, versão 1.6, de abril de 2023** — 44 páginas, 5,8 MB. Está oficialmente publicado em `https://sigam.ambiente.sp.gov.br/sigam3/repositorio/559/documentos/GESP_MANUAL_DE%20IDENTIDADE_VISUAL_2023.pdf` (servidor da Secretaria do Meio Ambiente, que disponibiliza o repositório do SIGAM).

Esse PDF é o documento normativo primário que define:

A paleta de cores oficial com seus códigos Pantone, RGB e CMYK exatos; as regras de proporção e margens de segurança do logotipo; as variações cromáticas permitidas (positiva, negativa, escala de cinza); os "usos não permitidos" que evitam descaracterização da marca; o padrão para assinatura conjunta com Secretarias e parceiros; o grid de 12 colunas com gutter de 30px para web; e os modelos de papelaria institucional.

### `Portfolio_LILP_2026_v4.pdf`

É o **Portfólio do Laboratório de Inovação em Logística Pública, edição 2026, versão 4** — 36 páginas (notei que o `pdfinfo` me mostrou 36, não 8 como eu havia dito antes), 666 KB. Está hospedado em `https://compras.sp.gov.br/wp-content/uploads/2026/02/Portfolio-Laboratorio-Inovacao-2026_V4-1.pdf`.

Esse documento é fundamental para o portal BDLP por dois motivos. Primeiro, ele apresenta os projetos, parcerias e iniciativas do LILP — material que pode ser parcialmente reaproveitado na página "Sobre" do portal. Segundo, e mais importante, ele estabelece o padrão visual de comunicação do LILP: capa com a faixa vermelha curva no topo, logotipo do LILP centralizado, títulos em caixa alta com fonte sans-serif bold, paleta dominante em vermelho e preto sobre branco. Essa é a estética que o portal BDLP deve dialogar.

---

## Como entregar ao Claude Code

Quando você abrir a sessão do Claude Code para executar o prompt que entreguei na conversa anterior, faça o seguinte na sequência. Primeiro, descompacte este pacote no seu computador. Depois, dentro do repositório Git do portal Django da BDLP, crie a estrutura de diretórios `catalog/static/img/logos/` e `docs/referencias/`. Em seguida, copie os arquivos para os diretórios apropriados conforme a equivalência:

- Os arquivos da pasta `governo-sp/` vão para `catalog/static/img/logos/governo-sp/`
- Os arquivos da pasta `sggd/` vão para `catalog/static/img/logos/sggd/`
- O arquivo `lilp/logo-lilp-original.png` vai para `catalog/static/img/logos/lilp.png` (renomeie no destino)
- Os SVGs e PNGs da pasta `sbu-unicamp/` vão para `catalog/static/img/logos/sbu-unicamp/`
- Os ícones de redes sociais e acessibilidade vão para `catalog/static/img/icons/` (mas só se você decidir não usar SVGs inline do Lucide/Heroicons; é uma escolha técnica)
- Os PDFs da pasta `manuais/` vão para `docs/referencias/`

Faça commit dessa estrutura com mensagem do tipo `chore(assets): adiciona logos institucionais e manuais de referência`, e só depois cole o prompt do Claude Code. Assim o agente já encontra os arquivos no lugar certo e não precisa interromper a execução pedindo arquivos.

---

## O que ainda pode ficar como TODO

Após esta entrega, restam três pontos abertos que dependem de gestão institucional, não de busca técnica. Primeiro, o **arquivo SVG vetorial original do logo do LILP** — o PNG extraído do portfólio resolve para o portal, mas para impressão futura ou uso em peças grandes vale solicitar à equipe que produziu o portfólio. Segundo, a **identificação formal do encarregado de proteção de dados (DPO) da SGGD** — necessário para a página de Política de Privacidade conforme art. 41 da LGPD; se não houver designação ainda, mantenha como placeholder e sinalize ao gabinete da Secretaria. Terceiro, a **definição do e-mail institucional do BDLP** — sugestões como `bdlp@sggd.sp.gov.br` ou `lilp@sp.gov.br` precisam ser criadas pelo setor de TI da SGGD antes do lançamento público do portal.

Esses três TODOs estão longe de impeditivos. O prompt do Claude Code já antecipa cada um e os trata com placeholders claros marcados como rascunho, permitindo que o portal seja construído em paralelo à resolução desses pontos institucionais.

---

*Documento gerado em 06 de maio de 2026 — Laboratório de Inovação em Logística Pública (LILP) / SGGD / Governo do Estado de São Paulo.*
