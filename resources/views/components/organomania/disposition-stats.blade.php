<div class="row">
    @foreach ($statGroups as $stats)
        <dl @class(['mb-lg-0', 'mb-0' => $loop->last, 'col-12', 'col-lg'])>
            @foreach ($stats as $name => $value)
                <dt class="float-start me-2 fw-normal" style="min-width: 11em;">{{ $name }}</dt>
                <dd class="fw-bold">{!! $value !!}</dd>
            @endforeach
        </dl>
    @endforeach
</div>
