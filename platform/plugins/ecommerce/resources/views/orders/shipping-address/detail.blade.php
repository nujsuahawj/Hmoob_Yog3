<li>{{ $address->name }}</li>
<li>
    <a href="tel:{{ $address->phone }}">
        <span><i class="fa fa-phone-square cursor-pointer mr5"></i></span>
        <span>{{ $address->phone }}</span>
    </a>
</li>
<li>
    <div>{{ $address->address }}</div>
    <div>{{ $address->city }}</div>
    <div>{{ $address->state }}</div>
    <div>{{ $address->country_name }}</div>
    <div>
        <a target="_blank" class="hover-underline" href="https://maps.google.com/?q={{ $address->address }}, {{ $address->city }}, {{ $address->state }}, {{ $address->country_name }}">{{ __('See on maps') }}</a>
    </div>
</li>
