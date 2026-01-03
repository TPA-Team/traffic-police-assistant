from django.urls import path
from .views import read_plate

urlpatterns = [
    path('read-plate/', read_plate),
]

