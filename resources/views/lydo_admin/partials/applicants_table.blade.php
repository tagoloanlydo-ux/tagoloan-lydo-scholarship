@forelse($applicants as $applicant)
    <tr class="hover:bg-gray-50 transition-colors duration-200">
        <td class="px-4 py-3 border border-gray-200 text-center">
            <input type="checkbox"
                   name="selected_applicants"
                   value="{{ $applicant->applicant_email }}"
                   data-applicant-id="{{ $applicant->applicant_id }}"
                   class="applicant-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        </td>
        <td class="px-4 py-3 border border-gray-200 text-left">
            {{ $applicant->applicant_lname }}, {{ $applicant->applicant_fname }}
            @if($applicant->applicant_mname)
                {{ substr($applicant->applicant_mname, 0, 1) }}.
            @endif
            @if($applicant->applicant_suffix)
                {{ $applicant->applicant_suffix }}
            @endif
        </td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $applicant->applicant_brgy }}</td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $applicant->applicant_email }}</td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $applicant->applicant_school_name }}</td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $applicant->applicant_course ?? 'N/A' }}</td>
        <td class="px-4 py-3 border border-gray-200 text-center">{{ $applicant->applicant_acad_year ?? 'N/A' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-4 py-3 border border-gray-200 text-center text-muted" style="font-size: 15px; font-weight: bold;">
            No applicants found
        </td>
    </tr>
@endforelse
