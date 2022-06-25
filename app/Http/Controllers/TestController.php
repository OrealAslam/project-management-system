<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\Invoice;
use Carbon\Carbon;

use DateTime;



class TestController extends Controller
{

    public function index(Request $request)
    {
        $currentDateTime = new Carbon('07:10:00');
        $newDateTime = $currentDateTime->addHours(2);
        // echo "<pre />";
        // echo ($currentDateTime);
        // echo ($newDateTime);
        return $newDateTime->format('%H%I');
    }

    public function setTime(Request $request)
    {
        return view('setTime');
    }

    public function getTime(Request $request)
    {
        dd($request);
    }

    public function difference()
    {
        $checkTime = strtotime('09:00:59');
        echo 'Check Time : ' . date('H:i:s', $checkTime);
        echo '<hr>';

        $loginTime = strtotime('09:01:00');
        $diff = $checkTime - $loginTime;

        echo 'Login Time : ' . date('H:i:s', $loginTime) . '<br>';
        echo ($diff < 0) ? 'Late!' : 'Right time!';
        echo '<br>';
        echo 'Time diff in sec: ' . abs($diff);
    }

    public function check()
    {
        // $task = Project::find(10)->task;
        // foreach ($task as $value) {
        //     dd(TaskLog::where('task_id', $value->id)->where('log_status', 'pending')->count());
        // }

        $times = ['17:46', '03:15', '21:56'];
        $timeshift = 3;

        $new_times = array_map(
            fn ($t) => Carbon::createFromFormat('H:i',  $t)
                ->subHours($timeshift)
                ->format('H:i'),
            $times
        );

        dd($new_times);
    }

    public function totalPaidLogs($task)
    {
        $arr = [];
        foreach ($task as $task) {
            $paid = TaskLog::where('task_id', $task->id)->where('log_status', 'complete')->get();
            foreach ($paid as $log) {
                $start  = new Carbon($log->start_time);
                $end    = new Carbon($log->end_time);
                $difference = $this->timeDifference($start, $end);
                array_push($arr, $difference);
            }
        }
        dd($this->totalTimeSpend($arr));
    }


    public function timeDifference($start, $end)
    {
        $start  = new Carbon($start);
        $end    = new Carbon($end);
        return $start->diff($end)->format('%H:%I');
    }

    public function totalTimeSpend($time)
    {
        $sum = strtotime('00:00:00');

        $totaltime = 0;

        foreach ($time as $element) {

            // Converting the time into seconds
            $timeinsec = strtotime($element) - $sum;

            // Sum the time with previous value
            $totaltime = $totaltime + $timeinsec;
        }


        $h = intval($totaltime / 3600);

        $totaltime = $totaltime - ($h * 3600);

        // Minutes is obtained by dividing
        // remaining total time with 60
        $m = intval($totaltime / 60);

        // Remaining value is seconds
        $s = $totaltime - ($m * 60);

        // Printing the result
        // return ("$h:$m:$s");
        return ("$h:$m");
    }

    public function fixedHourInvoice(Request $request)
    {

        // check weather the given project has enough hours spend or not
        $request->validate([
            'hours' => 'required|numeric',
            'projectId' => 'required|numeric',
        ]);
        $projectID = $request->projectId;
        $project = Project::find($projectID);
        $tasks = $project->task;
        $taskHours = [];
        $obj = new ProjectController();

        foreach ($tasks as $task) {
            $taskLog = $task->tasklog;
            $hours = $obj->calcHours($taskLog);
            array_push($taskHours, $hours);
        }
        $projectHours = $obj->totalTimeSpend($taskHours);
        if ($projectHours < $request->hours) {
            $request->session()->flash('success', 'Project has no enough hours');
            return redirect()->route('create.invoice.view', ['id' => $projectID]);
        }
        // $hourInvoice = str_replace(':', '.', $obj->totalTimeSpend($taskHours)) * $request->hours;
        $hourInvoice = $project->per_hour_rate * $request->hours;
        // fixed hour invoice entry
        $this->fixedHourInvoiceEntry($project->project_name, $request->hours, $hourInvoice, $projectHours);
        return view('admin.invoice.fixedHourInvoice', ['projectId' => $projectID, 'task' => $tasks, 'hourInvoice' => $hourInvoice])->with('fixedAmountInvoice', 'set');
    }

    public function fixedHourInvoiceEntry($projectName, $fixedHours, $invoiceRate, $projectHours)
    {
        // add new invoice data in invoice table 
        // $invoice = new Invoice();
        // $invoice->project_name = $projectName;
        // $invoice->date_created = Carbon::now();
        // $invoice->total_hours  = $projectHours;
        // $invoice->fixedHours   = $fixedHours;
        // $invoice->start_date   = Null;
        // $invoice->end_date     = Null;
        // $invoice->invoice_rate = $invoiceRate;

        // $invoice->save();
    }

    // -----------------------------------------------------------------------------------------------------------

    public function call()
    {
        $a = 4;
        $b = 2;
        return $a / $b;
    }

    function timeSubtractionFirstTime($actual_time, $time_to_reduce)
    {
        $actual_time_array = explode(":", $actual_time);
        $time_to_reduce = explode(":", $time_to_reduce);
        $final_result = [];
        if ($actual_time_array[1] < $time_to_reduce[1]) {
            $actual_time_array[0] = $actual_time_array[0] - 1;
            $final_result[] = $actual_time_array[1] + 60 - $time_to_reduce[1];
        } else {
            $final_result[] = $actual_time_array[1] - $time_to_reduce[1];
        }
        $final_result[] = $actual_time_array[0] - $time_to_reduce[0];

        return implode(":", array_reverse($final_result));
    }

    public function test()
    {
        $timeToReduceLeft = "17:45";
        $val = ["37:10", "4:16", "2:05"];

        foreach ($val as &$value) {
            $diff = $this->timeSubtractionFirstTime($value, $timeToReduceLeft);
            if (strpos($diff, chr(45)) !== false) { //if $value < $timeToReduceLeft
                $timeToReduceLeft = $this->timeSubtractionFirstTime($timeToReduceLeft, $value);
                $value = "00:00";
            } else { //if $value >= $timeToReduceLeft
                $value = $this->timeSubtractionFirstTime($value, $timeToReduceLeft);
                $timeToReduceLeft = "00:00";
            }


            if ($timeToReduceLeft == "00:00") {
                break;
            }
        }
        echo implode(",", $val);
    }


    public function paidHours($projectTasks)
    {
        // loop through each TASK -> TASK-LOG
        // if log_status == 'complete', calculate its time difference
        // push time difference in an array (sum the array and get PAID-HOURS)
        $paidHours = [];
        foreach ($projectTasks as $task) {
            foreach ($task->tasklog as $log) {
                if ($log->log_status == 'complete') {
                    $start  = new Carbon($log->start_time);
                    $end    = new Carbon($log->end_time);
                    $difference = $this->timeDifference($start, $end);
                    array_push($paidHours, $difference);
                }
            }
        }
        return $this->totalTimeSpend($paidHours);
    }
}
