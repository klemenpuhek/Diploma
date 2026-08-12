<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin prijava</title>
    <link rel="stylesheet" href="{{ asset('css/adminLogin.css') }}">
</head>
<body>

    <form action="{{ route('login') }}" method="POST" class="loginForm">
        @csrf
        <h1>Admin prijava</h1>

        <input type="text" name="username" placeholder="Uporabniško ime" value="{{ old('username') }}">
        <input type="password" name="password" placeholder="Geslo">

        @error('username')
            <p class="errorMessage">{{ $message }}</p>
        @enderror

        <button type="submit">Prijava</button>
    </form>

</body>
</html>