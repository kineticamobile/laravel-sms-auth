@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <p class="fw-bold fs-5">Verificar</p>
    <form method="POST" action="{{ route('sms_verify') }}">
        @csrf
        @method('POST')
        <div class="mb-3 row">
            <label for="token" class="col-md-4 col-form-label text-md-end text-muted">{{ __('Código') }}</label>

            <div class="col-md-6">
                <input id="token" type="text" class="form-control @error('token') is-invalid @enderror" maxlength="{{config('sms-auth.token_length',8)}}" name="token" value="{{ old('token') }}" required autofocus>

                @error('token')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="mb-0 row">
            <div class="col-12 offset-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    {{ __('Enviar') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
