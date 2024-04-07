@php
    $records = isset($results['records']) ? $results['records'] : null;
    $photos = isset($results['photos']) ? $results['photos'] : null;
    $hashed2024 = isset($results['hash']['hashed2024']) ? $results['hash']['hashed2024'] : null;
    $count = isset($results['count']) ? $results['count'] : null;
    $search = isset($results['search']) ? $results['search'] : null;
    $matches = strtolower(trans_choice('ui.Match', ($count > 1 || $count == 0) ? 2 : 1));
    $locale = app()->getLocale();
@endphp
<div class="table" style="color:#0f0f0f" id="searchResultsTable">
    <table  class="table-responsive table-condensed medium-text">
        <thead>
            <tr>
                <td style="color:black;">
                    "{{$search}}": {{$count}} {{$matches}}
                </td>
            </tr>
        </thead>
    </table>
    <table class="table table-striped">
        <tbody>
        @if (isset($hashed2024))
            <tr>
                <td>
                    <div id="flash" class="form-group">
                        <span id='entry2024'>{{$hashed2024}}</span>
                        <a href='#' onclick="javascript:clipboardCopy(event, 'entry2024', 'entry2024')";>
                            <span id="" class="glyphCustom glyphicon glyphicon-copy" style="color:DarkGreen; font-size:1.1em; margin-left:5px; display:{{isset($hashed2024) && strlen($hashed2024) > 0 ? 'default' : 'none'}}"></span>
                        </a>		
                    </div>	
                </td>
            </tr>
        @else
            @if (isset($records))
                @foreach($records as $record)
                    <tr>
                        <td><a class="m-0" href="/entries/{{$record->permalink}}">{{$record->title}}</a></td>
                    </tr>
                @endforeach
            @endif
            @if (isset($photos))
                @foreach($photos as $record)
                    <tr>
                        <td>
                            Photo: <a class="m-0" href="/photos/{{$record->permalink}}">{{$record->alt_text}}</a>
                        </td>
                    </tr>
                @endforeach
            @endif
        @endif
        </tbody>
    </table>
</div>

