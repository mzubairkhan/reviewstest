<?php namespace App\Http\Controllers;

use App\Registration;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		redirect(view('registration.create'));
		$regs = Registration::all();
		return view('registration.index', compact('regs'));

	}


	public function create()
		{
			return view('registration.create');
		}


}