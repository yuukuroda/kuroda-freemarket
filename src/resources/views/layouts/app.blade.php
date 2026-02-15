<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>coachtechfreemarket</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    @yield('css')
</head>

<body>
    <header class="header">
        <a href="{{ route('item.index') }}">
            <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="coachtech">
        </a>

        <ul class="header-nav">
            @if (Auth::check())
            <li class="header-nav__item">
                <form action="/logout" method="post">
                    @csrf
                    <button class="header-nav__button">ログアウト</button>
                </form>
            </li>
            @endif
        </ul>

        <a class="header-nav__link" href="{{ url('/mypage') }}">マイページ</a>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>