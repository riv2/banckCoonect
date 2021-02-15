<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\{
	EmployeesOrder,
	EmployeesOrderName,
	EmployeesOrderUser,
    EmployeesUsersVote,
    EmployeesPosition
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class EmployeesOrdersController extends Controller
{
    public function index(){
    	return view('admin.pages.employees.orders');
    }

    public function ordersDatatable(Request $request){
        $user = Auth::user();
    	$records = EmployeesOrder::all();

        return Datatables::of($records)
        	->addColumn('name', function ($record){
        		return $record->orderName->name;
        	})
            ->addColumn('status', function($record){
                return EmployeesOrder::$statuses[$record->status];
            })
            ->addColumn('action', function ($record) use ($user){
                $str = '';

                if($record->status == 'new'){
                     $str .= '<a 
                            href="'.route("employees.edit.order", ["id" => $record->id]).'" 
                            class="btn btn-default" 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="Редактировать приказ"
                        >
                            <i class="md md-edit"></i>
                        </a>
                            <a href="'.route('employees.delete.order', ["id" => $record->id]).'" 
                                class="btn btn-default" 
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="Удалить приказ"
                            >
                                <i class="fa fa-trash"></i>
                            </a>';
                } else {

                    if ($record->status == 'review'){
                        $str = '<a 
                            href="'.route("order.for.agreement.show", ["name" => $record->id]).'" 
                            class="btn btn-default" 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="Посмотреть приказ"
                        >
                            <i class="fa fa-eye"></i>
                        </a>';
                    }

                    $userVote = EmployeesUsersVote::where('order_id', $record->id)->where('user_id', $user->id)->first();

                    if($userVote && $userVote->vote == null){
                        $str .= '<button 
                                    class="btn btn-default openModal" 
                                    data-order-id="'.$record->id.'" 
                                    data-toggle="tooltip" 
                                    data-placement="top" 
                                    title="Согласование"
                                >
                                    <i class="fa fa-check"></i>
                                </button>';
                    }
                    if($record->status == 'approved' || $record->status == 'declined'){
                        $str .= '
                                <a 
                                    href="'.route("approved.order.page", ["id" => $record->id]).'" 
                                    class="btn btn-default" 
                                    data-toggle="tooltip" 
                                    data-placement="top" 
                                    title="Итоги согласования"
                                >
                                    <i class="fa fa-list"></i>
                                </a>';
                    }
                }

                return $str;

            })
            ->rawColumns(['action', 'name'])
            ->make(true);
    }

    public function editOrderPage($id = null){
    	$order = null;
    	$ordersNames = EmployeesOrderName::all();
        $votesList = EmployeesPosition::where('managerial', true)->get();

    	if(isset($id)){
    		$order = EmployeesOrder::where('id', $id)->first();
    	}

    	return view('admin.pages.employees.edit_order', compact('order', 'ordersNames', 'id', 'votesList'));
    }

    public function deleteOrderPage($id = null){
        if(isset($id)){
            $order = EmployeesOrder::where('id', $id)->first();
            if(isset($order->file)){
                unlink(storage_path('app/employees/orders/'.$order->file));
            }
            $order->delete();
        }
        return redirect()->route('employees.orders.page');
    }

    public function createOrder(Request $request){
    	$order = EmployeesOrder::create([
    		'employees_order_name_id' => $request->order_name_id,
    		'number'				  => $request->number,
    		'order_date'			  => $request->date,
    		'status' 				  => 'new'
    	]);

    	if (isset($request->file)) {
			$fileName = pathinfo($request->file->getClientOriginalName(), PATHINFO_FILENAME)
						.'_'.Carbon::now()->format('Y_m_d_H_i_s')
						.'.'.$request->file->getClientOriginalExtension();
			$request->file->move(storage_path('app/employees/orders/'), $fileName);

            $order->update([
            	'file' => $fileName
            ]);
		}

        foreach($request->vote_positions_ids as $id){
            $position = EmployeesPosition::find($id);
            foreach ($position->users as $user) {
                EmployeesUsersVote::create([
                    'order_id' => $order->id,
                    'user_id'  => $user->user_id
                ]);
            }
        }

    	return redirect()->route('employees.orders.page');
    }

    public function addEmployeesToOrder(Request $request){
    	foreach ($request->employees as $value) {
    		EmployeesOrderUser::updateOrCreate(
    			[
    				'order_id' => $request->order_id,
    				'employees_id' => $value
    			]
    		);
    	}

    	return response()->json(['status' => 'success']);
    }

    public function addCandidatesToOrder(Request $request){
    	foreach ($request->employees as $value) {
    		EmployeesOrderUser::updateOrCreate(
    			[
    				'order_id' => $request->order_id,
    				'employees_id' => $value
    			]
    		);
    	}

    	return response()->json(['status' => 'success']);
    }

    public function editOrderFile(Request $request){
    	if (isset($request->file)) {
			$fileName = pathinfo($request->file->getClientOriginalName(), PATHINFO_FILENAME)
						.'_'.Carbon::now()->format('Y_m_d_H_i_s')
						.'.'.$request->file->getClientOriginalExtension();
			$request->file->move(storage_path('app/employees/orders/'), $fileName);

			$order = EmployeesOrder::where('id', $request->order_id)->first();
			$oldFileName = $order->file;
			File::delete(storage_path('app/employees/orders/'.$oldFileName));

            $order->update([
            	'file' => $fileName
            ]);
		}

		return redirect()->back();
    }

    public function editEmployeesOrder(Request $request){
		foreach ($request->employees as $value) {
    		EmployeesOrderUser::where('order_id', $request->order_id)->where('employees_id', $value)->delete();
    	}

    	return response()->json(['status' => 'success']);
    }

    public function downloadOrder($name){
    	$file = storage_path('app/employees/orders/').$name;

        return response()->download($file);
    }

    public function orderToAgreement($id){
        EmployeesOrder::where('id', $id)->update(['status' => 'review']);

        return redirect()->route('employees.orders.page');
    }

    public function showOrderForAgreement($id)
    {
        $order = EmployeesOrder::where('status', 'review')->where('id', $id)->first();
        if (empty($order)){
           return redirect()->back();
        }
        $employees = $order->votes;

        return view('admin.pages.employees.agreement_order', compact('order', 'employees'));
    }

    public function orderVote(Request $request){
        $user = Auth::user();

        EmployeesUsersVote::where('order_id', $request->order_id)->where('user_id', $user->id)->update([
            'vote'    => $request->vote == 'approved' ? true : false,
            'comment' => $request->comment
        ]);

        $order = EmployeesOrder::where('id', $request->order_id)->first();
        $votes = $order->votes()->pluck('vote')->toArray();

        if(in_array(false, $votes, true)){
            $order->update([
                'status' => 'declined'
            ]);
        } elseif(!in_array(false, $votes, true) && !in_array(null, $votes, true)){
            foreach ($order->users as $key => $value) {
                $value->update([
                    'status' => $order->orderName->new_status
                ]);
            }
            $order->update([
                'status' => 'approved'
            ]);
        }

        return redirect()->route('employees.orders.page');
    }

    public function approvedOrderPage($id){
        $order = EmployeesOrder::where('id', $id)->first();
        $votes = $order->votes()->pluck('vote')->toArray();
        $countVotes = array_filter($votes, function($value) {
            return ($value !== null); 
        });

        return view('admin.pages.employees.orders_approved_page', compact('order', 'votes', 'countVotes'));
    }

    public function positionsByDate(Request $request){
        $positions = EmployeesPosition::where('created_at', '<=', $request->order_date)
                                ->where('managerial', true)
                                ->get();

        return response()->json(['status' => 'success', 'positions' => $positions]);
    }
}
