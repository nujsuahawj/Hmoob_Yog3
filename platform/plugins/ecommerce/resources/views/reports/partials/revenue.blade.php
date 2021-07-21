<style>
    .change-date-range {
        position: absolute;
        top: -52px;
        right: 80px;
    }
    .change-date-range .btn {
        padding: 5px 10px;
        border-radius: 0 !important;
    }
</style>
<div class="col-12">
    <div class="btn-group change-date-range">
        <a class="btn btn-sm btn-secondary" href="javascript:;" data-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-filter" aria-hidden="true"></i>
            <span>{{ $defaultRange }}</span>
            <i class="fa fa-angle-down "></i>
        </a>
        <ul class="dropdown-menu float-right">
            <li>
                <a href="{{ route('ecommerce.report.revenue', ['filter' => 'date']) }}">
                    {{ __('Today') }}
                </a>
            </li>
            <li>
                <a href="{{ route('ecommerce.report.revenue', ['filter' => 'week']) }}">
                    {{ __('This week') }}
                </a>
            </li>
            <li>
                <a href="{{ route('ecommerce.report.revenue', ['filter' => 'month']) }}">
                    {{ __('This month') }}
                </a>
            </li>
            <li>
                <a href="{{ route('ecommerce.report.revenue', ['filter' => 'year']) }}">
                    {{ __('This year') }}
                </a>
            </li>
        </ul>
    </div>
    @if (!empty($chartTime))
        {!! $chartTime->renderChart() !!}
    @else
        @include('core/dashboard::partials.no-data')
    @endif
</div>

