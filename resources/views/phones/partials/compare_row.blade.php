<tr>
    <td class="p-3 text-sm font-medium text-gray-700 dark:text-gray-300 border-r dark:border-gray-600">{{ $label }}</td>
    @foreach($phones as $phone)
        @php
            $value = data_get($phone, $prop);
        @endphp
        <td class="p-3 text-sm text-gray-800 dark:text-gray-200 border-r dark:border-gray-600 text-center">
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
