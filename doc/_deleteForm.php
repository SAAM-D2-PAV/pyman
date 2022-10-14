

<form method="post" action="{{ path('task_document_remove', {'tid':task.id, 'id': document.id}) }}" onsubmit="return confirm('Valider la suppression du document ?');">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ document.id) }}">
    <button class="btn-custom-lg bg-amour text-white"><i class="far fa-trash-alt"></i></button>
</form>
