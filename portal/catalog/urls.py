from django.urls import path

from . import views

app_name = "catalog"

urlpatterns = [
    path("", views.home, name="home"),
    path("busca/", views.search, name="search"),
    path("documento/<str:code>/", views.document_detail, name="document_detail"),
    path("colecoes/", views.collection_list, name="collection_list"),
    path("colecao/<int:topic_id>/", views.collection_detail, name="collection_detail"),
    path("download/<str:code>/", views.download, name="download"),
    path("sobre/", views.about, name="about"),
]
