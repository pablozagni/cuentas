<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use Illuminate\Http\Request;

class CuentaController extends Controller
{
    public function index()
    {
        return view('cuentas.index');
    }
    
    public function cuentasHijas(Request $request)
    {
        if ($request->get('id')==null) {
            $id = 0 ;
        } else {
            $id = $request->get('id');
        }
        $cuentas = Cuenta::where('parent_id','=', $id )->orderBy('codigo')->get();
        $c = array();
        foreach ($cuentas as $cuenta) {
            $node = [];
            $node['id'] = $cuenta->id ;
            $node['text'] = $cuenta->codigo .' - '.$cuenta->name ;
            if( $cuenta->usadaEnConfig() ) {
                $node['text'] = $node['text'] . ' (*)';
            }
            $node['state'] = $cuenta->tieneHijos() ? 'closed' : 'open' ;
            $c[] = $node ;
        }
        return response()->json( $c );
    }

    public function create( $id ) {
        $CuentasSelector = Cuenta::selector(true);
        $EmpresasSelector = [];
        $cuenta = new Cuenta ;
        $cuenta->parent_id = $id ;
        return view('cuentas.createOrUpdate', compact('cuenta','EmpresasSelector','CuentasSelector'));
    }

    public function edit( $id ) {
        $CuentasSelector = Cuenta::selector(true);
        $EmpresasSelector = [];
        $cuenta = Cuenta::findOrFail( $id );
        return view('cuentas.createOrUpdate', compact('cuenta','EmpresasSelector','CuentasSelector'));
    }

    public function createPost( Request $request, $id ) {
        $rules = array(
            'codigo'            => 'required|min:1|max:20|unique:cuentas',
            'name'              => 'required|min:1|max:60|unique:cuentas',
            );
        $validation = validator()->make($request->all(),$rules);
        if ($validation->fails() ) {
            return redirect()->back()->withInput()->withErrors($validation);
        } else {
            $cuenta = new Cuenta ;
            $cuenta->fill($request->all());
            $cuenta->save();
            return 'Cierre esta ventana';
        }
    }

    public function editPost( Request $request, $id ) {
        $rules = array(
            'codigo'            => 'required|min:1|max:20|unique:cuentas,codigo,'.$id,
            'name'              => 'required|min:1|max:60|unique:cuentas,name,'.$id,
            );
        $validation = validator()->make($request->all(),$rules);
        if ($validation->fails() ) {
            return redirect()->back()->withInput()->withErrors($validation);
        } else {
            $cuenta = Cuenta::findOrFail( $id ) ;
            $cuenta->fill($request->all());
            if ($request->get('detalladabalance')==1) {
                $cuenta->detalladabalance = 1 ;
            } else {
                $cuenta->detalladabalance = 0 ;
            }
                $cuenta->save();
            return 'Cierre esta ventana';
            }
    }

}
