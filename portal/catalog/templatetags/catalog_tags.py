import re
from urllib.parse import urlencode, urlparse

from django import template

register = template.Library()


_YEAR_ONLY_RE = re.compile(r"^\s*(?:19|20)\d{2}\s*$")


@register.filter
def is_year_only(value):
    """True se a string for apenas um ano de 4 dígitos (1900-2099) com
    eventuais espaços. Usado para esconder `source` quando ele é
    redundante com `ano` (357 docs do acervo).
    """
    if not value:
        return False
    return bool(_YEAR_ONLY_RE.match(str(value)))


_PLURAIS_PT = {
    # Casos irregulares ou "{singular}+s" não aplicável.
    # Adicione aqui ao introduzir nova palavra na UI.
    "material": "materiais",
    "papel": "papéis",
    "nível": "níveis",
    "mês": "meses",
    "país": "países",
    "coleção": "coleções",
    "subcoleção": "subcoleções",
    "função": "funções",
    "razão": "razões",
    "informação": "informações",
    "edição": "edições",
    "publicação": "publicações",
    "operação": "operações",
    "cidadão": "cidadãos",  # exceção da regra ão→ões
    "irmão": "irmãos",
}


@register.filter
def pluralize_pt(value, singular):
    """Pluraliza palavra em português conforme `value` (count).

    Uso: {{ count|pluralize_pt:"material" }} → "material" se count==1,
    senão "materiais" (consulta o dicionário; fallback para singular+'s').

    Por que existir: o `pluralize` padrão do Django só sabe sufixar 's'/'es'
    (regra inglesa). Para palavras como "material → materiais" ou
    "coleção → coleções" o comportamento default produz "materialis" ou
    "coleçãos". Esta tag centraliza os irregulares do português.
    """
    try:
        n = int(value)
    except (TypeError, ValueError):
        n = 0
    if n == 1:
        return singular
    if singular in _PLURAIS_PT:
        return _PLURAIS_PT[singular]
    # Heurística simples para os casos não dicionarizados:
    # ão→ões cobre a esmagadora maioria; consoantes finais ganham 'es';
    # demais ganham 's'. Se aparecer caso novo, prefira adicionar ao dict
    # acima em vez de generalizar a heurística.
    if singular.endswith("ão"):
        return singular[:-2] + "ões"
    if singular and singular[-1] in "rsz":
        return singular + "es"
    return singular + "s"


@register.filter
def url_domain(value):
    """Extrai o host de uma URL para exibir como hint sob botões de
    'Acessar documento' (ex: 'https://springer.com/x' → 'springer.com').
    Strips 'www.' para legibilidade.
    """
    if not value:
        return ""
    try:
        host = urlparse(str(value)).netloc
    except Exception:
        return ""
    if host.startswith("www."):
        host = host[4:]
    return host


@register.filter
def truncate_words_html(value, length=30):
    """Trunca texto preservando palavras inteiras."""
    if not value:
        return ""
    words = value.split()
    if len(words) <= length:
        return value
    return " ".join(words[:length]) + "..."


@register.filter
def split_keywords(value):
    """Divide string de palavras-chave em lista."""
    if not value:
        return []
    separators = [";", ","]
    for sep in separators:
        if sep in value:
            return [kw.strip() for kw in value.split(sep) if kw.strip()]
    return [kw.strip() for kw in value.split() if kw.strip()]


@register.simple_tag
def querystring_replace(querydict, key, value):
    """Reescreve a querystring atual substituindo uma chave por um valor.

    Útil para links de paginação que preservam filtros aplicados:
        <a href="?{% querystring_replace request.GET 'page' 2 %}">Próxima</a>
    """
    params = querydict.copy() if hasattr(querydict, "copy") else dict(querydict)
    if value is None or value == "":
        params.pop(key, None)
    else:
        params[key] = str(value)
    # urlencode aceita dict comum ou QueryDict via .lists()
    if hasattr(params, "urlencode"):
        return params.urlencode()
    return urlencode(params)
