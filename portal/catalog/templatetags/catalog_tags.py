from urllib.parse import urlencode

from django import template

register = template.Library()


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
