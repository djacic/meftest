<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel
    </title>
    <script src="js/jquery.min.js" type="text/javascript">
    </script>
    <script>
        $(document).ready(function(){
           $("#distance").prop('checked',false);
           $("#distance").prop('disabled',true);
           $("#distance").change(function(){
                if ($(this).is(":checked"))
                {
                    $('.dist').removeClass('hidden');
                }
                else
                {
                    $('.dist').addClass('hidden');
                }
            });
        });

        function load(id){
            $.ajax({
                    url: "http://localhost/meftest/meftest/meftest/public/load/"+id,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(result){
                        $("#distance").prop('checked',false);
                        $("#distance").prop('disabled',false);
                        $("#feedback").html("");
                        var data = JSON.parse(JSON.stringify(result));
                        var adrese = JSON.parse(data[0]);
                        var skole = JSON.parse(data[1]);
                        var stanice = JSON.parse(data[2]);
                        if(adrese.length>0){
                            $(".adrese").html("");
                            for(var i = 0; i<adrese.length;i++){
                                $(".adrese").append("- "+adrese[i].street+", "+adrese[i].site_number+",<br/>"+adrese[i].site_description+"</br>");
                            }
                        }
                        else{
                            $(".adrese").html("Ne postoje zapisi o adresama.");
                        }
                        if(skole.length>0){
                            $(".skole").html("");
                            for(var i = 0; i<skole.length;i++){
                                $(".skole").append(skole[i].name+", <span class='dist hidden'>"+skole[i].distance.toFixed(2)+"km</span><br/>");
                            }
                        }
                        else{
                            $(".skole").html("Ne postoje zapisi o školama.");
                        }
                        if(stanice.length>0){
                            $(".stanice").html("");
                            for(var i = 0; i<stanice.length;i++){
                                $(".stanice").append(stanice[i].name+", <span class='dist hidden'>"+stanice[i].distance.toFixed(2)*1000+"m</span><br/>");
                            }
                        }
                        else{
                            $(".adrese").html("Ne postoje zapisi o stanicama.");
                        }
                        alert($(this).attr());

                    }
                    ,
                    error: function(xhr,status,error){
                        alert(xhr.status);
                    }
                }
            );
        }
    </script>
    <!-- Fonts -->
    <link rel="stylesheet" href="css/switch.css"/>
    <link rel="stylesheet" href="css/main.css"/>
    <!-- Styles -->
    <script>
    </script>
</head>
<?php
$group1 = array();
$group2 = array();
$group3 = array();
foreach($postcodeByGroup as $postcode){
    switch ($postcode->group_id){
        case 1:
            if(substr($postcode->postcode,0,2)==='SE'){
                $group1['postcode'][] = trim(substr($postcode->postcode,0,strpos($postcode->postcode,' ')));
            }
            break;
        case 2:
            if(substr($postcode->postcode,0,2)==='SE'){
                $group2['postcode'][] = trim(substr($postcode->postcode,0,strpos($postcode->postcode,' ')));
            }
            break;
        case 3:
            if(substr($postcode->postcode,0,2)==='SE'){
                $group3['postcode'][] = trim(substr($postcode->postcode,0,strpos($postcode->postcode,' ')));
            }
            break;
    }
}
$group1 = array_unique($group1['postcode']);
$group2 = array_unique($group2['postcode']);
$group3 = array_unique($group3['postcode']);
?>
<body>
<div id="omot">
    <div id="left">
        <ul class="lista">
            @foreach($groups as $group)
                @switch($group->id)
                    @case(1)
                    <li id="st">{{strtoupper($group->name)}}
                    </li>
                    <ul>
                        @foreach($group1 as $gr1)
                            <li id="nd">{{$gr1}}
                                <ul>
                                    @foreach($postcodeGroup1 as $grp1)
                                        <li id="rd">
                                            @if(substr($grp1->postcode,0,strpos($grp1->postcode,' ')) == $gr1)
                                                <a href="#tabela" onclick="load({{$grp1->id}})">{{$grp1->postcode}}
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                    @break
                    @case(2)
                    <li id="st">{{strtoupper($group->name)}}
                    </li>
                    <ul>
                        @foreach($group2 as $gr2)
                            <li id="nd">{{$gr2}}
                                <ul>
                                    @foreach($postcodeGroup2 as $grp2)
                                        <li id="rd">
                                            @if(substr($grp2->postcode,0,strpos($grp2->postcode,' ')) == $gr2)
                                                <a href="#tabela" onclick="load({{$grp2->id}})")">{{$grp2->postcode}}</a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                    @break
                    @case(3)
                    <li id="st">{{strtoupper($group->name)}}</li>
                    <ul>
                        @foreach($group3 as $gr3)
                            <li id="nd">{{$gr3}}
                                <ul>
                                    @foreach($postcodeGroup3 as $grp3)
                                        <li id="rd">
                                            @if(substr($grp3->postcode,0,strpos($grp3->postcode,' ')) == $gr3)
                                                <a href="#tabela" onclick="load({{$grp3->id}})">{{$grp3->postcode}}
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                    @break
                @endswitch
            @endforeach
        </ul>
    </div>
    <div id="right">
        <a id="tabela"></a>
        <table class="tabela">
            <thead>
            <tr>
                <th>
                    Distance<br>(0->inf)
                </th>
                <th>
                    5 najbližih autobuskih stanica
                </th>
                <th>
                    Škole u prečniku 5km
                </th>
                <th>
                    Adrese na tom poštanskom broju
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <label class="switch">
                        <input type="checkbox" id="distance">
                        <span class="slider"></span>
                    </label>
                </td>
                <td class="stanice">
                </td>
                <td class="skole">
                </td>
                <td class="adrese">
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div id="feedback">Izaberite poštanski broj u listi sa leve strane</div>
                </td>
            </tr>
            </tbody>
        </table>

    </div>
    <div class="cleaner">
    </div>
</div>
</body>
</html>
