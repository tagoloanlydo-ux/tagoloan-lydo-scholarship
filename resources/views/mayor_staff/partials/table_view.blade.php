<tbody class="bg-white">
    @php $count = 1; @endphp
    @forelse($tableApplicants as $index => $app)
        <tr class="border-b border-gray-200 hover:bg-blue-50 transition-colors duration-200">
            <td class="px-6 py-4 text-center">{{ $count++ }}</td>
            <td class="px-6 py-4 text-center font-medium">
                {{ ucfirst(strtolower($app->applicant->applicant_lname)) }}, 
                {{ ucfirst(strtolower($app->applicant->applicant_fname)) }}
                @if(!empty($app->applicant->applicant_mname))
                    {{ strtoupper(substr($app->applicant->applicant_mname, 0, 1)) }}.
                @endif
                @if(!empty($app->applicant->applicant_suffix))
                    {{ ucfirst(strtolower($app->applicant->applicant_suffix)) }}
                @endif
            </td>
            <td class="px-6 py-4 text-center">{{ $app->applicant->applicant_brgy }}</td>
            <td class="px-6 py-4 text-center">{{ $app->applicant->applicant_gender }}</td>
            <td class="px-6 py-4 text-center date-format">{{ $app->applicant->applicant_bdate }}</td>
            <td class="px-6 py-4 text-center">
                <button type="button"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm"
                    onclick="openApplicationModal({{ $app->application_personnel_id }}, 'pending')">
                    Review Applications
                </button>
            </td>
            <td class="px-6 py-4 text-center">
                <button type="button" 
                        onclick="confirmDeletePending({{ $app->application_personnel_id }}, '{{ $app->applicant->applicant_fname }} {{ $app->applicant->applicant_lname }}')" 
                        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 text-sm font-medium transition-colors duration-200 shadow-sm">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="px-6 py-8 text-center text-gray-500 bg-gray-50">No pending applications found.</td>
        </tr>
    @endforelse
</tbody>