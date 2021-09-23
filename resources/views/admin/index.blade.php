@extends('admin.template');

@section('title', 'B7BIO | HOME');

@section('content')
    
    <header>
        <h2>Suas Páginas</h2>
    </header>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th width="20">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td>{{$page->op_title}} ({{$page->slug}})</td>
                    <td>
                        <a href="{{url('/'.$page->slug)}}" targer="_blank">Abrir</a>
                        <a href="{{url('/admin/'.$page->slug.'/links')}}" targer="_blank">Links</a>
                        <a href="{{url('/admin/'.$page->slug.'/design')}}" targer="_blank">Aparência</a>
                        <a href="{{url('/admin/'.$page->slug.'/stats')}}" targer="_blank">Estatísticas</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection