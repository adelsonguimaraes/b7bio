@extends('admin.page')

@section('body')

    <a href="{{url('/admin/'.$page->slug.'/newlink')}}" class="bigbutton">Novo Link </a>

    <ul id="links">
        @foreach ($links as $link)
            <li class="link--item" data-id="{{$link->id}}">
                <div class="link--item-order">
                    <img src="{{url('/assets/images/sort.png')}}" alt="Ordenar" width="18"/>
                </div>
                <div class="link--item-info">
                    <div class="link--item-title">{{$link->title}}</div>
                    <div class="link--item-href">{{$link->href}}</div>
                </div>
                <div class="link--item-buttons">
                    <a href="{{url('/admin/'.$page->slug.'/editlink/'.$link->id)}}">Editar</a>
                    <a href="{{url('/admin/'.$page->slug.'/dellink/'.$link->id)}}">Excluir</a>
                </div>
            </li>
        @endforeach
    </ul>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        new Sortable(document.querySelector('#links'), {
            animation: 150,
            onEnd: async (e) => {
                const id = e.item.getAttribute('data-id');
                console.log(id);
                const link = `{{url('/admin/linkorder/${id}/${e.newIndex}')}}`;
                await fetch(link);
                window.location.reload();
            }
        });
    </script>
@endsection