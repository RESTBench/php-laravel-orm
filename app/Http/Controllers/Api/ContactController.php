<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Http\Controllers\Controller;

use App\Domains\Contacts\Contact;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Contact::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ContactRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactRequest $request)
    {
        if (!Contact::create($request->all())) {
            return response()->json(['error' => 'contact_store_error'], 500);
        }
        return response()->json(['success' => 'contact_store_success'], 204);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Contact::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ContactRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContactRequest $request, $id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['error' => 'contact_not_found'], 404);
        }

        $contact->fill($request->all());
        if (!$contact->save()) {
            return response()->json(['error' => 'contact_store_error'], 500);
        }
        return response()->json(['success' => 'contact_store_success'], 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['error' => 'contact_not_found'], 404);
        }

        if (!$contact->destroy($id)) {
            return response()->json(['error' => 'contact_store_error'], 500);
        }
        return response()->json(['success' => 'contact_store_success'], 204);

    }
}
