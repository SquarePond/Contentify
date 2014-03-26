{{-- Form Template generated by SmartFormGenerator --}}

{{ Form::errors($errors) }}

@if (isset($entity))
{{ Form::model($entity, ['route' => ['admin.news.update', $entity->id], 'method' => 'PUT']) }}
@else
{{ Form::open(['url' => 'admin/news']) }}
@endif
    {{ Form::smartText('title', trans('app.title')) }}
    {{ Form::smartSelectRelation('newscat', 'News '.trans('app.category'), $modelName, null, true) }}
    {{ Form::smartSelectRelation('creator', trans('app.author'), $modelName, user()->id, true) }}
    
    {{ Form::smartTextarea('intro', trans('news.intro')) }}
    {{ Form::smartTextarea('text', trans('app.text')) }}

    {{ Form::smartDateTime('published_at', trans('news::publish_at')) }}
    {{ Form::smartCheckbox('published', trans('app.published'), true) }}
    {{ Form::smartCheckbox('internal', trans('app.internal')) }}
    {{ Form::smartCheckbox('enable_comments', trans('app.enable_comments'), true) }}

    {{ Form::actions() }}
{{ Form::close() }}