@extends('backoffice.layouts.app')

@section('title', 'System Logs')
@section('header_title', 'Centralized System Logs')

@section('content')
    <div class="card" style="padding: 0; overflow: hidden; height: calc(100vh - 180px);">
        <iframe 
            src="{{ $logUrl }}" 
            style="width: 100%; height: 100%; border: none;"
            title="Dozzle Log Viewer">
        </iframe>
    </div>
@endsection
