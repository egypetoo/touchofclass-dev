@php
    $designImages = Path::projectDesignImages();
    $cardImage = $project->coverUrl() ?: Path::imagesPath($designImages[$index % count($designImages)]);
@endphp

<a href="{{ route('projects.show', $project) }}" class="industry-bento-card industry-bento-grid">
    <img src="{{ $cardImage }}" alt="{{ $project->title }}" class="industry-bento-img" loading="lazy" />
    <div class="industry-bento-overlay"></div>
    <div class="industry-bento-content">
        <div class="industry-bento-text">
            <h3 class="industry-bento-title">{{ $project->title }}</h3>
            @if ($project->short_text)
                <p class="industry-bento-desc">{{ $project->short_text }}</p>
            @endif
        </div>
        <div class="industry-bento-btn"><i class="fas fa-arrow-right"></i></div>
    </div>
</a>
