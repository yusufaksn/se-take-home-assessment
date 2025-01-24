<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\Order\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): array
    {
        return $this->orderService->index();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validate([
            'customerId' => 'required|integer|exists:customers,id',
            'productItems' => 'required|array|min:1',
            'productItems.*.productId' => 'required|integer|exists:products,id',
            'productItems.*.quantity' => 'required|integer|min:1',
        ]);
        $result = $this->orderService->store($validatedData);
        return response()->json([
            'message' => $result['message'],
            'data' => $result['data']
        ], $result['status']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): void
    {
        $this->orderService->deleteOrder($id);
    }
}
