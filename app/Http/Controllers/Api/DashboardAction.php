<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IndexCourtCaseResources;
use App\Http\Resources\VisitorResources;
use App\Models\CaseExtraFee;
use App\Models\CaseFee;
use App\Models\Client;
use App\Models\CourtCase;
use App\Models\Expense;
use App\Models\Hearing;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardAction extends Controller
{
    public function dashboard(Request $request){
        try {
            $visitor_total = Visitor::count();
            $visitor = Visitor::whereDate('created_at', today())->count();

            $client_total = Client::count();
            $client = Client::whereDate('created_at', today())->count();

            $cases_total = CourtCase::count();
            $cases_today = CourtCase::whereDate('created_at', today())->count();

            $hearing = Hearing::whereDate('date_time', now()->toDateString())->count();
            $hearingTomorrow = Hearing::whereDate('date_time', Carbon::tomorrow())->count();

            $fee_received_todays = CaseFee::whereDate('created_at', today())->sum('amount');
            $fee_received_months = CaseFee::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount');

            $extra_fee_received_todays = CaseExtraFee::whereDate('created_at', today())->sum('amount');
            $extra_fee_received_months = CaseExtraFee::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount');

            $expense_todays = Expense::whereDate('created_at', today())->sum('amount');
            $expense_months = Expense::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount');

            $employee_todays = User::whereDate('created_at', today())->count();
            $employee_this_month = User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();


            $search = $request->query('search');
            $visitorQuery = Visitor::orderByDesc('id')->whereDate('created_at', today());

            if ($search) {
                $visitorQuery->where(function ($q) use ($search) {
                    $q->where("name", "like", "%{$search}%")
                        ->orWhere("phone", "like", "%{$search}%")
                        ->orWhere("visitorId", "like", "%{$search}%");
                });
            }

            $visitors = $visitorQuery->paginate(50)->appends($request->query());


            $caseQuery = CourtCase::orderByDesc('id')->whereDate('created_at', today());

            if ($search) {
                $caseQuery->where(function ($q) use ($search) {
                    $q->where("caseId", "like", "%{$search}%")
                        ->orWhere("priority", "like", "%{$search}%")
                        ->orWhere("clientId", "like", "%{$search}%")
                        ->orWhereHas('clientAdd', function ($query) use ($search) {
                            $query->where("name", "like", "%{$search}%")
                                ->orWhere("phone", "like", "%{$search}%")
                                ->orWhere("email", "like", "%{$search}%");
                        });
                });
            }

            $cases = $caseQuery->paginate(50)->appends($request->query());

            $month = $request->query('month', now()->month);
            $year = $request->query('year', now()->year);

            $start_date = Carbon::create($year, $month, 1);
            $end_date = $start_date->copy()->endOfMonth();

            $dates = [];
            $dates1 = [];

            while ($start_date <= $end_date) {
                $dates[$start_date->toDateString()] = Visitor::whereDate('created_at', $start_date)->count();
                $start_date->addDay();
            }

            $start_date = Carbon::create($year, $month, 1);

            while ($start_date <= $end_date) {
                $dates1[$start_date->toDateString()] = CourtCase::whereDate('created_at', $start_date)->count();
                $start_date->addDay();
            }

            $graph_data_for_total_visitor = $dates;
            $graph_data_for_total_cases = $dates1;

            return response()->json([
                'visitor_total' => $visitor_total,
                'visitor_todays' => $visitor,
                'client_total' => $client_total,
                'client_todays' => $client,
                'case_total' => $cases_total,
                'case_todays' => $cases_today,
                'hearing_todays' => $hearing,
                'hearing_tomorrow' => $hearingTomorrow,
                'fee_received_todays' => $fee_received_todays,
                'fee_received_this_months' => $fee_received_months,
                'extra_fee_todays' => $extra_fee_received_todays,
                'extra_fee_this_months' => $extra_fee_received_months,
                'expense_todays' => $expense_todays,
                'expense_this_months' => $expense_months,
                'employee_todays' => $employee_todays,
                'employee_this_month' => $employee_this_month,
                'graph_data_for_total_visitor_in_month' => $graph_data_for_total_visitor,
                'graph_data_for_total_cases' => $graph_data_for_total_cases,
                'todays_visitors' => VisitorResources::collection($visitors),
                'todays_case' => IndexCourtCaseResources::collection($cases),
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something Went Wrong',
                'status' => 500
            ]);
        }
    }



}
