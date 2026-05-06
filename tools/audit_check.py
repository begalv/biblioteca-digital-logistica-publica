"""Auditoria estática rápida — sem dependências externas.

Checa:
- Balanceamento de chaves/parenteses no main.js
- Variáveis CSS usadas mas não definidas
- IDs duplicados em cada HTML renderizado
- Atributos alt ausentes em imagens
- form sem label associado
- Hierarquia de heading (saltos h1->h3 etc.)
"""
from __future__ import annotations

import io
import re
import sys
from pathlib import Path

# Força UTF-8 no stdout (Windows console default é cp1252)
if hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")

ROOT = Path(__file__).resolve().parent.parent
RESULTS = []


def report(severity: str, msg: str) -> None:
    RESULTS.append((severity, msg))


# ------------------------------------------------------------
# 1. JS balance check
# ------------------------------------------------------------
def check_js_balance(path: Path) -> None:
    js = path.read_text(encoding="utf-8")
    pairs = {"(": ")", "[": "]", "{": "}"}
    stack: list[str] = []
    in_str: str | None = None
    in_regex = False
    i = 0
    while i < len(js):
        c = js[i]
        if in_str:
            if c == "\\":
                i += 2
                continue
            if c == in_str:
                in_str = None
            i += 1
            continue
        # comentário linha
        if c == "/" and i + 1 < len(js) and js[i + 1] == "/":
            nl = js.find("\n", i)
            i = len(js) if nl == -1 else nl
            continue
        # comentário bloco
        if c == "/" and i + 1 < len(js) and js[i + 1] == "*":
            end = js.find("*/", i + 2)
            i = len(js) if end == -1 else end + 2
            continue
        if c in ("\"", "'", "`"):
            in_str = c
            i += 1
            continue
        if c in pairs:
            stack.append(pairs[c])
        elif c in pairs.values():
            if not stack or stack[-1] != c:
                report("ERROR", f"JS desbalanceado em {path.name}:{js[:i].count(chr(10))+1}: esperava {stack[-1] if stack else 'fim'}, achou {c}")
                return
            stack.pop()
        i += 1
    if stack:
        report("ERROR", f"JS {path.name}: pilha não vazia: {stack}")
    else:
        report("OK", f"JS {path.name} balanceado ({len(js)} bytes, {js.count(chr(10))} linhas)")


# ------------------------------------------------------------
# 2. CSS variables — usadas vs. definidas
# ------------------------------------------------------------
def check_css_vars() -> None:
    used: set[str] = set()
    defined: set[str] = set()
    for css_path in (ROOT / "portal/static/css").glob("*.css"):
        css = css_path.read_text(encoding="utf-8")
        # Definições: --nome:
        for m in re.finditer(r"--([a-zA-Z][a-zA-Z0-9-]*)\s*:", css):
            defined.add(m.group(1))
        # Usos: var(--nome)
        for m in re.finditer(r"var\(\s*--([a-zA-Z][a-zA-Z0-9-]*)", css):
            used.add(m.group(1))

    undefined = used - defined
    unused = defined - used

    if undefined:
        for v in sorted(undefined):
            report("ERROR", f"CSS variável usada mas NÃO DEFINIDA: --{v}")
    else:
        report("OK", f"CSS variáveis: {len(used)} usadas, {len(defined)} definidas, todas resolvem")

    if unused:
        for v in sorted(unused):
            report("WARN", f"CSS variável definida mas não usada: --{v}")


# ------------------------------------------------------------
# 3. HTML renderizado — IDs duplicados, alt ausente, hierarquia
# ------------------------------------------------------------
HTML_DIR = ROOT / "preview_output"


