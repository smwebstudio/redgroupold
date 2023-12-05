<?php

namespace App\Http\Controllers;

use App\RequiredProject;
use Illuminate\Http\Request;
use App\Category;
use App\Page;
use Illuminate\Support\Facades\Validator;
use App\Nikita;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Location;
use App\Product;

class RequiredProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requests = RequiredProject::with(['categories','state', 'community' ,'users'])->orderBy('id', 'DESC')->paginate(20);
        $categories = Category::all();
        $pages = Page::get();
        return view('admin.pages.property-requests')->with(compact('requests','categories','pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [];
        parse_str($request->formData, $data);
        $validator = Validator::make($data, [
            'name' => 'required',
            'phone' => 'required|digits_between:7,15',
            'email' => 'required|email',
            'states' => 'required|exists:locations,id',
            'by_sms' => 'required_unless:by_email,1',
            'by_email' => 'required_unless:by_sms,1',
            'price_min' => 'integer|min:25000',
            'price_max' => 'integer:|min:25500'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            $user = User::create(['name' => $data['name'], 'email' => $data['email']]);
        }
        // $user_id = RequiredProperty::where('phone', $data['phone'])->first();
        // if (!$user_id) {
        //     $user = User::create(['name' => $data['name'], 'email' => $data['email']]);
        // } else {
        //     $user = User::find($user_id->id);
        // }

        $data['user_id'] = $user->id;
        // dd($data);
        // $data['states'] = serialize($data['states']);
        // $data['communities'] = serialize($data['communities']);
        // $data['rooms'] = serialize($data['rooms']);
        $data['price_min'] = intval($data['price_min']);
        $data['price_max'] = intval($data['price_max']);
        $data['last_sent_id'] = Product::orderBy('id')->first()->id;
        $required_property = RequiredProject::create($data);
        
        // $last_product = new ProjectController;
        // $last_product = $last_product->getPotentialLayouts($required_property)->orderBy('id', 'desc')->first();
        // if($last_product) {
        //     $required_property->last_sent_id = $last_product->id;
        //     $required_property->save();
        // }

        // dd($required_property);
        $recipient = $required_property->phone;
        $message = '"'.'Ողջույն '.$user->name.',\n Ձեր հարցման արդյունքում գտնվել են հետևյալ գույքերը կառուցապատողից.\n' . url('/') . '/properties/'.$required_property->id.'"';
        if ($required_property->by_sms) {
            $nikita = new Nikita;
            try {
                $nikita->send($recipient,$message);
            } catch (Exception $e) {
                return response()->json($e->getMessage());
            }
        }

        if ($required_property->by_email) {
            try {
                Mail::raw($message, function($message) use($user) {
                    $message->to($user->email)->subject('RED INVEST GROUP');
                });
            } catch (Exception $e) {
                return response()->json($e->getMessage());
            }

        }

        return $required_property;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RequiredProject  $requiredProject
     * @return \Illuminate\Http\Response
     */
    public function show(RequiredProject $requiredProject)
    {
        //
    }

    public function showForm() {
        $locations = Location::with('childs', 'parent')->get();
        return view('pages.request')->with(compact('locations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RequiredProject  $requiredProject
     * @return \Illuminate\Http\Response
     */
    public function edit(RequiredProject $requiredProject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RequiredProject  $requiredProject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequiredProject $requiredProject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RequiredProject  $requiredProject
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequiredProject $requiredProject)
    {
        //
    }

    public function unsubscribe(Request $request)
    {
        $required_property_id = $request->get('potId');
        $required_property = RequiredProject::find($required_property_id);
        $required_property->update(['by_sms' => false, 'by_email' => false]);
        // return route('properties', [$required_property_id])->with(['required_property_id' => $required_property_id]);
        return response()->json( __('projects.unsubscribe_done') );
    }
}
