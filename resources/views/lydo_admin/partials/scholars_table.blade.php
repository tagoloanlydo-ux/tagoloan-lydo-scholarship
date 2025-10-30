@forelse($scholars as $scholar)
    <tr class="hover:bg-gray-50 transition-colors duration-200">
        <td class="px-4 py-3 border border-gray-200 text-center">
            <input type="checkbox" 
                   name="selected_scholars" 
                   value="{{ $scholar->applicant_email }}"
                   data-scholar-id="{{ $scholar->scholar_id }}"
                   class="scholar-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        </td>
        <td class="px-4 py-3 border border-gray-200 text-left">
            {{ $scholar->applicant_lname }}, {{ $scholar->applicant_fname }}
            @if($scholar->applicant_mname)
                {{ substr($scholar->applicant_mname, 0, 1) }}.
            @endif
            @if($scholar->applicant_suffix)
                {{ $scholar->applicant_suffix }}
            @endif
        </td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $scholar->applicant_brgy }}</td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $scholar->applicant_email }}</td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $scholar->applicant_school_name }}</td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $scholar->applicant_course }}</td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $scholar->applicant_acad_year ?? 'N/A' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-4 py-3 border border-gray-200 text-center text-muted" style="font-size: 15px; font-weight: bold;">
            No scholars found
        </td>
    </tr>
@endforelse

@if($scholars->hasPages())
    <tr>
        <td colspan="7" class="px-4 py-3 border border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Showing {{ $scholars->firstItem() }} to {{ $scholars->lastItem() }} of {{ $scholars->total() }} entries
                </div>
                <div class="flex space-x-2">
                    {{ $scholars->links('pagination::simple-tailwind') }}
                </div>
            </div>
        </td>
    </tr>
@endif