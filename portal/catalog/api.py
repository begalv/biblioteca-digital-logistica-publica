"""
Endpoints JSON para uso pelo frontend (cascata dinâmica de filtros do Acervo).
"""

from django.http import JsonResponse
from django.views.decorators.cache import cache_page
from django.views.decorators.http import require_GET

from .facets import compute_facets
from .views import _read_filters


@require_GET
@cache_page(30)  # cache leve para reduzir carga em paginação rápida do usuário
def facets(request):
    """Devolve as facetas e contagens dado o conjunto de filtros aplicado.

    Suporta os mesmos parâmetros da view `search()` (assunto_id, category_id,
    subcategoria_id, microcategoria_id, tipologia, complexidade, etc.).
    Retorna JSON pronto para o JS atualizar a sidebar sem reload.
    """
    filters = _read_filters(request)
    data = compute_facets(filters)
    return JsonResponse(data)
