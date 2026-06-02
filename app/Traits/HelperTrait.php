<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\Absence;
use App\Models\Restallowance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait HelperTrait
{

    public function resetAllownesState($restallowance, $state)
    {
        $restallowance->update([
            'state' => $state,
        ]);
        return $restallowance->state;
    }

    public function updateBalance($classBalance, $balance)
    {
        $classBalance->update([
            'balance' => $classBalance->balance - $balance,
        ]);
    }

    public function saveAbsences($request)
    {
        $absences = [];
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $dates = $startDate->daysUntil($endDate)->toArray();
        foreach ($dates as $date) {
            $data = [
                'description' => $request->description,
                'Type' => $request->Type,
                'user_id' => Auth::user()->id,
                'date' => $date,
                'rest_id' => $request->rest_id,
            ];
            array_push($absences, Absence::saveItem($data));
        }
        return $absences;
    }

    public function addAbsenceTypeRest($dailyFife, $rest_id, $reques)
    {
        /** @var User */
        $user = Auth::User();
        $restallowance = Restallowance::findOrFail($rest_id);
        if ($restallowance) {
            $absences = $this->saveAbsences($reques);
            if ($dailyFife == true) {
                if ($restallowance->state == 5) {
                    $this->resetAllownesState($restallowance, 0);
                } else {
                    $this->resetAllownesState($restallowance, 5);
                }
                $this->updateBalance($user->restBalance, 0.5);
            } else {
                $this->updateBalance($user->restBalance, 1);
            }
            return $absences;
        }
    }
    public function addAbsenceTypeRegular($dailyFife, $reques)
    {
        /** @var User */
        $user = Auth::User();
        $absences = $this->saveAbsences($reques);
        if ($dailyFife == true) {
            $this->updateBalance($user->regularBalance, 0.5);
        } else {
            $this->updateBalance($user->regularBalance, count($absences));
        }
    }
    // public function FunctionName() : Returntype {
    //     if ($exists->isEmpty() || $dailyFife) {
    //         if (count($exists) <= 1) {
    //             DB::beginTransaction();
    //             try {
    //                 $Type = request()->Type;
    //                 $rest_id = request()->rest_id;
    //                 /** Types of vacations
    //                  *
    //                  * اعتيادية	Regular
    //                  * بدل راحة	Rest allowance
    //                  * عارضة	Casual
    //                  * اجازة عمرة	Umrah leave
    //                  * تجنيد	Recruitment
    //                  * اجازة وضع	Maternity leave
    //                  * اجازة تعويضية	Compensatory leave
    //                  * اخري	Other
    //                  */
    //                 if ($Type == 'Rest allowance') {
    //                     $this->addAbsenceTypeRest($dailyFife, $rest_id);
    //                 } elseif ($Type == 'Regular' ||  $Type == 'Casual') {
    //                     $this->addAbsenceTypeRegular($dailyFife);
    //                 } else {
    //                     $absences = $this->saveAbsences(request());
    //                 }
    //                 DB::commit();
    //                 return $absences;
    //             } catch (\Exception $e) {
    //                 DB::rollback();
    //                 return "حدث خطأ أثناء حذف المستخدم. يرجى المحاولة مرة أخرى.";
    //             }
    //         } else {
    //             return response()->json($exists, 409);
    //         }
    //     } else {
    //         return response()->json($exists, 409);
    //     }
    // }
}
