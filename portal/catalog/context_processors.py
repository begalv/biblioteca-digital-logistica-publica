"""Context processors injetados em todos os templates do portal.

Fornecem dados institucionais (estatísticas globais, ano corrente)
e utilitários de navegação (URL ativa para destaque do menu via
aria-current="page" — eMAG R1.4 / WCAG 2.4.8).
"""

from datetime import datetime

from .models import Document, Topic


def site_context(request):
    """Disponibiliza variáveis globais para todos os templates.

    Variáveis injetadas:
        current_url_name: nome da rota Django ativa (ex.: "home", "search")
        site_year: ano corrente (rodapé)
        total_documentos: contagem de documentos publicados (status='a')
        total_colecoes: contagem de coleções principais (parent_id=0)

    Cuidado: este processor roda em TODA requisição. Os counts são
    cheap (índice em status), mas podem ser cacheados em iteração
    futura se virarem gargalo.
    """
    current_url_name = ""
    if request.resolver_match:
        # Inclui namespace para distinguir 'catalog:home' de outros 'home'
        current_url_name = request.resolver_match.url_name or ""

    return {
        "current_url_name": current_url_name,
        "site_year": datetime.now().year,
        "total_documentos": Document.objects.filter(status="a").count(),
        "total_colecoes": Topic.objects.filter(parent_id=0).count(),
    }
