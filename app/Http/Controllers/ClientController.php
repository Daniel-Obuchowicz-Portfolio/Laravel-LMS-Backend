<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * List all clients.
     */
    public function index()
    {
        return Client::all();
    }

    /**
     * Show details of a single client.
     */
    public function show(Client $client)
    {
        return $client->load('borrowedBooks');
    }

    /**
     * Add a new client.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        $client = Client::create($validated);

        return response()->json($client, 201);
    }

    /**
     * Delete a client.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(null, 204);
    }
}
