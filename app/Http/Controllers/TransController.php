<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Trans;
use JWTAuth;

class TransController extends Controller
{
    public function index(){
    	$data = Trans::all();
    	return response($data);
    }
    public function show($username){
    	$data=Trans::find($username);
    	return response($data);
    }
    public function create(Request $request){
    	$trans = new trans();
    	$user = JWTAuth::parseToken()->authenticate();

    	$trans->username = $user->username;
    	$trans->jenis_transaksi = $user->jenis_transaksi;
    	$trans->nama_transaksi = $request->nama_transaksi;
    	$trans->jenis_transaksi= $request->jenis_transaksi;
    	$trans->jml_transaksi = $request->jml_transaksi;
    	$saldo_awal=$user->jml_saldo;

    	if($request->jenis_transaksi == "kredit"){
    		$saldo_akhir=$user->jml_saldo - $request->jml_transaksi;
    	}elseif($request->jenis_transaksi == "debit"){
			$saldo_akhir=$user->jml_saldo + $request->jml_transaksi;    		
    	}else{
    		return "jenis_transaksi";
    	}
    	$trans->save();

    	$user->jml_saldo=$saldo_akhir;
    	$user->save();

    	return "Username 	: ".$user->username."
				Jenis Transaksi 	: ".$request->jenis_transaksi."
				Nama Transaksi 	: ".$request->nama_transaksi."
				Saldo Awal 	:Rp. ".$saldo_awal."
				Jumlah Pembayaran 	: Rp.".$request->jml_transaksi."
				Saldo Akhir 	: Rp.".$saldo_akhir;
    }
}
