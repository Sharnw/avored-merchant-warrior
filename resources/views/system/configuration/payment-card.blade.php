@php
    $data = collect();
    $data->put('mw_card_status', $repository->getValueByCode('mw_card_status'));
@endphp

<merchant-warrior-config :data="{{ $data }}"></merchant-warrior-config>

@push('scripts')
<script src="{{ asset('avored-admin/js/merchant-warrior.js') }}"></script>
@endpush
