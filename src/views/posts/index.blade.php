@extends('bumble::layouts.master')

@section('content')
<section class="main-area">
    @include('bumble::partials.sidenav')
    <main class="main-content">
        <div class="header">
            <div class="flex jcc aic acc">
                <h2 class="header__title">{{ $model->getPluralName() }}</h2>
                @if ($model->isSoftDeleting())
                    <a href="{{ route(Config::get('bumble::admin_prefix') . '.' . $model->getPluralSlug() . '.trashed') }}" class="trashed-link">View Trashed</a>
                @endif
            </div>
            <a href="{{ route(Config::get('bumble::admin_prefix') . '.' . $model->getPluralSlug() . '.create') }}" class="btn-create">Create {{{ str_singular($model->getModelName()) }}} &#8594;</a>
        </div>

        @include('bumble::partials.messages')

        @unless ($entries->isEmpty())
        <table class="table">
            <thead>
                <tr>
                    <th class="id">ID</th>

                    @foreach ($model->getFields() as $field)
                        @if ($field->showInListing())
                            @if ($field->getColumn() == 'active' || $field->getFieldType() == "BooleanField")
                                <th class="active-status">{{ Str::title($field->getName()) }}</th>
                            @elseif ($field->getFieldType() == "HasOneField")
                                <th>{{ $field->getTitle() }}</th>
                            @else
                                <th>{{ Str::title($field->getName()) }}</th>
                            @endif
                        @endif
                    @endforeach

                    <th class="">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                <tr>
                    <td class="id">{{ $entry->id }}</td>
                    @foreach ($model->getFields() as $field)
                        @unless ($field->showInListing() == false)
                            @if ($field->getFieldType() == 'TextField')
                                <td>{{ $entry->{$field->getColumn()} }}</td>
                            @elseif ($field->getFieldType() == 'SlugField')
                                <td><code>{{ $entry->{$field->getColumn()} }}</code></td>
                            @elseif ($field->getFieldType() == 'DateTimeField')
                                <td><code>{{ $field->display($entry->{$field->getColumn()}) }}</code></td>
                            @elseif ($field->getFieldType() == 'BooleanField')
                                <td class="active-status"><i class="badge {{ active_color($entry->active) }}"></i></td>
                            @elseif ($field->getFieldType() == 'DropdownField')
                                <td>{{ $field->getValue($entry->{$field->getColumn()}) }}</td>
                            @elseif ($field->getFieldType() == 'TextareaField')
                                <td>{{ str_limit($entry->{$field->getColumn()}, $limit = 40, $end = '&hellip;') }}</td>
                            @elseif ($field->getFieldType() == 'ImageField')
                                <td>{{ $entry->{$field->getColumn()} }}</td>
                            @elseif ($field->getFieldType() == 'HasOneField')
                                <td>{{ $model->{$field->method()}()->getRelated()->whereId($entry->{$field->getColumn()})->pluck($field->getTitleOption()) }}</td>
                            @else
                                <td>{{ $entry->{$field->getColumn()} }}</td>
                            @endif
                        @endunless
                    @endforeach
                    <td width="90">
                        <div class="inline-flex">
                            <a href="{{ route(Config::get('bumble::admin_prefix').'.'.$model->getPluralSlug().'.edit', ['id' => $entry->id]) }}" class="edit-post">Edit</a>
                            {{ Form::open(['method' => 'delete', 'route' => [Config::get('bumble::admin_prefix').'.'.$model->getPluralSlug().'.destroy', $entry->id]]) }}
                            {{ Form::hidden('id', $entry->id) }}
                            {{ Form::button('', ['type' => 'submit', 'class' => 'delete-post js-delete-post']) }}
                            {{ Form::close() }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pv">
        {{ $entries->links() }}
        </div>
        @else
            <div class="info-box">
                <p>You haven&rsquo;t create any {{ strtolower($model->getPluralName()) }} yet.
                    <a href="{{ route(Config::get('bumble::admin_prefix') . '.' . $model->getPluralSlug() . '.create') }}">Create a new {{{ str_singular($model->getModelName()) }}} &#8594;</a>
                </p>
            </div>
        @endunless
    </main>
</section>
@stop
