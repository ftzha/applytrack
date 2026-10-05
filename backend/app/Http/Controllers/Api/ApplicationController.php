<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Application;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start with applications belonging only to the authenticated user.
        $query = Application::query()
            ->where('user_id', $request->user()->id);

        // Search by company name OR position.
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Filter by application status.
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Apply the requested sorting option
        // If none was supplied, use newest
        $sort = $request->input('sort', 'newest');

        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;

            case 'recently_applied':
                $query->orderByDesc('applied_at');
                break;

            case 'company_az':
                $query->orderBy('company_name');
                break;

            default:
                $query->latest();
                break;
        }

        $applications = $query->paginate(5);

        return response()->json($applications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApplicationRequest $request)
    {
        //Associates the new application with the authenticated user
        $application = $request->user()
            ->applications()
            ->create($request->validated());

        return response()->json([
            'data' => $application,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        $this->authorize('view', $application);

        return response()->json([
            'data' => $application,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateApplicationRequest $request,
        Application $application
    ) {
        $this->authorize('update', $application);

        $application->update($request->validated());

        return response()->json([
            'data' => $application,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        $this->authorize('delete', $application);

        $application->delete();

        return response()->noContent();
    }
}