def check_html_file(path: Path) -> None:
    html = path.read_text(encoding="utf-8")
    rel = path.relative_to(HTML_DIR)

    # IDs duplicados
    ids = re.findall(r'\bid="([^"]+)"', html)
    seen: dict[str, int] = {}
    for i in ids:
        seen[i] = seen.get(i, 0) + 1
    dupes = {i: c for i, c in seen.items() if c > 1}
    if dupes:
        for i, c in dupes.items():
            report("ERROR", f"HTML {rel}: ID duplicado '{i}' aparece {c}x")

    # img sem alt
    imgs = re.findall(r'<img[^>]*>', html)
    sem_alt = [im for im in imgs if not re.search(r'\balt\s*=', im)]
    if sem_alt:
        for im in sem_alt:
            report("ERROR", f"HTML {rel}: <img> sem alt: {im[:120]}")

    # Hierarquia de heading (não pode pular níveis indo para baixo)
    headings = [(int(m.group(1)), m.start()) for m in re.finditer(r'<h([1-6])\b', html)]
    if headings:
        if headings[0][0] != 1:
            report("WARN", f"HTML {rel}: primeira heading é h{headings[0][0]} (esperado h1)")
        for prev, cur in zip(headings, headings[1:]):
            if cur[0] > prev[0] + 1:
                report("WARN", f"HTML {rel}: salto h{prev[0]} → h{cur[0]} na posição {cur[1]}")
        # Múltiplos h1 (cada página deve ter só um)
        h1s = [h for h in headings if h[0] == 1]
        if len(h1s) > 1:
            report("WARN", f"HTML {rel}: {len(h1s)} h1 (deveria ser 1)")
        elif len(h1s) == 0:
            report("WARN", f"HTML {rel}: nenhum h1")

    # input/textarea/select sem label associado nem aria-label
    field_pattern = re.compile(
        r'<(input|textarea|select)\b((?:[^>]|"[^"]*"|\'[^\']*\')*?)>',
        re.IGNORECASE,
    )
    for m in field_pattern.finditer(html):
        attrs = m.group(2)
        if re.search(r'\btype\s*=\s*["\'](?:hidden|submit|button)["\']', attrs):
            continue
        # tem id?
        id_m = re.search(r'\bid\s*=\s*["\']([^"\']+)["\']', attrs)
        # tem aria-label?
        if re.search(r'\baria-label\s*=', attrs):
            continue
        # tem aria-labelledby?
        if re.search(r'\baria-labelledby\s*=', attrs):
            continue
        if not id_m:
            report("WARN", f"HTML {rel}: campo de form sem id e sem aria-label: {m.group(0)[:100]}")
            continue
        fid = id_m.group(1)
        # Existe <label for="$fid">?
        if not re.search(rf'<label[^>]*\bfor\s*=\s*["\']{re.escape(fid)}["\']', html):
            report("WARN", f"HTML {rel}: campo id='{fid}' sem <label for=...> nem aria-label")

    # Anchor aninhado (<a> dentro de <a>)
    nested = re.findall(r'<a\b[^>]*>(?:(?!</a>).)*?<a\b', html, re.DOTALL)
    if nested:
        report("ERROR", f"HTML {rel}: <a> aninhado em <a> ({len(nested)}x)")

    # Inline style (problemático com CSP script-src 'self' sem 'unsafe-inline')
    inline_styles = re.findall(r'\bstyle\s*=\s*"[^"]+"', html)
    if inline_styles:
        report("WARN", f"HTML {rel}: {len(inline_styles)} atributo(s) style= inline (CSP exige 'unsafe-inline' em style-src)")

    # Inline event handlers (CSP script-src sem 'unsafe-inline' bloqueia)
    inline_events = re.findall(r'\bon\w+\s*=\s*"[^"]+"', html)
    if inline_events:
        report("ERROR", f"HTML {rel}: {len(inline_events)} handler(s) inline (on*=) — bloqueados por CSP")

    # <button> sem type explicito (default é "submit" — pode submeter forms inadvertidamente)
    button_no_type = re.findall(r'<button\b(?!\s[^>]*\btype\s*=)[^>]*>', html)
    if button_no_type:
        report("WARN", f"HTML {rel}: {len(button_no_type)} <button> sem type= (default 'submit')")

    # Hrefs vazios ou problemáticos
    empty_hrefs = re.findall(r'<a\b[^>]*\bhref\s*=\s*"(?:#?|javascript:[^"]*)"', html)
    if empty_hrefs:
        report("WARN", f"HTML {rel}: {len(empty_hrefs)} <a> com href vazio/javascript:")

    # target="_blank" sem rel="noopener" (segurança — risco de tabnabbing)
    blanks = re.findall(r'<a\b[^>]*\btarget\s*=\s*"_blank"[^>]*>', html)
    insecure_blanks = [b for b in blanks if "noopener" not in b]
    if insecure_blanks:
        report("WARN", f"HTML {rel}: {len(insecure_blanks)} target=\"_blank\" sem rel=\"noopener\"")

    # Tag <p> contendo block-level (inválido HTML5)
    p_with_block = re.findall(r'<p\b[^>]*>(?:(?!</p>).)*?<(?:div|section|article|header|footer|nav|aside|h[1-6]|ul|ol|table|form|fieldset)\b', html, re.DOTALL)
    if p_with_block:
        report("WARN", f"HTML {rel}: {len(p_with_block)} <p> contendo block-level (HTML5 inválido)")


# ------------------------------------------------------------
# 4. Executa todas as checagens
# ------------------------------------------------------------
def main() -> int:
    js_main = ROOT / "portal/static/js/main.js"
    if js_main.exists():
        check_js_balance(js_main)

    check_css_vars()

    if HTML_DIR.exists():
        for html in sorted(HTML_DIR.rglob("*.html")):
            check_html_file(html)
    else:
        report("WARN", f"preview_output/ ausente — rode tools/preview_render.py antes")

    erros = sum(1 for s, _ in RESULTS if s == "ERROR")
    warns = sum(1 for s, _ in RESULTS if s == "WARN")
    oks = sum(1 for s, _ in RESULTS if s == "OK")

    for sev, msg in RESULTS:
        prefix = {"OK": "[OK]   ", "WARN": "[WARN] ", "ERROR": "[ERROR]"}[sev]
        print(f"{prefix} {msg}")

    print()
    print(f"Resumo: {oks} OK, {warns} WARN, {erros} ERROR")
    return 0 if erros == 0 else 1


if __name__ == "__main__":
    sys.exit(main())
