from django.conf import settings
from django.core.paginator import Paginator
from django.http import FileResponse, Http404
from django.shortcuts import get_object_or_404, render

from .facets import compute_facets
from .models import Document, NrCategory, Topic, TypeInformation
from .search import filter_documents, search_documents


def home(request):
    """Homepage: busca, stats, coleções e últimas adições."""
    from django.db.models import Count

    collections = Topic.objects.filter(parent_id=0).order_by("name")
    recent_docs = Document.objects.filter(status="a").order_by("-created")[:10]

    # Stat 1: total de materiais
    total_docs = Document.objects.filter(status="a").count()

    # Stat 2: distribuição por Tipo de informação (top 3 por contagem)
    tipo_dist = (
        Document.objects.filter(status="a", typeinform_id__isnull=False)
        .values("typeinform_id")
        .annotate(c=Count("id"))
        .order_by("-c")[:3]
    )
    type_info_names = {
        ti.id: ti.name
        for ti in TypeInformation.objects.filter(
            id__in=[t["typeinform_id"] for t in tipo_dist]
        )
    }
    tipos_top = [
        {"nome": type_info_names.get(t["typeinform_id"], "—"), "count": t["c"]}
        for t in tipo_dist
    ]
    tipos_distintos = (
        Document.objects.filter(status="a", typeinform_id__isnull=False)
        .values("typeinform_id").distinct().count()
    )

    # Stat 3: cobertura temática
    from .models import Assunto, Subcategoria
    cobertura = {
        "assuntos": Assunto.objects.count(),
        "subcategorias": Subcategoria.objects.count(),
        "macroetapas": NrCategory.objects.count(),
    }

    # Stat 4: % de acesso aberto
    aberto = Document.objects.filter(status="a", permissao="Aberto").count()
    pct_aberto = round(100 * aberto / total_docs) if total_docs else 0

    # Cards de coleção na home — adicionar contagem (incluindo subcoleções)
    docs_by_topic = dict(
        Document.objects.filter(status="a")
        .values_list("topic_id")
        .annotate(c=Count("id"))
        .values_list("topic_id", "c")
    )
    collections_with_count = []
    for col in collections:
        sub_ids = list(Topic.objects.filter(parent_id=col.id).values_list("id", flat=True))
        topic_ids = [col.id] + sub_ids
        col_count = sum(docs_by_topic.get(tid, 0) for tid in topic_ids)
        collections_with_count.append({"topic": col, "doc_count": col_count})

    return render(request, "home.html", {
        "collections": collections_with_count,
        "recent_docs": recent_docs,
        "total_docs": total_docs,
        "tipos_top": tipos_top,
        "tipos_distintos": tipos_distintos,
        "cobertura": cobertura,
        "pct_aberto": pct_aberto,
    })


FILTER_PARAMS = (
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
)


def _read_filters(request):
    """Extrai filtros válidos da query string, descartando vazios."""
    filters = {p: request.GET.get(p) for p in FILTER_PARAMS}
    return {k: v for k, v in filters.items() if v}


def search(request):
    """Busca full-text com filtros facetados em cascata."""
    query = request.GET.get("q", "").strip()
    page_number = request.GET.get("page", 1)
    filters = _read_filters(request)

    if query:
        results = search_documents(query, filters if filters else None)
    elif filters:
        results = filter_documents(filters)
    else:
        # Sem busca e sem filtros: lista os mais recentes (não vazio como antes)
        results = filter_documents({})

    paginator = Paginator(results, settings.SEARCH_RESULTS_PER_PAGE)
    page_obj = paginator.get_page(page_number)

    facets = compute_facets(filters)

    return render(request, "search.html", {
        "query": query,
        "page_obj": page_obj,
        "filters": filters,
        "facets": facets,
        "total_results": paginator.count,
    })


def document_detail(request, code):
    """Detalhes de um documento."""
    doc = get_object_or_404(Document, code=code, status="a")
    return render(request, "document_detail.html", {"document": doc})


def collection_list(request):
    """Grid das coleções principais."""
    collections = Topic.objects.filter(parent_id=0).order_by("name")

    # Contagem agregada de docs por topic_id (única query)
    from django.db.models import Count
    docs_by_topic = dict(
        Document.objects.filter(status="a")
        .values_list("topic_id")
        .annotate(c=Count("id"))
        .values_list("topic_id", "c")
    )

    collection_data = []
    for col in collections:
        subcollections_qs = Topic.objects.filter(parent_id=col.id)
        subs_with_count = []
        sub_total = 0
        for sub in subcollections_qs:
            count = docs_by_topic.get(sub.id, 0)
            sub_total += count
            subs_with_count.append({"topic": sub, "doc_count": count})
        # Ordenar por contagem desc (mais populadas primeiro), depois alfabético
        subs_with_count.sort(key=lambda s: (-s["doc_count"], s["topic"].name))
        own_count = docs_by_topic.get(col.id, 0)
        collection_data.append({
            "topic": col,
            "subcollections": subs_with_count,
            "doc_count": own_count + sub_total,
        })

    return render(request, "collection_list.html", {"collection_data": collection_data})


