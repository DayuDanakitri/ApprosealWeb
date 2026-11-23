
<div class="stat-card">

    <div class="stat-card-top">

        <span class="stat-label">{{ $stat['label'] }}</span>

        <div class="stat-icon {{ $stat['color'] }}">
            <i class="bi {{ $stat['icon'] }}"></i>
        </div>

    </div>

    <div class="stat-value">
        {{ number_format($stat['value']) }}
    </div>

</div>
