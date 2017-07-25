<div class="bus-remark-section">
    <p class="gap-line">{{ \Carbon\Carbon::createFromTimestamp($ticket['dept_at'])->format('m月d日') }}</p>
    @foreach( $commentItems as $commentItem )
        <div class="brc-item">
            <span class="brc-title">{{ $commentItem['title'] }}</span>
            <div class="remark-star js_bus_score_item"  data-key="{{ $commentItem['key'] }}" data-score="{{ isset($commentItem['default_score']) ?$commentItem['default_score'] : 5  }}" data-src="{{ isset($commentItem['score']) ? $commentItem['score'] : 0 }}">
                <i class="hl-star "></i>
                <i class="hl-star"></i>
                <i class="hl-star"></i>
                <i class="hl-star"></i>
                <i class="hl-star"></i>
            </div>
        </div>
    @endforeach
    {{--<div class="brc-item">--}}
        {{--<span class="brc-title">车内环境</span>--}}
        {{--<div class="remark-star" id="js_bus_level" data-score="5" data-src="{{ isset($ticket['comment']['bus_env_level']) ? $ticket['comment']['bus_env_level'] : 0 }}">--}}
            {{--<i class="hl-star "></i>--}}
            {{--<i class="hl-star"></i>--}}
            {{--<i class="hl-star"></i>--}}
            {{--<i class="hl-star"></i>--}}
            {{--<i class="hl-star"></i>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<div class="brc-item">--}}
        {{--<span class="brc-title">着装礼仪</span>--}}
        {{--<div class="remark-star" id="js_driver_level" data-score="5" data-src="{{ isset($ticket['comment']['driver_svc_level']) ? $ticket['comment']['driver_svc_level'] : 0 }}">--}}
            {{--<i class="hl-star "></i>--}}
            {{--<i class="hl-star"></i>--}}
            {{--<i class="hl-star"></i>--}}
            {{--<i class="hl-star"></i>--}}
            {{--<i class="hl-star"></i>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<div class="brc-item">--}}
        {{--<span class="brc-title">文明行车</span>--}}
        {{--<div class="remark-star" id="js_level" data-score="5" data-src="{{ isset($ticket['comment']['level']) ? $ticket['comment']['level'] : 0 }}">--}}
            {{--<i class="hl-star "></i>--}}
            {{--<i class="hl-star"></i>--}}
            {{--<i class="hl-star"></i>--}}
            {{--<i class="hl-star"></i>--}}
            {{--<i class="hl-star"></i>--}}
        {{--</div>--}}
    {{--</div>--}}

    <textarea class="remark-desc" id="js_comment"  {{ isset($ticket['comment']) ? 'readonly' : ''  }} placeholder="说点什么吧...">{{ isset($ticket['comment']) ? $ticket['comment']['comment'] : ''  }}</textarea>
    @if(!isset($ticket['comment']))
        <div class="remark-text-count">
            <span class="js_text_count">0</span>/60
        </div>
        <input id="js_ticket_id" type="hidden" value="{{ $ticket['id'] }}">
        <button class="btn btn-primary text-center  full-width font-16 mt-20 mb-20" data-time="{{ $ticket['dept_at'] }}" id="js_remark_btn">评价</button>
    @endif

</div>