 <?php

	function union(array $files,$name)
	{
		$union = NULL;

		for($i=0;$i<count($files);$i++)
		{
			if(file_exists($files[$i]))
			{
				$union .= file_get_contents($files[$i])."\n";
			}
		}

		$fp = fopen($name,"w");

		fwrite($fp,$union);

		fclose($fp);
	}

	function array_search_multi($busca, $arrays)
	{
   		foreach($arrays as $array)
   		{
       	   	if($i = array_search($busca,$array) !== false)
       	   	{
                return $i;
	        }
       	}
    	return false;

	}

	function distancia($lat1, $lon1, $lat2, $lon2) {

		$lat1 = deg2rad($lat1);
		$lat2 = deg2rad($lat2);
		$lon1 = deg2rad($lon1);
		$lon2 = deg2rad($lon2);

		$latD = $lat2 - $lat1;
		$lonD = $lon2 - $lon1;

		$dist = 2 * asin(sqrt(pow(sin($latD / 2), 2) +
		cos($lat1) * cos($lat2) * pow(sin($lonD / 2), 2)));
		$dist = $dist * 6371;
		return number_format($dist, 2, '.', '');
	}

	function gera_aptidao($individuo,$ponto,$cobertura)
	{
		$carga = 0;
		
		for ($i=0; $i <= count($ponto)-1; $i++)
		{
    		$ponto[$i]->atendido = false;		
		}

		//Cria a matriz de distâncias
		for($i = 0; $i <= strlen($individuo)-1; $i++)
		{
			if (substr($individuo, $i, 1) == "1")
			{	
				for($k = 0; $k <= count($ponto)-1; $k++)
				{
					$dist = distancia($ponto[$i]->lat, $ponto[$i]->lng, $ponto[$k]->lat, $ponto[$k]->lng);
					
					if ($dist <= $cobertura and $dist > 0 and $ponto[$k]->atendido == false)
					{
						$carga = $carga +1;
						$ponto[$k]->atendido = true;
					}	
				}
			}	
		}	
		return $carga;
	}

	function monta_mapa($s,$p,$c)
	{
		$fp = fopen("mapa2.html", "w");

		for($i = 0; $i <= count($s)-1; $i++)  	
		{			
			fwrite($fp,"{lat: ".$s[$i]->lat.", lng: ".$s[$i]->lng.", nome: \"".
				$s[$i]->nome."\", tipo: 1},\n");
		}

		//Marca demandas no MAPA
		for($i = 0; $i <= count($p)-1; $i++)  	
		{	
			$string =  serialize($s);
 	
 			if(strpos($string, $p[$i]->nome) == false)
			{
				fwrite($fp,"{lat: ".$p[$i]->lat.", lng: ".$p[$i]->lng.", nome: \"".
					$p[$i]->nome."\", tipo: 0},\n");
			}
		}

		fwrite($fp,"];\n");
		fwrite($fp,"var raio = ".($c*1000).";\n");

		union(array("mapa1.html","mapa2.html","mapa3.html"),"mapa.html");
	}

	function selecaoTorneio($array,$txcross, $tam_pop)
	{

		for ($a=0; $a<=2; $a++)
		{
			$val = rand(0, ($tam_pop*$txcross)-1);
			$pais[] = new Individuo(0,$array[$val]->genes,$array[$val]->aptidao);				
		}

        //ordena a população
        usort($pais, array ("Individuo", "cmp_obj"));
    }

    function crossover($numGenes, $individuo_1, $individuo_2) {

		$pontoCorte = rand(0, $numGenes);

		$pai1_1 = substr($individuo1, 0, $pontoCorte);
		$pai1_2 = substr($individuo1, $pontoCorte+1,$numGenes-1);

		$pai2_1 = substr($individuo2, 0, $pontoCorte);
		$pai2_2 = substr($individuo2, $pontoCorte+1,$numGenes-1);

		$filho1 = $pai1_1.$pai2_2;

		$individuos[] = new Individuo($geracao,$filho1,0);

		$filho2 = $pai2_1.$pai1_2;

		$individuos[] = new Individuo($geracao,$filho2,0);
    }
?> 