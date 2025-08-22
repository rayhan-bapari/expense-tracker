{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}


@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <form action="{{ route('login') }}" method="POST" id="loginform" class="form-horizontal mt-3 needs-validation" novalidate>
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-success text-white h-100" id="basic-addon1">
                            <i class="mdi mdi-account fs-4"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control form-control-lg" placeholder="email address" name="email"
                        required />

                    <div class="invalid-feedback">
                        Email is required
                    </div>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-warning text-white h-100" id="basic-addon2">
                            <i class="mdi mdi-lock fs-4"></i>
                        </span>
                    </div>
                    <input type="password" class="form-control form-control-lg" placeholder="Password" name="password"
                        required />

                    <div class="invalid-feedback">
                        Password is required
                    </div>
                </div>
            </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="row">
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" style="cursor: pointer;">
                    <label class="form-check-label text-white" for="remember" style="cursor: pointer;">
                        Remember Me
                    </label>
                </div>
            </div>
        </div>

        <div class="row border-top border-secondary">
            <div class="col-12">
                <div class="form-group">
                    <div class="pt-3 d-flex align-items-center justify-content-between">
                        @if (Route::has('password.request'))
                            <a class="btn btn-info" href="{{ route('password.request') }}">
                                <i class="mdi mdi-lock fs-4 me-1"></i> {{ __('Lost password?') }}
                            </a>
                        @endif
                        <button class="btn btn-success text-white @if (!Route::has('password.request')) w-100 @endif"
                            type="submit">
                            Login
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
