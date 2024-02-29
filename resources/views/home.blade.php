<link rel="icon" type="image/svg+xml" href="{{ asset('images/logo_vectorizado.svg') }}">
<link rel="stylesheet" href="{{ asset('css/intranet/home.css') }}">

@extends('layouts.app_new')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- {{ __('You are logged in!') }} --}}
                    @canany(['create-role', 'edit-role', 'delete-role'])
                        <a class="btn btn-primary" href="{{ route('roles.index') }}">
                            <i class="bi bi-person-fill-gear"></i> Gestionar Roles</a>
                    @endcanany
                    @canany(['create-user', 'edit-user', 'delete-user'])
                        <a class="btn btn-success" href="{{ route('users.index') }}">
                            <i class="bi bi-people"></i> Gestionar usuarios</a>
                    @endcanany
                    @canany(['create-component', 'edit-component', 'delete-component'])
                        <a class="btn btn-warning" href="{{ route('components.index') }}">
                            <i class="bi bi-house-gear"></i> Manage Components</a>
                    @endcanany
                    @canany(['create-tour', 'edit-tour', 'delete-tour'])
                        <a class="btn btn-secondary" href="{{ route('tours.index') }}">
                            <i class="bi bi-bezier2"></i> Manage Tours</a>
                    @endcanany
                    @canany(['edit-visit', 'delete-visit'])
                        <a class="btn btn-info" href="{{ route('visits.index') }}">
                            <i class="bi bi-calendar-week"></i> Manage Visits</a>
                    @endcanany

                    <p>&nbsp;</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
