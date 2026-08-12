<!DOCTYPE html>
<html lang="sl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin prijava</title>
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    </head>
    <body>
        <div class="admin-login-wrapper">
            <div class="admin-box">
                <h1>Admin prijava</h1>

                @if ($errors->any())
                    <div class="admin-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf

                    <div class="admin-field">
                        <label for="username">Uporabniško ime</label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
                    </div>

                    <div class="admin-field">
                        <label for="password">Geslo</label>
                        <input id="password" type="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-block">Prijava</button>
                </form>
            </div>
        </div>
    </body>
</html>
