from django.conf import settings
from django.core.paginator import Paginator
from django.http import FileResponse, Http404
from django.shortcuts import get_object_or_404, render

from .models import Document, NrCategory, Topic, TypeInformation
from .search import search_documents


def home(request):
    """Homepage: busca, stats, coleções e últimas adições."""
    collections = Topic.objects.filter(parent_id=0).order_by("name")
    recent_docs = Document.objects.filter(status="a").order_by("-created")[:10]
    total_docs = Document.objects.filter(status="a").count()
    total_collections = collections.count()

    return render(request, "home.html", {
        "collections": collections,
        "recent_docs": recent_docs,
        "total_docs": total_docs,
        "total_collections": total_collections,
    })


def search(request):
    """Busca full-text com filtros facetados."""
    query = request.GET.get("q", "").strip()
    page_number = request.GET.get("page", 1)

    filters = {
        "topic_id": request.GET.get("topic_id"),
        "category_id": request.GET.get("category_id"),
        "tipologia": request.GET.get("tipologia"),
        "etapa": request.GET.get("etapa"),
        "complexidade": request.GET.get("complexidade"),
        "year_from": request.GET.get("year_from"),
        "year_to": request.GET.get("year_to"),
        "typeinform_id": request.GET.get("typeinform_id"),
    }
    # Limpar filtros vazios
    filters = {k: v for k, v in filters.items() if v}

    if query:
        results = search_documents(query, filters if filters else None)
    elif filters:
        results = Document.objects.filter(status="a")
        if filters.get("topic_id"):
            results = results.filter(topic_id=filters["topic_id"])
        if filters.get("category_id"):
            results = results.filter(category_id=filters["category_id"])
        if filters.get("tipologia"):
            results = results.filter(tipologia=filters["tipologia"])
        if filters.get("etapa"):
            results = results.filter(etapa_processo_licitatorio=filters["etapa"])
        if filters.get("complexidade"):
            results = results.filter(complexidade=filters["complexidade"])
        results = results.order_by("-created")
    else:
        results = Document.objects.none()

    paginator = Paginator(results, settings.SEARCH_RESULTS_PER_PAGE)
    page_obj = paginator.get_page(page_number)

    collections = Topic.objects.filter(parent_id=0).order_by("name")
    categories = NrCategory.objects.all().order_by("name")
    tipologias = ["Administrativo", "Informacional", "Jurisprudencial", "Normativo", "Operacional"]
    complexidades = ["Baixa", "Média", "Alta"]

    return render(request, "search.html", {
        "query": query,
        "page_obj": page_obj,
        "filters": filters,
        "collections": collections,
        "categories": categories,
        "tipologias": tipologias,
        "complexidades": complexidades,
        "total_results": paginator.count,
    })


def document_detail(request, code):
    """Detalhes de um documento."""
    doc = get_object_or_404(Document, code=code, status="a")
    return render(request, "document_detail.html", {"document": doc})


def collection_list(request):
    """Grid das coleções principais."""
    collections = Topic.objects.filter(parent_id=0).order_by("name")

    collection_data = []
    for col in collections:
        subcollections = Topic.objects.filter(parent_id=col.id).order_by("name")
        # Contar docs na coleção e subcoleções
        topic_ids = [col.id] + list(subcollections.values_list("id", flat=True))
        doc_count = Document.objects.filter(status="a", topic_id__in=topic_ids).count()
        collection_data.append({
            "topic": col,
            "subcollections": subcollections,
            "doc_count": doc_count,
        })

    return render(request, "collection_list.html", {"collection_data": collection_data})


def collection_detail(request, topic_id):
    """Detalhes de uma coleção com subcoleções e documentos."""
    topic = get_object_or_404(Topic, id=topic_id)
    subcollections = Topic.objects.filter(parent_id=topic.id).order_by("name")
    page_number = request.GET.get("page", 1)

    # Documentos da coleção e subcoleções
    topic_ids = [topic.id] + list(subcollections.values_list("id", flat=True))
    documents = Document.objects.filter(status="a", topic_id__in=topic_ids).order_by("-created")

    paginator = Paginator(documents, settings.SEARCH_RESULTS_PER_PAGE)
    page_obj = paginator.get_page(page_number)

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
