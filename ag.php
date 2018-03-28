<!DOCTYPE html>
<html>
	<head>
		<title>GRASP-Pmedianas</title>
		<link type='text/css' rel='stylesheet' href='style.css'/>
	</head>
	
	<body>
	  <p>
	  <?php
	  	include("includes.php");
	  	include("classes.php");
	  	include("demandas.php");

		date_default_timezone_set('America/Sao_Paulo');

		//Numero de facilidades
		$facilidades = 5;

		//Raio de cobertura das torres de radiotransmissão
		$cobertura = 1.5;

		//usado no calculo do tempo gasto com o processamento
		$hora_ini = date("G:i:s");
  		
		// tamanho da populacao	
  		$tamPopulacao = 100;

  		// taxa de mutacao
  		$txMutacao = .3;

  		// taxa de cruzamento
  		$txCrossover = .6;

  		// intervalo de gerações
  		$maxGeracoes = 100;

  		// usar elitismo na solucao
  		$elitismo = true;

  		// Numero de genes
  		$numGenes = count($ponto) -1;

  		$geracao = array();

		$populacao = array();

  		$individuos = array();

  		//Gera populacoa inicial
  		for ($i=0; $i<=$tamPopulacao-1; $i++)
  		{	
  			$individuo = "";
  			$teste = 0;

  			for ($b=0; $b<= $numGenes; $b++)
  			{
  				$gene = rand(0, 1);

  				if ($gene >= 1)
  				{
					if ($teste >= $facilidades)
					{
						$gene = 0;	
  					}

  					$teste++;
  				} 

				$individuo = $individuo.$gene;
  			}

  			$aptidao = gera_aptidao($individuo,$ponto,$cobertura);

  			$individuos[] = new Individuo(0,$individuo,$aptidao);

  		}

		//Ordena o array de pontos em ordem decrescente	de carga
		usort($individuos, array ("Individuo", "cmp_obj"));

  		// for ($a=0;$a <= count($individuos)-1;$a++)
  		// {
  		// 	echo "<br>Populacao: ".$individuos[$a]->populacao." Inividuo: ".$a." - ".$individuos[$a]->genes." / ".$individuos[$a]->aptidao;
  		// }	

        $geracao = 1;
		$individuos2 = array();

		while ($geracao <= 100) {

			for ($i=0; $i<=count($individuos)-1; $i++)
			{
				$individuos2[$i] = clone $individuos[$i];
			}

			unset($individuos);

			$individuos = array();

  			$conta = 0;

            if ($elitismo == true) {
	  			$individuos[] = new Individuo($geracao,$individuos2[0]->genes,$individuos2[0]->aptidao);
	  			$conta++;
            }
	
            // Gera nova populacao
	        while ($conta <= $tamPopulacao-1)
            {
				$pais = array();

				//selecao por torneio, de 3 pais
				for ($a=0; $a<=2; $a++)
				{
					$val = rand(0, (($tamPopulacao-1) * $txCrossover)-1);
					$pais[] = new Individuo(0,$individuos2[$val]->genes,0);			
//echo "<br>"."pop ".$geracao." ".$pais[$a]->genes;
				}
		        //ordena os pais do melhor para o pior
		        usort($pais, array ("Individuo", "cmp_obj"));

		        //Crossover
				$pontoCorte = rand(1, $numGenes-1);

				$pai1_1 = substr($pais[0]->genes, 0, $pontoCorte);
				$pai1_2 = substr($pais[0]->genes, $pontoCorte, $numGenes-($pontoCorte-1));

				$pai2_1 = substr($pais[1]->genes, 0, $pontoCorte);
				$pai2_2 = substr($pais[1]->genes, $pontoCorte,$numGenes-($pontoCorte-1));

	            unset($pais);

				$filho1 = $pai1_1.$pai2_2;

				if (substr_count($filho1, '1') <> $facilidades)
				{
					continue;
				}

				$individuos[] = new Individuo($geracao,$filho1,0);

				$conta++;

				if ($conta >= $tamPopulacao)
				{
					break;
				}

				$filho2 = $pai2_1.$pai1_2;

				if (substr_count($filho1, '1') <> $facilidades)
				{
					continue;
				}

				$individuos[] = new Individuo($geracao,$filho2,0);
				$conta++;
            }
	        
            //Aplica Mutacao de acordo com a taxa
	  		for ($b=0;$b <= (count($individuos)*$txMutacao)-1;$b++)
            {
            	$pos1 = rand(1,$numGenes-1);
            	$pos2 = rand(1,$numGenes-1);

            	if ($pos1 > $pos2)
            	{
            		$pos3 = $pos1;
            		$pos1 = $pos2;
            		$pos2 = $pos3;
            	}
            	$val1 = substr($individuos[$b]->genes,$pos1-1,1);
            	$val2 = substr($individuos[$b]->genes,$pos2-1,1);

	            $individuos[$b]->genes = substr($individuos[$b]->genes,0,$pos1-1).$val2.substr($individuos[$b]->genes,$pos1,($pos2-$pos1)-1).$val1.substr($individuos[$b]->genes,$pos2,$numGenes-($pos2-1));

	            if (strlen($individuos[$b]->genes) > $numGenes)
	            {
	            	$individuos[$b]->genes = substr($individuos[$b]->genes,0,$numGenes+1);
	            }

   	// 			if (substr_count($individuos[$b]->genes, '1') > $facilidades)
				// {
				// 	echo "<br>".$individuos[$b]->genes;
				// }
			}

	  		//Gera aptidao para esta populacao
	  		for ($b=0; $b <= count($individuos)-1; $b++)
	  		{
	  			$individuo = $individuos[$b]->genes;

	  			//echo "<br>".count($ponto);

	  			$aptidao = gera_aptidao($individuo,$ponto,$cobertura);

  				$individuos[$b]->aptidao = $aptidao;
	  		}

			//Ordena o array de pontos em ordem decrescente	de carga
			usort($individuos, array ("Individuo", "cmp_obj"));

			echo "<br>Populacao: ".$individuos[0]->populacao." Inividuo: ".$individuos[0]->genes." / ".$individuos[0]->aptidao;

            $geracao++;
	    }  

		$hora_fim = date("G:i:s");

		$best_solucao = array();

  		for ($b=0; $b <= numGenes-1; $b++)
  		{
  			if (substr($individuos[0]->genes, $b, 1) == "1") 
  			{
  				$best_solucao[] = clone($ponto[$b]);
  				unset($ponto[$b]); 
  			}
  		}

		monta_mapa($best_solucao,$ponto,$cobertura);	

		// echo "<br>Exportando Resultados...";

		// $fp = fopen('results.csv', 'w');

 	// 	foreach ($cargas as $linha)
 	// 	{
  //    		fputcsv($fp,explode(',',$linha));
		// }
		// fclose($fp);

		echo "<br>Hora Inicial: ".$hora_ini."<br>";
		echo "Hora Final..: ".$hora_fim;

		?> 

		<br>
		<a href="mapa.html" target="_blank">Visualizar pontos no mapa</a>
		</p>
	</body>
</html>

