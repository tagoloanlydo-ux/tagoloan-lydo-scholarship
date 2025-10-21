<?php

namespace App\Services;

use App\Models\Announce;
use Illuminate\Support\Facades\DB;

class AnnouncementService
{
    /**
     * Get all announcements ordered by date and creation time
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllAnnouncements()
    {
        return Announce::orderBy('date_posted', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get announcements for scholars
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getScholarAnnouncements()
    {
        return Announce::where('announce_type', 'Scholars')
            ->orderBy('date_posted', 'desc')
            ->get();
    }

    /**
     * Get announcements for applicants
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getApplicantAnnouncements()
    {
        return Announce::where('announce_type', 'Applicants')
            ->orderBy('date_posted', 'desc')
            ->get();
    }

    /**
     * Create a new announcement
     *
     * @param array $data
     * @return \App\Models\Announce
     */
    public function createAnnouncement(array $data)
    {
        return Announce::create([
            'lydopers_id' => $data['lydopers_id'],
            'announce_title' => $data['announce_title'],
            'announce_content' => $data['announce_content'],
            'announce_type' => $data['announce_type'],
            'date_posted' => now(),
        ]);
    }

    /**
     * Update an existing announcement
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Announce
     */
    public function updateAnnouncement($id, array $data)
    {
        $announcement = Announce::findOrFail($id);
        $announcement->update([
            'announce_title' => $data['announce_title'],
            'announce_type' => $data['announce_type'],
            'announce_content' => $data['announce_content'],
        ]);

        return $announcement;
    }

    /**
     * Delete an announcement
     *
     * @param int $announceId
     * @return bool
     */
    public function deleteAnnouncement($announceId)
    {
        return Announce::where('announce_id', $announceId)->delete();
    }

    /**
     * Find an announcement by ID
     *
     * @param int $id
     * @return \App\Models\Announce|null
     */
    public function findAnnouncement($id)
    {
        return Announce::find($id);
    }
}
