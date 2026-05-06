"""
Faceted aggregation: para cada filtro disponível, calcula a contagem de documentos
que satisfazem TODOS os outros filtros aplicados — exceto ele mesmo. Isso permite
que o usuário troque um valor de filtro sem ficar com lista vazia.
"""

from django.db.models import Count, Min, Max

from .models import (
    Assunto,
    Document,
    Microcategoria,
    NrCategory,
    Subcategoria,
    Topic,
    TypeInformation,
)
from .search import _apply_filters


def _facet_counts(filters, exclude_key, group_by):
    """Conta documentos agrupados por `group_by`, aplicando filters menos `exclude_key`."""
    f = {k: v for k, v in (filters or {}).items() if k != exclude_key}
    qs = Document.objects.filter(status="a")
    qs = _apply_filters(qs, f)
    return (
        qs.exclude(**{f"{group_by}__isnull": True})
        .values(group_by)
        .annotate(count=Count("id"))
        .order_by("-count")
    )


def _attach_names(rows, key, model, label_field="nome"):
    """Recebe rows [{key: id, count: n}] e devolve [{id, nome, count}] com nomes resolvidos."""
    ids = [r[key] for r in rows if r.get(key)]
    if not ids:
        return []
    name_by_id = {obj.pk: getattr(obj, label_field) for obj in model.objects.filter(pk__in=ids)}
    out = []
    for r in rows:
        rid = r.get(key)
        if rid and rid in name_by_id:
            out.append({"id": rid, "nome": name_by_id[rid], "count": r["count"]})
    return out


def _string_facet(filters, exclude_key, field):
    """Faceta para campo string. Retorna [{value, nome, count}] com chaves uniformes."""
    f = {k: v for k, v in (filters or {}).items() if k != exclude_key}
    qs = Document.objects.filter(status="a")
    qs = _apply_filters(qs, f)
    rows = (
        qs.exclude(**{f"{field}__isnull": True})
        .exclude(**{field: ""})
        .values(field)
        .annotate(count=Count("id"))
        .order_by("-count")
    )
    return [{"value": r[field], "nome": r[field], "count": r["count"]} for r in rows]


def compute_facets(filters):
    """Devolve um dict com as facetas disponíveis dado o conjunto de filtros aplicado."""
    facets = {}

    # Hierarquia taxonômica
    facets["assuntos"] = _attach_names(
        list(_facet_counts(filters, "assunto_id", "assunto_id")), "assunto_id", Assunto
    )
    facets["categorias"] = _attach_names(
        list(_facet_counts(filters, "category_id", "category_id")), "category_id", NrCategory, label_field="name"
    )
    facets["subcategorias"] = _attach_names(
        list(_facet_counts(filters, "subcategoria_id", "subcategoria_id")),
        "subcategoria_id",
        Subcategoria,
    )
    facets["microcategorias"] = _attach_names(
        list(_facet_counts(filters, "microcategoria_id", "microcategoria_id")),
        "microcategoria_id",
        Microcategoria,
    )

    # Coleção e tipo de informação
    facets["colecoes"] = _attach_names(
        list(_facet_counts(filters, "topic_id", "topic_id")), "topic_id", Topic, label_field="name"
    )
    facets["tipos_informacao"] = _attach_names(
        list(_facet_counts(filters, "typeinform_id", "typeinform_id")),
        "typeinform_id",
        TypeInformation,
        label_field="name",
    )

    # Atributos string
    facets["tipologias"] = _string_facet(filters, "tipologia", "tipologia")
    facets["complexidades"] = _string_facet(filters, "complexidade", "complexidade")
    facets["permissoes"] = _string_facet(filters, "permissao", "permissao")

    # Ano: min/max para o slider (ignorando o próprio ano nos filtros)
    f_year = {
        k: v for k, v in (filters or {}).items()
        if k not in ("ano_min", "ano_max", "year_from", "year_to")
    }
    qs = Document.objects.filter(status="a", ano__isnull=False)
    qs = _apply_filters(qs, f_year)
    bounds = qs.aggregate(min=Min("ano"), max=Max("ano"))
    facets["ano_bounds"] = {
        "min": bounds["min"] or 2000,
        "max": bounds["max"] or 2025,
    }

    return facets
