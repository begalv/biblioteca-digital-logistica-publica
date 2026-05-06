from django.contrib.postgres.search import SearchQuery, SearchRank, SearchVector

from .models import Document


# Filtros suportados pela busca; o helper apply_filters reutiliza esta lista
FILTERABLE_FIELDS = (
    "topic_id",
    "category_id",
    "subcategoria_id",
    "microcategoria_id",
    "assunto_id",
    "tipologia",
    "etapa",
    "complexidade",
    "typeinform_id",
    "permissao",
    "ano_min",
    "ano_max",
    # Compatibilidade retroativa: year_from/year_to ainda aceitos
    "year_from",
    "year_to",
)


def _apply_filters(qs, filters):
    """Aplica filtros estruturados ao queryset de Document."""
    if not filters:
        return qs

    if filters.get("topic_id"):
        qs = qs.filter(topic_id=filters["topic_id"])
    if filters.get("category_id"):
        qs = qs.filter(category_id=filters["category_id"])
    if filters.get("subcategoria_id"):
        qs = qs.filter(subcategoria_id=filters["subcategoria_id"])
    if filters.get("microcategoria_id"):
        qs = qs.filter(microcategoria_id=filters["microcategoria_id"])
    if filters.get("assunto_id"):
        qs = qs.filter(assunto_id=filters["assunto_id"])
    if filters.get("tipologia"):
        qs = qs.filter(tipologia=filters["tipologia"])
    if filters.get("etapa"):
        qs = qs.filter(etapa_processo_licitatorio=filters["etapa"])
    if filters.get("complexidade"):
        qs = qs.filter(complexidade=filters["complexidade"])
    if filters.get("typeinform_id"):
        qs = qs.filter(typeinform_id=filters["typeinform_id"])
    if filters.get("permissao"):
        qs = qs.filter(permissao=filters["permissao"])

    # Ano: novos params (ano_min/ano_max) preferidos sobre legados (year_from/year_to)
    ano_min = filters.get("ano_min") or filters.get("year_from")
    ano_max = filters.get("ano_max") or filters.get("year_to")
    if ano_min:
        try:
            qs = qs.filter(ano__gte=int(ano_min))
        except (TypeError, ValueError):
            pass
    if ano_max:
        try:
            qs = qs.filter(ano__lte=int(ano_max))
        except (TypeError, ValueError):
            pass

    return qs


def search_documents(query, filters=None):
    """Busca full-text em português + filtros estruturados nos documentos arquivados.

    O vetor inclui campos LILP (tipologia, complexidade, uso_futuro, metodo, resultado)
    além dos clássicos title/keywords/author/abstract.
    """
    vector = (
        SearchVector("title", weight="A", config="portuguese")
        + SearchVector("keywords", weight="A", config="portuguese")
        + SearchVector("author", weight="B", config="portuguese")
        + SearchVector("autor_principal", weight="B", config="portuguese")
        + SearchVector("abstract", weight="C", config="portuguese")
        + SearchVector("uso_futuro", weight="C", config="portuguese")
        + SearchVector("metodo", weight="D", config="portuguese")
        + SearchVector("resultado", weight="D", config="portuguese")
        + SearchVector("tipologia", weight="D", config="portuguese")
        + SearchVector("complexidade", weight="D", config="portuguese")
    )
    search_query = SearchQuery(query, config="portuguese")

    qs = Document.objects.filter(status="a")
    qs = qs.annotate(rank=SearchRank(vector, search_query))
    qs = qs.filter(rank__gte=0.01).order_by("-rank")

    qs = _apply_filters(qs, filters)
    return qs


def filter_documents(filters):
    """Apenas filtros estruturados (sem termo de busca). Ordena por mais recente."""
    qs = Document.objects.filter(status="a")
    qs = _apply_filters(qs, filters)
    return qs.order_by("-created")
