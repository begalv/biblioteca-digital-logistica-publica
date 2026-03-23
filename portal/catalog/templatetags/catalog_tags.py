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
