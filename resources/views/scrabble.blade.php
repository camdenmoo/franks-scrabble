<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
   
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css?t='.time()) }}" rel="stylesheet">

    <title>Frank's Scrabble Game</title>

</head>
<body>

    <header>
        <div class="container">
            <h1>Frank's Scrabble Game</h1>
        </div>
    </header>

    <main>
        <div class="container">
            <a href="/"><button class="btn btn-success fetch-tiles">Fetch new tiles</button></a>
            
            <div class="tiles">
                @foreach($tiles as $tile)
                    <div class="tile">
                        <div class="letter">{{$tile['letter']}}</div>
                        <div class="value">{{$tile['value']}}</div>
                    </div>
                @endforeach

          
            </div>

            <div>
                @foreach($words as $word)
                    {{$word['word']}} - {{$word['value']}}
                    <br>
                @endforeach   
            </div>

        </div>
    </main>



    
</body>
</html>