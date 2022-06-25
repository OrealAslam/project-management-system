<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    protected $paginate_limit;

    public function __construct(Request $request)
    {
        $this->paginate_limit = $request->input('paginate_limit', config('app.paginate_limit'));
    }


    public function clientDashboard(Request $request)
    {
        if (!session('userEmail')) {
            return redirect()->route('userLogin');
        }

        $user = User::where('email', session('userEmail'))->get(['id', 'user_role']);
        $projects = Project::paginate($this->paginate_limit);
        return view('users.client.clientDashboard', ['projects' => $projects, 'role' => $user[0]->user_role]);
    }

    public function exploreProject($id)
    {
        // get project PAID/UNPAID /TOTAL HOURS and pass to View
        $tasks = Project::find($id)->task;

        $obj = new ProjectController();
        $totalHours = $obj->totalHours($tasks);
        $totalHours = $obj->totalTimeSpend($totalHours);

        $paidHours  = $obj->paidHours($tasks);
        $unPaidHours  = $obj->unPaidHours($tasks);

        return view('users.client.viewProjectTask', ['tasks' => $tasks, 'paidHours' => $paidHours, 'unPaidHours' => $unPaidHours, 'totalHours' => $totalHours]);
    }


    // check invoice
    public function checkInvoice($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        return view('users.client.invoice', ['invoice' => $invoice]);
    }


    // change password form
    public function ChangePasswordForm()
    {
        return view('users.client.changePassword');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old-pass' => 'required',
            'new-pass' => 'required|min:8',
            'confirm-pass' => 'required|min:8',
        ]);

        // match new and confirm new password
        if ($request['new-pass'] === $request['confirm-pass']) {
            $data = User::where('email', session('userEmail'))->get();

            if (Hash::check($request['old-pass'], $data[0]->password)) {
                $update = User::where('email', session('userEmail'))->update([
                    'password' => Hash::make($request['new-pass'])
                ]);

                if ($update) {
                    $request->session()->flash('success', 'Password has been updated!!');
                    return redirect()->route('client.change.password');
                }
            } else {
                $request->session()->flash('success', 'Incorrect Old Password');
                return redirect()->route('client.change.password');
            }
        } else {
            $request->session()->flash('success', 'New & Confirm new password must be same');
            return redirect()->route('client.change.password');
        }
    }

    public function clientLogout(Request $request)
    {
        // Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('userLogin'));
    }
}
