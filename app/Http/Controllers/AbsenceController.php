<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Absence;
use Illuminate\Http\Request;
use App\Models\Restallowance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    use HelperTrait;
    /** Types of vacations
     *
     * اعتيادية	Regular
     * بدل راحة	Rest allowance
     * عارضة	Casual
     * اجازة عمرة	Umrah leave
     * تجنيد	Recruitment
     * اجازة وضع	Maternity leave
     * اجازة تعويضية	Compensatory leave
     * اخري	Other
     */
    public function index()
    {
        if (request()->countYear) {
            // الحصول على تاريخ بداية الشهر الحالي
            $startOfMonth = Carbon::now()->startOfMonth();

            // استعلام Eloquent للحصول على عدد السجلات من بداية الشهر
            $count = auth()->user()->absences->where('created_at', '>=', $startOfMonth)->count();

            return response()->json(['count' => $count]);
        }
        if (request()->countMonth) {
            // الحصول على تاريخ بداية السنة الحالية
            $startOfYear = Carbon::now()->startOfYear();

            // استعلام Eloquent للحصول على عدد السجلات من بداية السنة
            $count = auth()->user()->absences->where('created_at', '>=', $startOfYear)->count();

            return response()->json(['count' => $count]);
        }
        if (request()->countPerMonth) {
            // الحصول على تاريخ بداية السنة الحالية
            $startOfYear = Carbon::now()->startOfYear();

            // الحصول على تاريخ نهاية السنة الحالية
            $endOfYear = Carbon::now()->endOfYear();

            // استعلام Eloquent للحصول على عدد السجلات لكل شهر خلال السنة
            $countsPerMonth = auth()->user()->absences->whereBetween('created_at', [$startOfYear, $endOfYear])
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupByRaw('MONTH(created_at)')
                ->get();

            return response()->json(['countsPerMonth' => $countsPerMonth]);
        }
        $user_id = Auth::id();

        // $currentMonth = date('m');  // الحصول على الشهر الحالي
        $currentYear = date('Y');  // الحصول على السنة الحالي

        $absences = Absence::query()
            ->where('user_id', $user_id)
            ->whereYear('date', $currentYear)
            ->get();
        return $absences;
    }
    public function store(Request $request)
    {
        $dailyFife = request()->dailyFife;
        $startDate = Carbon::parse(request()->start_date);
        $endDate = Carbon::parse(request()->end_date);
        $dates = $startDate->daysUntil($endDate)->toArray();
        $exists = Absence::whereIn('date', $dates)->where('user_id', Auth::id())->pluck('date');
        // return ['$exists->isEmpty()' => $exists->isEmpty(), '$dailyFife' => $dailyFife, 'if 1' => $exists->isEmpty() || $dailyFife ? 'tmam' : 'no', 'if 2' => count($exists) <= 1 ? 'tmam' : 'no', 'count($exists) <= 1' => count($exists) <= 1, 'count($exists)' => count($exists)];
        if ($exists->isEmpty() || $dailyFife) {
            // return count($exists);
            if (count($exists) <= 1) {
                DB::beginTransaction();
                try {
                    $Type = request()->Type;
                    $rest_id = request()->rest_id;
                    if ($Type == 'Rest allowance') {
                        $this->addAbsenceTypeRest($dailyFife, $rest_id, request());
                    } elseif ($Type == 'Regular' ||  $Type == 'Casual') {
                        $this->addAbsenceTypeRegular($dailyFife, request());
                    } else {
                        $absences = $this->saveAbsences(request());
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    // DB::rollback();
                    return response()->json("حدث خطأ أثناء تنفيذ العملية . يرجى المحاولة مرة أخرى.", 409);
                }
            } else {
                return response()->json($exists, 409);
            }
        } else {
            return response()->json($exists, 409);
        }
    }
    public function show(Absence $absence)
    {
        //
    }
    public function update(Request $request, Absence $absence)
    {
        //
    }
    public function destroy(Absence $absence)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            if ($absence->Type == 'Rest allowance') {
                $restallowance = Restallowance::findOrFail($absence->rest_id);
                $restallowance->update([
                    'state' => true,
                ]);
                $restBalance = $user->restBalance;
                $restBalance->balance += 1;
                $restBalance->save();
            } elseif ($absence->Type == 'Regular') {
                $regularBalance = $user->regularBalance;
                $regularBalance->balance += 1;
                $regularBalance->save();
            }
            $absence->delete();
            // return true;
            DB::commit();
            return $absence;
        } catch (\Exception $e) {
            DB::rollback();
            return "حدث خطأ أثناء حذف المستخدم. يرجى المحاولة مرة أخرى.";
        }
    }
}