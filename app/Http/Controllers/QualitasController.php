<?php
namespace App\Http\Controllers;

use App\Http\Classes\Qualitas;
use Illuminate\Http\Request;

class QualitasController extends Controller
{
	public function default()
	{
		$qualitas = $this->getInstance();
		//$qualitas->getSystemId();
		$data = $qualitas->getEdosCuenta();
		$polizas = $data->responseData->edosCuenta;
		//$filename = $qualitas->downloadExcel($data->responseData->edosCuenta[0]);
		//$qualitas->uploadToFtp($filename);
		return view('polizas', get_defined_vars());
	}
	/**
	 * 
	 * @return \App\Http\Classes\Qualitas
	 */
	protected function getInstance($key = '78595', $password = 'PRO4508')
	{
		static $obj;
		if( $obj )
			return $obj;
		$obj = new Qualitas();
		
		$obj->setCrendentials($key, 'MAESTRA', $password);
		
		return $obj;
	}
	protected function downloadFile($key, $password, $clave, $fecha)
	{
		$qualitas = $this->getInstance($key, $password);
		$obj = (object)[
			'clave'	=> $clave,
			'fecha'	=> $fecha
		];
		$filename = $qualitas->downloadExcel($obj);
		return $filename;
	}
	public function download(Request $req)
	{
		list($key, $password, $clave, $fecha) = explode(',', $req->get('data'));
		$filename = $this->downloadFile($key, $password, $clave, $fecha);
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=". basename($filename));  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		readfile($filename);
		die();
	}
	public function upload(Request $req)
	{
		list($key, $password, $clave, $fecha) = explode(',', $req->get('data'));
		$filename = $this->downloadFile($key, $password, $clave, $fecha);
		$qualitas = $this->getInstance();
		$qualitas->uploadToFtp($filename, $key);
		return redirect(route('qualitas'));
	}
}
