<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Kısa bir okuma molası · İzEdebiyat</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f2f0e9;color:#243831;font-family:Arial,sans-serif}
        main{max-width:880px;margin:48px auto;padding:0 24px}header{display:flex;justify-content:space-between;align-items:center;margin-bottom:44px}
        .brand{font-family:Georgia,serif;font-size:27px;font-weight:bold}.label{font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#69766d}
        article{background:#fffefb;border:1px solid #dfE3d8;border-radius:20px;padding:40px;box-shadow:0 14px 50px #24383108}
        .eyebrow{font-size:11px;letter-spacing:2px;color:#6f8068;font-weight:bold}h1{font:normal 38px/1.15 Georgia,serif;margin:16px 0}
        p{font-size:15px;line-height:1.7;color:#657169}.reading{margin:28px 0;border:1px solid #e5e2d6;border-radius:12px;overflow-x:auto;background:#fbf8f0}
        .reading img{display:block;width:100%;min-width:620px;height:auto}.caption{font-size:12px;color:#758172;margin-top:-16px;margin-bottom:28px}
        label{display:block;font-size:18px;font-weight:bold;margin:12px 0}input{width:100%;padding:16px;border:1px solid #bfc9bb;border-radius:8px;font-size:18px;background:#fff}
        input:focus{outline:2px solid #688362;outline-offset:2px}.actions{display:flex;align-items:center;gap:24px;margin-top:20px}
        button{background:#294c3c;color:white;border:0;padding:16px 24px;border-radius:8px;font-size:15px;cursor:pointer}a{color:#566b53;font-size:14px}
        footer{text-align:center;font-size:12px;color:#778274;margin:24px}.error{background:#fff0e6;padding:12px;border-radius:8px;color:#853c23}
        @media(max-width:600px){main{margin:24px auto;padding:0 16px}header{margin-bottom:24px}article{padding:24px 18px}h1{font-size:30px}.label{display:none}.actions{gap:16px}}
    </style>
</head>
<body>
<main>
    <header><div class="brand">İzEdebiyat<span style="color:#849573">.</span></div><div class="label">Edebiyatın buluşma noktası</div></header>
    <article>
        <div class="eyebrow">OKUYARAK DEVAM EDİN</div>
        <h1>Kısa bir okuma molası.</h1>
        <p>Sitemizi otomatik ziyaretlerden korumak için küçük bir doğrulama yapıyoruz.<br>Görseldeki metni okuyun ve aşağıdaki soruyu yanıtlayın.</p>
        <div class="reading"><img src="{{ $imagePath }}" alt="Doğrulama için yedi satırlık Türkçe okuma görseli"></div>
        <div class="caption">Satırlar soldan numaralandırılmıştır. Noktalama işaretlerini yazmanız gerekmez.</div>
        @if(session('challenge_error'))<p class="error" role="alert">{{ session('challenge_error') }}</p>@endif
        <form method="post" action="/human-check" autocomplete="off">
            @csrf
            <input type="hidden" name="challenge_id" value="{{ $challengeId }}">
            <label for="answer">{{ $question }}</label>
            <input id="answer" name="answer" type="text" maxlength="100" placeholder="Yanıtınızı buraya yazın" required spellcheck="false" autocapitalize="none">
            <div class="actions"><button type="submit">Doğrula ve devam et →</button><a href="/human-check">Başka bir soru</a></div>
        </form>
    </article>
    <footer>Bu doğrulama sayfasında analiz ve takip araçları kullanılmaz.</footer>
</main>
</body>
</html>
