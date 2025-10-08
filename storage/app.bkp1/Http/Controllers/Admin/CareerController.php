<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Career;
use App\Model\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CareerController extends Controller
{

    public function add()
    {
        return view('admin-views.careers.add-new');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'department' => 'required|string',
            'employment_type' => 'required|string',
            'experience' => 'required|string',
            'salary' => 'required|string',
            'location' => 'required|string',
            'openings' => 'required|integer',
            'education' => 'required|string',
            'skills' => 'required|string',
            'job_description' => 'required|string',
        ]);
    
        $career = new Career();
        $career->title = $request->title;
        $career->department = $request->department;
        $career->employment_type = $request->employment_type;
        $career->experience = $request->experience;
        $career->salary = $request->salary;
        $career->location = $request->location;
        $career->openings = $request->openings;
    
        $career->education = json_encode(array_map('trim', explode(',', $request->education)));
        $career->skills = json_encode(array_map('trim', explode(',', $request->skills)));
        $career->job_description = json_encode(array_filter(array_map('trim', explode("\n", $request->job_description))));
    
        $career->save();
    
        return redirect()->route('admin.employee.career.list')->with('success', 'Job posted successfully');
    }
    
    public function index(Request $request)
    {
        $search = $request->search;
        $careers = Career::when($search, function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('admin-views.careers.list', compact('careers'));
    }

    public function edit($id)
    {
        $career = Career::find($id);
        return view('admin-views.careers.edit', compact('career'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'department' => 'required|string',
            'employment_type' => 'required|string',
            'experience' => 'required|string',
            'salary' => 'required|string',
            'location' => 'required|string',
            'openings' => 'required|integer',
            'education' => 'required|string',
            'skills' => 'required|string',
            'job_description' => 'required|string',
        ]);
    
        $career = Career::findOrFail($id);
    
        $career->title = $request->title;
        $career->department = $request->department;
        $career->employment_type = $request->employment_type;
        $career->experience = $request->experience;
        $career->salary = $request->salary;
        $career->location = $request->location;
        $career->openings = $request->openings;
    
        // Save as JSON
        $career->education = json_encode(array_map('trim', explode(',', $request->education)));
        $career->skills = json_encode(array_map('trim', explode(',', $request->skills)));
        $career->job_description = json_encode(array_filter(array_map('trim', explode("\n", $request->job_description))));
    
        $career->save();
    
        return redirect()->route('admin.employee.career.edit', $career->id)->with('success', 'Job updated successfully!');
    }
    
    

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $career = Career::find($request->id);
        $career->delete();

        return redirect()->back()->with('success', 'Job deleted successfully!');
    }

    public function showapplicant()
    {
        $applicants = Applicant::with('career')->latest()->paginate(10);
        return view('admin-views.careers.applicants', compact('applicants'));
    }

    public function applicant_delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $applicant = Applicant::find($request->id);
        $applicant->delete();

        return redirect()->back()->with('success', 'Applicant deleted successfully!');
    }
}
