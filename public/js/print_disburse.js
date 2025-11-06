// Print Records PDF Button
document.getElementById('recordsPrintPdfBtn').addEventListener('click', function() {
    const search = document.querySelector('input[name="search"]').value;
    const barangay = document.querySelector('select[name="barangay"]').value;
    const academicYear = document.querySelector('select[name="academic_year"]').value;
    const semester = document.querySelector('select[name="semester"]').value;
    
    let url = '{{ route("LydoAdmin.generateDisbursementRecordsPdf") }}?';
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (barangay) params.append('barangay', barangay);
    if (academicYear) params.append('academic_year', academicYear);
    if (semester) params.append('semester', semester);
    
    window.open(url + params.toString(), '_blank');
});