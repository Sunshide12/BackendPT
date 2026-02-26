<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->has('search') && $request->search !== '') { //if para revisar si la consulta viene con parámetro search
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $clients = $query->orderBy('name')->paginate(10); //Ejecuta la consulta y devuelve 10 resultados por página

        return response()->json([
            'clients' => $clients,
            'message' => 'Clients retrieved successfully',
            'status'  => 200,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string|max:20',
        ]);

        $client = Client::create($request->only('name', 'email', 'phone')); //Se filtra el request y solo deja pasar esos 3 campos

        return response()->json([
            'client'  => $client,
            'message' => 'Client created successfully',
            'status'  => 201,
        ], 201);
    }

    public function all(Request $request)
    {   
        /*Devuelve todos los clientes para que al momento de crear un nuevo pedido y seleccionar
        al cliente, muestre todos los clientes que hay */
        $query = Client::query();

        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $clients = $query->orderBy('name')->get();

        return response()->json([
            'clients' => $clients,
            'message' => 'Clients retrieved successfully',
            'status'  => 200,
        ]);
    }

    public function show(Client $client)
    {
        return response()->json([
            'client'  => $client,
            'message' => 'Client retrieved successfully',
            'status'  => 200,
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
        ]);

        $client->update($request->only('name', 'email', 'phone'));

        return response()->json([
            'client'  => $client,
            'message' => 'Client updated successfully',
            'status'  => 200,
        ]);
    }

    public function destroy(Client $client)
    {
        if ($client->orders()->count() > 0) {
        return response()->json([
            'message' => 'You cannot delete a client with associated orders.',
            'status'  => 422,
        ], 422);
    }

        $client->delete();

        return response()->json([
            'client'  => null,
            'message' => 'Client deleted successfully',
            'status'  => 200,
        ]);
    }
}