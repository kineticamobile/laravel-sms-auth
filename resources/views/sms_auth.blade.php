@extends('layouts.app')

@section('content')
    <div class="container mt-4">
       
        <p class="fw-bold fs-5">Acceder</p>
        <form method="POST" action="{{ route('sms_login') }}">
            @csrf
            @method('POST')
            <div class="mb-3 row">
                <label for="phone" class="col-12 col-form-label text-muted">{{ __('TELEFONO') }}</label>

                <div class="col-12">
                    <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="tel" autofocus>

                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="mb-0 row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">
                        {{ __('Siguiente') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