def collection_detail(request, topic_id):
    """Detalhes de uma coleção com subcoleções e documentos."""
    from django.db.models import Count

    topic = get_object_or_404(Topic, id=topic_id)
    subcollections_qs = Topic.objects.filter(parent_id=topic.id).order_by("name")
    page_number = request.GET.get("page", 1)

    # Documentos da coleção e subcoleções
    topic_ids = [topic.id] + list(subcollections_qs.values_list("id", flat=True))
    documents = Document.objects.filter(status="a", topic_id__in=topic_ids).order_by("-created")

    paginator = Paginator(documents, settings.SEARCH_RESULTS_PER_PAGE)
    page_obj = paginator.get_page(page_number)

    # Contagem por subcoleção (única query)
    docs_by_topic = dict(
        Document.objects.filter(status="a", topic_id__in=topic_ids)
        .values_list("topic_id")
        .annotate(c=Count("id"))
        .values_list("topic_id", "c")
    )
    subcollections = [
        {"topic": sub, "doc_count": docs_by_topic.get(sub.id, 0)}
        for sub in subcollections_qs
    ]
    # Ordenar por contagem desc, depois alfabético
    subcollections.sort(key=lambda s: (-s["doc_count"], s["topic"].name))
    # Versão filtrada (>0) para a UI; mantemos a lista completa para
    # `topic_ids` e contagem total dos documentos da coleção.
    subcollections_visiveis = [s for s in subcollections if s["doc_count"] > 0]

    # Breadcrumb
    breadcrumb = []
    if topic.parent_id != 0:
        parent = Topic.objects.filter(id=topic.parent_id).first()
        if parent:
            breadcrumb.append(parent)
    breadcrumb.append(topic)

    return render(request, "collection_detail.html", {
        "topic": topic,
        "subcollections": subcollections,
        "subcollections_visiveis": subcollections_visiveis,
        "page_obj": page_obj,
        "breadcrumb": breadcrumb,
        "total_docs": paginator.count,
    })


def download(request, code):
    """Download de documento via arquivo local ou redirecionamento."""
    doc = get_object_or_404(Document, code=code, status="a")

    if doc.is_remote and doc.acesso_eletronico:
        from django.shortcuts import redirect
        return redirect(doc.acesso_eletronico)

    import os
    archive_dir = settings.NOURAU_ARCHIVE_DIR
    # Nou-Rau organiza arquivos por topic_id/code
    file_path = os.path.join(archive_dir, str(doc.topic_id), doc.code, doc.filename)

    if not os.path.exists(file_path):
        raise Http404("Arquivo não encontrado.")

    return FileResponse(open(file_path, "rb"), as_attachment=True, filename=doc.filename)


def about(request):
    """Sobre o LILP e a biblioteca."""
    return render(request, "about.html")


# =====================================================================
# Páginas institucionais e legais (eMAG 3.1, LAI, LGPD, Lei 13.460/2017)
# Conteúdo em rascunho — aviso visual em cada página solicita validação
# pelo Encarregado de Dados (DPO) e pela Procuradoria/Assessoria Jurídica.
# =====================================================================

def transparencia(request):
    """Página de Transparência — atende LAI (Lei 12.527/2011)."""
    return render(request, "legal/transparencia.html")


def acessibilidade(request):
    """Compromisso de acessibilidade — eMAG 3.1 / WCAG 2.0 AA."""
    return render(request, "legal/acessibilidade.html")


def politica_privacidade(request):
    """Política de Privacidade — atende LGPD (Lei 13.709/2018)."""
    return render(request, "legal/politica_privacidade.html")


def politica_cookies(request):
    """Política de Cookies — atende LGPD e Marco Civil (Lei 12.965/2014)."""
    return render(request, "legal/politica_cookies.html")


def mapa_site(request):
    """Mapa do site — recomendação eMAG 3.1."""
    return render(request, "legal/mapa_site.html")


def fale_conosco(request):
    """Canal de contato — Lei 13.460/2017 (Direitos do Usuário)."""
    return render(request, "legal/fale_conosco.html")
