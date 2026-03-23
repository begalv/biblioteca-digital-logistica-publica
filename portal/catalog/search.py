from django.contrib.postgres.search import SearchQuery, SearchRank, SearchVector

from .models import Document


def search_documents(query, filters=None):
    """Busca full-text em português nos documentos arquivados."""
    vector = (
        SearchVector("title", weight="A", config="portuguese")
        + SearchVector("keywords", weight="A", config="portuguese")
        + SearchVector("author", weight="B", config="portuguese")
        + SearchVector("abstract", weight="C", config="portuguese")
    )
    search_query = SearchQuery(query, config="portuguese")

    qs = Document.objects.filter(status="a")
    qs = qs.annotate(rank=SearchRank(vector, search_query))
    qs = qs.filter(rank__gte=0.01).order_by("-rank")

    if filters:
        if filters.get("topic_id"):
            qs = qs.filter(topic_id=filters["topic_id"])
        if filters.get("category_id"):
            qs = qs.filter(category_id=filters["category_id"])
        if filters.get("tipologia"):
            qs = qs.filter(tipologia=filters["tipologia"])
        if filters.get("etapa"):
            qs = qs.filter(etapa_processo_licitatorio=filters["etapa"])
        if filters.get("complexidade"):
            qs = qs.filter(complexidade=filters["complexidade"])
        if filters.get("year_from"):
            qs = qs.filter(source__icontains=filters["year_from"])
        if filters.get("year_to"):
            qs = qs.filter(source__icontains=filters["year_to"])
        if filters.get("typeinform_id"):
            qs = qs.filter(typeinform_id=filters["typeinform_id"])

    return qs
