<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SemesterController extends Controller
{
    /**
     * Display a listing of semesters
     */
    public function index()
    {
        $semesters = Semester::orderBy('academic_year', 'desc')
            ->orderBy('start_date', 'desc')
            ->paginate(15);
            
        $currentSemester = Semester::current();
        
        return view('semesters.index', compact('semesters', 'currentSemester'));
    }

    /**
     * Show the form for creating a new semester
     */
    public function create()
    {
        return view('semesters.create');
    }

    /**
     * Store a newly created semester
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        // Check for overlapping semesters
        $overlapping = Semester::where('is_active', true)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['date_overlap' => 'The semester dates overlap with an existing active semester.']);
        }

        $semester = Semester::create([
            'name' => $request->name,
            'academic_year' => $request->academic_year,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'is_current' => false // New semesters are not current by default
        ]);

        return redirect()->route('semesters.index')
            ->with('success', 'Semester created successfully.');
    }

    /**
     * Display the specified semester
     */
    public function show(Semester $semester)
    {
        $semester->load(['registeredAssets', 'assetChanges']);
        
        // Get semester statistics
        $stats = $semester->getStatistics();
        
        return view('semesters.show', compact('semester', 'stats'));
    }

    /**
     * Show the form for editing the specified semester
     */
    public function edit(Semester $semester)
    {
        return view('semesters.edit', compact('semester'));
    }

    /**
     * Update the specified semester
     */
    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        // Check for overlapping semesters (excluding current semester)
        $overlapping = Semester::where('is_active', true)
            ->where('id', '!=', $semester->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['date_overlap' => 'The semester dates overlap with an existing active semester.']);
        }

        $semester->update([
            'name' => $request->name,
            'academic_year' => $request->academic_year,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('semesters.index')
            ->with('success', 'Semester updated successfully.');
    }

    /**
     * Remove the specified semester
     */
    public function destroy(Semester $semester)
    {
        // Check if semester has associated assets
        if ($semester->registeredAssets()->exists() || $semester->assetChanges()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete semester that has associated assets or changes.');
        }

        // Don't allow deletion of current semester
        if ($semester->is_current) {
            return redirect()->back()
                ->with('error', 'Cannot delete the current semester. Please set another semester as current first.');
        }

        $semester->delete();

        return redirect()->route('semesters.index')
            ->with('success', 'Semester deleted successfully.');
    }

    /**
     * Show form to set current semester
     */
    public function setCurrentForm()
    {
        $semesters = Semester::active()
            ->orderBy('academic_year', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();
            
        $currentSemester = Semester::current();
        
        return view('semesters.set-current', compact('semesters', 'currentSemester'));
    }

    /**
     * Set a semester as current
     */
    public function setCurrent(Request $request, Semester $semester = null)
    {
        if ($request->has('semester_id')) {
            $semester = Semester::findOrFail($request->semester_id);
        }

        if (!$semester) {
            return redirect()->back()
                ->with('error', 'Please select a semester.');
        }

        if (!$semester->is_active) {
            return redirect()->back()
                ->with('error', 'Cannot set an inactive semester as current.');
        }

        try {
            DB::transaction(function () use ($semester) {
                // Unset all current semesters
                Semester::where('is_current', true)->update(['is_current' => false]);
                
                // Set the selected semester as current
                $semester->update(['is_current' => true]);
            });

            return redirect()->route('semesters.index')
                ->with('success', "Successfully set '{$semester->full_name}' as the current semester.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to set current semester: ' . $e->getMessage());
        }
    }

    /**
     * Auto-detect and set current semester based on today's date
     */
    public function autoSetCurrent()
    {
        $detectedSemester = Semester::autoSetCurrent();
        
        if ($detectedSemester) {
            return redirect()->route('semesters.index')
                ->with('success', "Automatically set '{$detectedSemester->full_name}' as the current semester based on today's date.");
        } else {
            return redirect()->route('semesters.index')
                ->with('warning', 'No active semester found for today\'s date. Please manually set a current semester.');
        }
    }

    /**
     * Create default semesters for an academic year
     */
    public function createDefaults(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'start_year' => 'required|integer|min:2020|max:2030'
        ]);

        try {
            $semesters = Semester::createDefaultSemesters(
                $request->academic_year,
                $request->start_year
            );

            return redirect()->route('semesters.index')
                ->with('success', "Created {count($semesters)} default semesters for {$request->academic_year}.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create default semesters: ' . $e->getMessage());
        }
    }
}
