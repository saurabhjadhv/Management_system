<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vendor;
use App\Rules\ProfilePic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function getRegisterForm() {
        return view('register');
    }


    public function Register(Request $request) {
        $validate = $request->validate([
            'full_name' => 'required|regex:/^[a-zA-Z\s]+$/u|min:3|max:80', 
            'email' => 'required|regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i|unique:users,email', 
            'username' => 'required|string|regex:/^\w*$/|max:255|unique:users,username',
            'password' => [
                'required', 
                'string', 
                'min:6', 
                'regex:/[a-z]/', 
                'regex:/[A-Z]/', 
                'regex:/[0-9]/', 
                'regex:/[@$!%*#?&]/' 
            ],
            'address' => 'required|string|max:255',
            'contact' => 'required|digits_between:9,15',
        ]);
        

        User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password), 
            'address' => $request->address,
            'contact' => $request->contact
        ]);

        return redirect()->route('login')->with('message', 'Registered successfully');
    }


    public function getLoginForm() {
        return view('login');
    }

    public function Login(Request $request) {

        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
    

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('message', 'successfully redirected'); 
        }
    
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }


    public function getdashboard(Request $request) {
        return view('dashboard');
    }

    public function getVendors()
{
    return response()->json(Vendor::all());
}


    public function logout() {
        auth()->logout();
        return redirect()->route('login')->with('message', 'Logged out successfully.');
    }


    public function InsertVendor(Request $request) {
       
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
            'required',
            'email',
            'max:255',
            'unique:vendors,email',
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z]+\.[a-zA-Z]{2,}$/i',
        ],
            'designation' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact' => 'required|string|max:15',
            'profile_pic' => ['required', 'file', new ProfilePic()], 
        ]);
    
        $vendor = new Vendor();
        $vendor->user_id = auth()->id();
        $vendor->name = $validatedData['name'];
        $vendor->email = $validatedData['email'];
        $vendor->designation = $validatedData['designation'];
        $vendor->address = $validatedData['address'];
        $vendor->contact = $validatedData['contact'];
        

    
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profile_pics', $fileName, 'public'); 
            $vendor->profile_pic = 'profile_pics/' . $fileName; // Assign the path to the profile_pic
        } else {
            // Handle case when no file is uploaded (if profile_pic is required)
            return back()->withErrors(['profile_pic' => 'Profile picture is required.']);
        }
      
    
        $vendor->save();
    
        return redirect()->route('dashboard')->with('message', 'Vendor created successfully.');
    }
    

    // Controller method for fetching vendor data
public function edit($id) {
    $vendor = Vendor::findOrFail($id); 
    return response()->json($vendor);  // Send JSON response for AJAX
}




public function update(Request $request, $id)
{
    $validator = $request->validate([
        'name' => 'required|string|max:255',
        'email' => [
        'required',
        'email',
        'max:255',
        'unique:vendors,email',
        'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z]+\.[a-zA-Z]{2,}$/i',
    ],
        'designation' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'contact' => 'required|string|max:15',
        'profile_pic' => ['required', 'file', new ProfilePic()], 
    ]);

    if ($validator->fails()) {
        // Return JSON errors
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $vendor = Vendor::findOrFail($id);
    $vendor->name = $request->input('name');
    $vendor->email = $request->input('email');
    $vendor->designation = $request->input('designation');
    $vendor->address = $request->input('address');
    $vendor->contact = $request->input('contact');

    if ($request->hasFile('profile_pic')) {
        if ($vendor->profile_pic) {
            Storage::disk('public')->delete($vendor->profile_pic);
        }

        $path = $request->file('profile_pic')->store('profile_pics', 'public');
        $vendor->profile_pic = $path;
    }

    $vendor->save();

    return response()->json(['success' => 'Vendor updated successfully!']);
}



public function delete($id) {
    $vendor = Vendor::find($id);

    if (!$vendor) {
        return response()->json(['error' => 'Vendor not found.'], 404); // Handle not found
    }

    $vendor->delete();
    return response()->json(['success' => 'Vendor deleted successfully!']);
}

    

    
    
}
