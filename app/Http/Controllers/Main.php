<?php
/**
 * Created by PhpStorm.
 * User: Milos Djacic
 * Date: 11/19/2017
 * Time: 1:55 PM
 */

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class Main extends Controller
{
    public function index()
    {
        $groups = DB::table('groups')->get();
        $postcodeByGroup = DB::select(DB::raw(
            "SELECT p.postcode,p.group_id, p.id FROM groups as g JOIN postcodes as p ON g.id = p.group_id ORDER BY p.postcode ASC")
        );
        $postcodeGroup1 = DB::select(DB::raw(
            "SELECT p.postcode,p.group_id, p.id FROM groups as g JOIN postcodes as p ON g.id = p.group_id WHERE group_id=1")
        );
        $postcodeGroup2 = DB::select(DB::raw(
            "SELECT p.postcode,p.group_id, p.id FROM groups as g JOIN postcodes as p ON g.id = p.group_id WHERE group_id=2")
        );
        $postcodeGroup3 = DB::select(DB::raw(
            "SELECT p.postcode,p.group_id, p.id FROM groups as g JOIN postcodes as p ON g.id = p.group_id WHERE group_id=3")
        );



        return view('main', ['groups' => $groups,
                                   'postcodeByGroup'=>$postcodeByGroup,
                                   'postcodeGroup1'=>$postcodeGroup1,
                                   'postcodeGroup2'=>$postcodeGroup2,
                                   'postcodeGroup3'=>$postcodeGroup3,
                                   ]);
    }
    public function distance($lat1, $lon1, $lat2, $lon2) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;


        return ($miles * 1.609344);

    }
    public function loadData($id){
        $postcodeAddresses = DB::select(DB::raw(
            "SELECT DISTINCT(a.street), a.site_number, a.site_description FROM postcodes p RIGHT JOIN addresses a ON p.id=a.postcode_id WHERE a.postcode_id=$id")
        );

        $singlePostcode = DB::select(DB::raw(
            "SELECT latitude,longitude FROM postcodes WHERE id=$id"
        ));
        $postcodeSchools = DB::select(DB::raw(
            "SELECT DISTINCT (s.name), s.postcode_id, p.latitude as lat, p.longitude as lon FROM postcodes p RIGHT JOIN schools s ON p.id=s.postcode_id")
        );
        $postcodeBusStops = DB::select(DB::raw(
           "SELECT DISTINCT (b.name), b.postcode_id, p.latitude as lat, p.longitude as lon FROM postcodes p RIGHT JOIN busstops b ON p.id=b.postcode_id")
        );
        $inside = array();
        for($i=0;$i<count($postcodeSchools);$i++){
            $compare = $this->distance($singlePostcode[0]->latitude,$singlePostcode[0]->longitude,$postcodeSchools[$i]->lat,$postcodeSchools[$i]->lon);
            if($compare<5.000){
                $inside[]= array(
                    'name' => $postcodeSchools[$i]->name,
                    'distance' => $compare
                );

            }
        }
        $closest = array();
        for($i=0;$i<count($postcodeBusStops);$i++){
            $compare = $this->distance($singlePostcode[0]->latitude,$singlePostcode[0]->longitude,$postcodeBusStops[$i]->lat,$postcodeBusStops[$i]->lon);
            $closest[]= array(
                    'name' => $postcodeBusStops[$i]->name,
                    'distance' => $compare
                );


        }

        $list = array_sort($closest, 'distance', SORT_DESC);
        $fiveLowest = array_slice($list,0,5);
        $list2 = array_sort($inside, 'distance', SORT_DESC);
        $inside = array_slice($list2,0);

        return [json_encode($postcodeAddresses),
                json_encode($inside),
                json_encode($fiveLowest)];

    }


}