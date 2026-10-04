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
        $applications = $request->user()
            ->applications()
            ->latest()
            ->get();

        return response()->json([
            'data' => $applications,
        ]);
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
