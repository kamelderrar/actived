<?php

namespace App\Http\Controllers;

use App\Facture;
use App\LigneFacture;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
class FactureController extends Controller
{
    public function index($perPage){
        //Retrieve PDO connection
        $pdo = DB::connection()->getPdo();

        // Récupération des infos nécessaires
        $lines = DB::table('facture')
            ->join('ligne_facture', 'ligne_facture.ide_facture', '=', 'facture.id')
            ->select(
                'facture.numero',
                'facture.lname',
                'facture.fname',
                'facture.date',
                'facture.status',
                'ligne_facture.id',
                'ligne_facture.ide_facture',
                'ligne_facture.unit_price'
            )
            ->paginate($perPage) ;

        return $lines;

    }

    // suppression de ligne de facture
    public function delete($id){
        /** @var \App\LigneFacture*/
        $ligneFacture = LigneFacture::find($id);

        if (empty($ligneFacture)) {
            throw new \Exception('Facture line not found.');
        }

        $ligneFacture->delete();

        // return json values
        return new JsonResponse([
            'deleted'=> [
                'facture' => $id
            ]
        ]);
    }
}
