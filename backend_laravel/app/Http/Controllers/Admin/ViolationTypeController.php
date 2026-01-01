<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateViolationType;
use App\Http\Services\Admin\ViolationTypeService;
use Illuminate\Http\Request;

class ViolationTypeController extends Controller
{
    protected $violationType ;
    public function __construct( ViolationTypeService $violationType)
    {
        $this->violationType = $violationType;
    }

    public function index(){
         $violationTypes = $this->violationType->getViolationTypeList();
        return view("admin.violationType.index", compact('violationTypes'));
    }

    public function create(){
        return view('admin.violationType.create');
    }

    public function store(CreateViolationType $request ){
        $attr = $request->validated();
        $this->violationType->createViolationType($attr);
        return redirect()->route('admin.violationTypes.index')->with('success', 'violation type created successfully.');
    }


}
