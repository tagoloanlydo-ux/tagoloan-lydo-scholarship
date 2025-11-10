tutunog ang /notification/blade.wav kapag may bagong $query->where("ap.initial_screening", "Pending"); at   $filteredApplications = $appItems->filter(function ($a) {
            $screening = data_get($a, 'screening') ?? data_get($a, 'initial_screening') ?? data_get($a, 'initialScreening');
            $status = data_get($a, 'status');
            $remarks = data_get($a, 'remarks');

            return $screening === 'Reviewed'
                && $status === 'Pending'
                && in_array($remarks, ['Poor', 'Ultra Poor']); tapos malalagay sa notification bell kung anong name ng bagong pasok tapos kapag na buksan na ang notification bell tapos e close ko magiging unread na yung loob ng notifaction tapos mag cocount to zero nanaman ang badge mag kakaroon lang kapag may bagong pasok tapos nabuksan ko na sa isang blade tapos nag turn to zero na ma view din sa ibang page na wala na itong notification pero kung mayroong isang notfication tapos hindi ko pa ito na buksan tapos pupunta ako sa ibang page hindi mayroon 1 notfication parin ito separte ang script sa html
