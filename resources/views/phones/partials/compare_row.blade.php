<tr>
    <td class="p-3 text-sm font-medium text-gray-700 border-r">{{ $label }}</td>
    @foreach($phones as $phone)
        @php
            $value = data_get($phone, $prop);
        @endphp
        <td class="p-3 text-sm text-gray-800 border-r text-center">
            @if(isset($format) && $format == 'number')
                {{ number_format($value) }}
            @elseif($value === null || $value === '')
                -
            @else
                {{ $value }} {{ $suffix ?? '' }}
            @endif
        </td>
    @endforeach
</tr>
