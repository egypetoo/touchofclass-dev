@if (($statistics ?? collect())->isNotEmpty())
    <div class="about-stats-wrapper bg-white pt-5 pb-4">
        <div class="container">
            <div class="about-stats-row d-flex align-items-center justify-content-between">
                @foreach ($statistics as $statistic)
                    @php($statisticParts = $statistic->statisticParts())
                    @if (! $loop->first)
                        <div class="about-stat-divider"></div>
                    @endif
                    <div class="about-stat-item text-center flex-fill" data-aos="zoom-in" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="stat-number-wrap d-flex align-items-baseline justify-content-center">
                            <span class="stat-number counter" data-target="{{ $statisticParts['number'] }}">{{ $statisticParts['number'] }}</span>
                            @if ($statisticParts['suffix'] !== '')
                                <span class="stat-plus">{{ $statisticParts['suffix'] }}</span>
                            @endif
                        </div>
                        <div class="stat-label">{{ $statistic->sub_title }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
