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
            <a href="/"><button class="btn btn-success btn-lg fetch-tiles">Fetch new tiles</button></a>
            
            <div class="tiles">
                @foreach($tiles as $tile)
                    <div class="tile">
                        <div class="letter">{{$tile['letter']}}</div>
                        <div class="value">{{$tile['value']}}</div>
                    </div>
                @endforeach

          
            </div>

            <div class="info-panel">
                @foreach($string_lengths as $strlen)
                    <h4>{{$strlen}} letter words</h4>

                    <div style="text-align:center;">
                        <table class="suggestions">
                            <tr>
                                <th>Word</th>
                                <th>Score</th>
                                <th>Highest value letter</th>
                                <th>Score if used on tripple letter square</th>
                            </tr>
                            @foreach($words as $word)
                                @if(strlen($word['word']) == $strlen)
                                    <tr>
                                        <td>{{$word['word']}}</td>
                                        <td>{{$word['value']}}</td>
                                        <td> {{$word['highest_value_letter']}}</td>
                                        <td>{{$word['tripple_letter_score']}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>   
                    </div>
                @endforeach
            </div>

            

        </div>
    </main>
</body>
</html>