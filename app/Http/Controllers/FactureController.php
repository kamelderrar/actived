<?php

namespace App\Http\Controllers;

use App\Facture;
use App\LigneFacture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactureController extends Controller
{
    public function index(Request $request){
        //Retrieve PDO connection
        $pdo = DB::connection()->getPdo();

        $limit = $request->get('limit');
        $page = $request->get('page');
        $filter = $request->get('filter');

        if (empty($filter)) {

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
                ->paginate($limit) ;
        } else {
            // type de filtre en fonction des propriétés
            $operands = [
                'numero' => [
                    'operand' => "=",
                    'value' => "{$filter['value']}"
                ],
                'date' => [
                    'operand' => "LIKE",
                    'value' => "%{$filter['value']}%"
                ],
                'unit_price' => [
                    'operand' => "LIKE",
                    'value' => "%{$filter['value']}%"
                ],
                'status' => [
                    'operand' => "LIKE",
                    'value' => "%{$filter['value']}%"
                ]
            ];

            if ($filter['filter'] === 'date') {
                $time = strtotime($filter['value']);

                $newformat = date('Y-m-d',$time) ;
                var_dump($newformat);
                exit();
                $filter['value'] = date('Y-M-D HH:MM:SS', strtotime($filter['value']));
            }


            // filtres
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
                ->where($filter['filter'], $operands[$filter['filter']]['operand'], $operands[$filter['filter']]['value'])
                ->paginate($limit);
        }


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

    public function getAvailableFactureStatus()
    {
        //Retrieve PDO connection
        $pdo = DB::connection()->getPdo();
        return DB::table('facture')
            ->select('facture.status')
            ->groupBy('facture.status')
            ->get();
    }
}
