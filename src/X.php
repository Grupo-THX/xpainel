<?php
class X
{

	static function setRelatorioErros($errno, $errstr, $errfile, $errline)
	{
		try
		{
			if(! Sql::_rowCount("SELECT * FROM relatorio_erros WHERE relatorio_erros_arquivo = '{$errfile}' AND relatorio_erros_linha =".$errline))
			{
				Sql::_query("INSERT INTO relatorio_erros (relatorio_erros_cod,relatorio_erros_erro, relatorio_erros_arquivo, relatorio_erros_linha)
    												VALUES ({$errno},'{$errstr}', '{$errfile}', {$errline})");
			}
			Sql::_query("UPDATE relatorio_erros SET relatorio_erros_cont = relatorio_erros_cont+1, relatorio_erros_data = NOW() WHERE relatorio_erros_arquivo = '{$errfile}' AND relatorio_erros_linha =".$errline);

    		return true;

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getGerenciavel($id,$campo=false)
	{
		try
		{
			$result = Sql::_fetch('SELECT * FROM texto WHERE texto_id ='.$id);

			if(! $result)
			{
				return;
			}

			$result['imagem'] = U::getImg('imagens/conteudo/'.PASTA.'/'.$result['texto_id'].'.'.$result['texto_extensao']);

			for($i=1; $i<=10; $i++)
			{
				$result['imagem-'.$i.''] = U::getImg('imagens/conteudo/'.PASTA.'/'.$result['texto_id'].'-'.$i.'.'.$result['texto_extensao'.$i]);
			}
			$i=1;
			while(isset($result['arquivo_extensao'.$i]))
			{
				$path = '/arquivos/conteudo/'.PASTA.'/'.$result['texto_id'].'_'.$i.'.'.$result['arquivo_extensao'.$i];
				if($result['arquivo_extensao'.$i] != '')
				{
					$result['arquivo-'.$i] = HTTP.$path;
				}
				$i++;
			}

			foreach($result as $conteudo => $valor)
			{
				$result[$conteudo] =  U::clearStr($result[$conteudo]);
			}
			if($campo)
			{
				return $result[$campo];
			}

			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function ajudaDev($str)
	{
		try
		{
			if(DEBUG)
			{
				echo '<p><pre>'.print_r($str,1).'</pre></p></hr />';
			}
			return;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function replace($padrao,$array)
	{
		try
		{
			foreach($array as $key => $valor)
			{
				$padrao = str_replace("{{$key}}", $array[$key], $padrao);
			}
			return U::clearStr($padrao);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function protocolo($url = false)
	{
		try
		{
			if(! $url)
			{
				$url = HTTP;
			}
			if(isset($_SERVER['HTTPS']))
			{
				$url = str_replace('http://', 'https://', $url);
			}

			return $url;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function logXpainel($arquivo = 'INDEFINIDO',$dados = '')
	{
		try
		{
			$quebra = chr(13).chr(10);
			$dados='LOG:'.$dados.$quebra;
			$variaveis = $quebra.'ERROS:'.$quebra.'<pre>'.print_r(error_get_last(),true).'</pre>'.$quebra;
			$variaveis .= $quebra.'POST:'.$quebra.'<pre>'.print_r($_POST,true).'</pre>'.$quebra;
			$variaveis.= $quebra.'GET:'.$quebra.'<pre>'.print_r($_GET,true).'</pre>'.$quebra;
			$variaveis.= $quebra.'SERVER:'.$quebra.'<pre>'.print_r($_SERVER,true).'</pre>'.$quebra;
			$cabecalho =$quebra.date('d/m/Y G:s:i').$quebra.'INICIO_______________________________________________________________';
			$fim = $quebra.'_______________________________________________________________END';
			$dados = $cabecalho.$dados.$variaveis.$fim;
			$abre = fopen(ROOT.'/xpainel/logsXpainel/'.$arquivo.'-'.date('d-m-Y').'.txt', 'a');
			$escreve = fwrite($abre, $quebra.$dados);
			fclose($abre);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getValues($id)
	{
		try
		{
			$result = Sql::_fetchAll('SELECT * FROM forms WHERE form_categoria_id= ?',array($id));

	        foreach ($result as $campo => $valor)
	        {
	        	$value[$valor['form_chave']]=$valor;
	        }
			return $value;
		}
		catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }

	}

	static function setValuesForm($id)
	{
		$values = SELF::getValues($id);
		foreach($values as $value)
		{
			if(!isset($_POST[$value['form_chave']]) || $value['form_chave']=='clientes_senha')
			{
				$_POST[$value['form_chave']]='';
			}
		}
	}
	static function checkManutencao()
	{
		try
		{
			$paginasLiberadas = array('xpainel/rest/testMail.php', '/xpainel/rest/testMail.php', '/xpainel/js/funcoes.php', '/xpainel/css/style.php');
			if(in_array($_SERVER['PHP_SELF'], $paginasLiberadas))
			{
				return;
			}

			if(X::getParametro('manutencao') ==1 )
			{
				echo '<div id="ststusManutencao" style="position: fixed;left: 0;bottom: 0;width: 100%; height:20px;z-index: 9999999999999999999;    background: red;color: white;font-weight: bold;text-align: center;"><strong>Cuidado!!! : </strong>Este site está em modo manutenção !! Apenas você tem acesso a esse site !!!</div>
				<style>html,body{padding-bottom:20px;}</style>';

				$tml = '
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset="utf-8">
					<meta http-equiv="X-UA-Compatible" content="IE=edge">
					<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
					<title></title>
				</head>
				<body>
					'.U::clearStr(X::getParametro('textoManutencao'))
					.X::init().'
				</body>
				</html>';
				if(!in_array($_SERVER['REMOTE_ADDR'],unserialize(X::getParametro('ipsLiberados'))))
				{
					die($tml);
				}
			}
		}
		catch( Exception $e )
		{
			echo(sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__));
		}
	}
	static function setContato($remetente = 'nome', $assunto = 'assunto', $arquivo='arquivo',$imagem=false, $news = false, $salva_conversa = true)
	{
		try
		{
			Seguranca::checkCaptcha();

	        $local = $_SERVER['PHP_SELF'];
	        {
	        	if(isset ($_POST['xlocal']))
	        	{
	        		$local = $_POST['xlocal'];
	        		unset($_POST['xlocal']);
	        	}
	        }




		 	$msg='';

		 	$imagem = array();
		 	if(isset($_POST['imagem']))
			{
				$imagem = $_POST['imagem'];
				unset($_POST['imagem']);
			}

			foreach($imagem as $key => $img)
			{
				$msg.='<CENTER><img src="'.$img.'" style="max-width: 100%; height: auto; border-radius: 10px;" title="Imagem" alt="Libere as imagens para visualizar"  /><br /><br /></CENTER>';
			}


		 	$msg.='<br /><br /><h2 style="color:red; font-size:18px">Dados do Contato:</h2><hr />';



		 	if(isset($_POST[$assunto]))
			{
		 		$assunto=$assunto_a_excluir=$_POST[$assunto];
			}
			else
			{
				if($assunto != '')
				{
					$assunto=$assunto_a_excluir=$assunto;
				}
				else
				{
					$assunto=$assunto_a_excluir=X::getParametro('FromName').' mensagem do site';
				}
			}

			E::getDepartamento();

		  	foreach($_POST as $campo => $valor)
			{
				if($valor == 'SEPARADOR')
				{
					$msg.='<br /><br /><h2 style="color:red; font-size:18px">'.$campo.'</h2><hr />';
				}
				else
				{
					if ($valor != '' && $campo != $assunto_a_excluir && $campo != 'x' && $campo != 'y')
					{
						$msg.='<strong>'.ucwords(str_replace(array('clientes_','_'),' ',$campo))."</strong>: ".strip_tags($valor)."<br />";
					}
				}
			}


			$anexo=array();
			$anexos = '';
			if(count($_FILES) > 0)
			{
				if(!file_exists(ROOT.'/anexosCoDiFiFy'))
				{
					mkdir (ROOT.'/anexosCoDiFiFy', 0755 );
				}

				$permit=array('doc','docx','pdf','png', 'jpg', 'jpeg');
				$i=0;

				foreach($_FILES[$arquivo]['name'] as $file)
				{
					if($_FILES[$arquivo]['error'][$i] == 0)
					{
						if(!in_array(U::getExtensao($_FILES[$arquivo]['name'][$i]), $permit))
						{
						return X::alert('Arquivo Inválido os Formatos autorizados são : '.implode(',',$permit));
						}

						if(move_uploaded_file($_FILES[$arquivo]['tmp_name'][$i],ROOT.'/anexosCoDiFiFy/'.$_FILES[$arquivo]['name'][$i]))
						{
							$anexo[]= ROOT.'/anexosCoDiFiFy/'.$_FILES[$arquivo]['name'][$i];
							$anexos.='<br /><strong>'.($i+1).')</strong> '.$_FILES[$arquivo]['name'][$i];
						}
					}
					$i++;
				}
				if($anexos != '')
				{
					$msg.='<br /><br /><h2 style="color:red; font-size:18px">Arquivos Anexados</h2><hr />'.$anexos;
				}
			}

			if(isset($_SERVER['HTTP_REFERER']))
			{
				if(basename($_SERVER['HTTP_REFERER']) == 'lista-de-orcamento.php')
				{
					$hash = ' - Código: '. U::getToken(8);
					$assunto.=$hash;
					$msg.= Cart::getProdutosEmail();
				}
			}


			$envio = E::email($_POST['email'],$_POST[$remetente],$assunto, $msg, $anexo);

			if($envio)
			{

				if($news)
				{
					$_POST['newsletter_email'] = $_POST['email'];
					$_POST['newsletter_nome'] = $_POST['nome'];
					X::newsletter();
				}
				if($salva_conversa)
					X::setContatoDoSite($local,$envio);

				return X::alert("Sua mensagem foi Enviada !! \\n Obrigado",HTTP);
			}
			else
			{
				return X::alert("Erro ao enviar sua mensagem. Tente novamente mais tarde.");
			}


		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	static function setComprovante($remetente, $assunto=false, $arquivo='arquivo')
	{
		try
		{
			if(! $_POST)
			{
	        	return false;
	    	}



			if(! isset($_POST['assunto']) && $assunto)
			{
				$_POST['assunto'] = $assunto;
			}


			$assunto_a_excluir=$assunto;
		 	$msg='';
		 	if($imagem)
		 	{
				$msg.='<CENTER><img src="'.$imagem.'" style="max-width: 100%; height: auto; border-radius: 10px;" title="Imagem" alt="Libere as imagens para visualizar"  /><br /><br /></CENTER>';
		 	}

		 	$msg.='<br /><br /><strong>Dados do Contato:</strong><br /><br />';



			 if ($assunto)
			 {
			 	if (isset($_POST[$assunto]))
				{
			 		$assunto=$_POST[$assunto];
				}
			 	else
			 		$assunto=$assunto;
			 }
			 else
		 	{
				$assunto.="Mensagem do site";
			}


		  	foreach($_POST as $campo => $valor)
			{
				if ($valor != '' && $campo != $assunto_a_excluir && $campo != 'x' && $campo != 'y')
				$msg.='<strong>'.ucwords(str_replace('_',' ',$campo))."</strong>: ".strip_tags($valor)."<br />";
			}


			$anexo=array();
			$anexos = '';
			if( $_FILES)
			{
				if(!file_exists(ROOT.'/anexosCoDiFiFy'))
				{
				mkdir (ROOT.'/anexosCoDiFiFy', 0755 );
				}

				$permit=array('doc','docx','pdf','png', 'jpg', 'jpeg');

				foreach($_FILES[$arquivo]['name'] as $file)
				{
					if($_FILES[$arquivo]['error'] == 0)
					{
						if(!in_array(U::getExtensao($_FILES[$arquivo]['name']), $permit))
						{
						return X::alert('Arquivo Inválido os Formatos autorizados são : '.implode(',',$permit));
						}

						if(move_uploaded_file($_FILES[$arquivo]['tmp_name'],ROOT.'/anexosCoDiFiFy/'.$_FILES[$arquivo]['name']))
						{
							$anexo[]= ROOT.'/anexosCoDiFiFy/'.$_FILES[$arquivo]['name'];
							$anexos.='<strong>'.($i+1).')</strong> '.$_FILES[$arquivo]['name'];
						}
					}

				}
				if($anexos != '')
				{
					$msg.='<hr style="border-top: solid #EEE 1px;" /><br /><br /><strong>Arquivos anexados:</strong><br /><br />'.$anexos;
				}
			}
			//die(__LINE__.'=====>');


			$envio = E::email($_POST['email'],$_POST[$remetente],$assunto, $msg, $anexo);
			if($envio)
			{
				return X::alert("Sua mensagem foi Enviada !! \\n Obrigado",HTTP);
			}
			else
			{
				return X::alert("Erro ao enviar sua mensagem. Tente novamente mais tarde.");
			}


		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function setContatoDoSite($local,$texto)
	{
		try
		{
			return Sql::_query("INSERT INTO contato_do_site (contato_do_site_assunto,contato_do_site_texto,contato_do_site_ip) VALUES (?, ?, '{$_SERVER['SERVER_ADDR']}')", array($local, $texto));
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function alert($msg=false,$redireciona=false, $stopLoading=true)
	{
		try
		{
			$stopLoading = $stopLoading ? "parent.loadedX();" : '';
			if($redireciona)
			{
				$redireciona="top.location='{$redireciona}';";
			}

			if(!$msg)
			{
				return "<script>{$stopLoading} {$redireciona}</script>";
			}

			return "<script>{$redireciona} {$stopLoading} alert('{$msg}');</script>";
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getParametro($chave)
	{
		try
		{
			$result = Sql::_fetch("SELECT parametros_valor FROM parametros WHERE parametros_chave='{$chave}'");
			if(isset($result['parametros_valor']))
			{
				return X::protocolo($result['parametros_valor']);
			}
			return;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}
	static function loadingX()
	{
		try
		{
			return '<div id="loadingX" ondblclick="'.JQUERY.'(this).fadeOut()" style="background-image: url('.X::protocolo().'/xpainel/imagens/loading.gif);"></div>';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getParametros($tipo)
	{
		try
		{
			return Sql::_fetchAllAssoc("SELECT parametros_chave, parametros_valor FROM parametros WHERE parametros_tipo LIKE '$tipo'");

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}
	static function newsletter()
	{
		try
		{

			if(! isset($_POST['g-recaptcha-response']) && (! isset($_POST['xblock']) || $_POST['xblock'] != ''))
			{
				return X::alert('Robôs são bloqueados. \n Prove que você não é um robô.');
			}


	    	if(isset($_POST['g-recaptcha-response']))
			{
	        	$recaptcha = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lfblg0TAAAAALIOH5uSpZQ0gqg4ObHWZs2_oQU9&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']));

	        	if (! $recaptcha->success)
	        	{
				    return X::alert('Robôs são bloqueados. \n Prove que você não é um robô.');
				}
				unset($_POST['g-recaptcha-response']);
	    	}


			if(! isset($_POST['newsletter_email']))
			{
				return X::alert('Email Inválido');
			}

	   			if(U::validaEmail($_POST['newsletter_email']))
				{
					$_POST ['newsletter_nome'] = isset($_POST ['newsletter_nome']) ? $_POST ['newsletter_nome'] : '';

					$result = Sql::_query("INSERT INTO newsletter (newsletter_nome, newsletter_email)
													SELECT ?, ? FROM DUAL
														WHERE NOT EXISTS (SELECT * FROM newsletter
														 WHERE  newsletter_email = ? )", array($_POST['newsletter_nome'], $_POST['newsletter_email'], $_POST['newsletter_email']));
					if($result)
					{
						return X::alert('Email Cadastrado',false,true);
					}
					else
					{
						return X::alert('Email Já Cadastrado',false,true);
					}
				}
				return X::alert('Email Inválido',false,true);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function LiberaAcesso()
	{
		try
		{

			$result = Sql::_fetch("SELECT parametros_valor FROM parametros WHERE parametros_chave = 'ipsLiberados'");
			$novoip = $result['parametros_valor'] == '' ? array() : unserialize($result['parametros_valor']);
			array_push($novoip, $_SERVER["REMOTE_ADDR"]);
			$serial = serialize($novoip);
			$sql = "UPDATE parametros SET parametros_valor = '{$serial}' WHERE parametros_chave = 'ipsLiberados'";
			//die($sql);
			$res = Sql::_query($sql);

    		if($res)
    		{
    			return "<script>location='".HTTP."'</script>";
    		}


		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function init($lado = 'right', $params = '')
	{
		try
		{
			$dominio = parse_url(HTTP);
			$retorno = '
			<iframe onClick="'.JQUERY.'(this).toggleClass(\'xdebugOpen\')" name="xgetDados" src="" id="xgetDados" style="'.(MODE_DEVELOPER ? '' : 'display: none !important').'" class="ocultaDebug"></iframe>
			<script src="'.HTTP.'/xpainel/js/vendor/instagramFeed/jquery.instagramFeed.min.js"></script>

			<a target="_blank" href="https://xpainel.com.br?cliente='.$dominio['host'].'">
				<img  title="Site Seguro" alt="Site Seguro" style="z-index:99999999; width:16px !important; height:13px !important; position:fixed !important; bottom:0 !important; cursor:pointer; right:0 !important;" src="'.HTTP.'/xpainel/imagens/lock.png" border="0" />
			</a>'.X::getJsCss().X::loadingX();

			if(DEBUG)
			{
				 $retorno.= X::getDebug().'

				 <div id="debugMediaQuerieX" class="ocultaDebug"></div>
					 <div class="dieBug ocultaDebug"  onClick="'.JQUERY.'(this).toggleClass(\'dieBugOpen\')">
					 <pre>
					 	<h2>Echo Debug X-Painel</h2>
					 	<hr />'.implode('<br /><hr /><br />',array_reverse($GLOBALS['Xdebug'])).'
					 </pre>
				 </div>
				 <div class="closeAllDegugX" onclick="'.JQUERY.'(\'.ocultaDebug\').toggleClass(\'ocultaDebugNow\'); '.JQUERY.'(this).toggleClass(\'closeAllDegugXOn\')"><img src="//xpainel.com/site/images/main-logo.png" /></div>
				 ';
			}


			$atalhosDeTeclado = '
			<script>
			 	document.onkeyup = function(e) {
			 		console.log(e.which);
			 	if (e.ctrlKey && e.shiftKey && e.which == 88) {
			 		loadingX();
				    '.JQUERY.'("#xgetDados").attr("src","?'.X.'debug");
				 }

				 if (e.ctrlKey && e.shiftKey && e.which == 83) {
			 		loadingX();
				    '.JQUERY.'("#xgetDados").attr("src","?'.X.'");
				 }

				 if (e.ctrlKey && e.which == 88) {
				    '.JQUERY.'(".ocultaDebug").toggleClass("ocultaDebugNow");
				    '.JQUERY.'(".closeAllDegugX").toggleClass("closeAllDegugXOn");
				  }

				};
			 </script>';
			 $retorno.=$atalhosDeTeclado;

			 $retorno.=X::getApis();

			return $retorno;

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getApis()
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM parametros WHERE parametros_tipo = 'apis'";
			$result = Sql::_fetchAll($sql);

			foreach($result as $res)
			{
				$ret.=$res['parametros_valor'];
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getDadoSite($campo)
	{
		try
		{
			if(! isset($GLOBALS[X]['dados_do_site']))
			{
				$GLOBALS[X]['dados_do_site'] = self::getGerenciavel(1);
				$GLOBALS[X]['dados_do_site']['email'] = X::getParametro('emailFrom');
			}

			return $GLOBALS[X]['dados_do_site'][$campo];
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function setJsCss($funcaoAdd)
	{
		try
		{
			$GLOBALS['Xjs'][] = $funcaoAdd;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getJsCss()
	{
		try
		{

			$retorno ='
			<script> var HTTP = "'.X::protocolo().'"; </script>
			<script src="'.X::protocolo().'/xpainel/js/funcoes.php" type="text/javascript" charset="utf-8"></script>
			<link href="'.X::protocolo().'/xpainel/css/style.php" rel="stylesheet">
			';
			if(X_ECOMMERCE)
			{
				$retorno.='<script type="text/javascript" src="//stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>';
			}
			$js="
			if(document.getElementById('clientes_estado') != null && document.getElementById('clientes_cidade') != null)
			{
				new dgCidadesEstados({
	                cidade : document.getElementById('clientes_cidade'),
	                estado : document.getElementById('clientes_estado'),
	                estadoVal : '".Cliente::getDado('clientes_estado')."',
	                cidadeVal : '".Cliente::getDado('clientes_cidade')."'
	            });
        	}";
			foreach($GLOBALS['Xjs'] as $script)
			{
				$js.= $script;
			}

			$retorno.= '<script>window.onload = function () {'.$js.'}</script>';
			return SCRIPTS_ADICIONAIS.$retorno;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function head()
	{
		try
		{
			return Seo::getSeo().SCRIPTS_ADICIONAIS;
		}
		catch( Exception $e )
		{
			X::sendErrors($e);
		}
	}
	static function getDebug()
	{
		// $constantes = SUPER_DEBUG ? get_defined_constants(true) : array('user' => array('SUPER_DEBUG' => $_SERVER['REMOTE_ADDR']));
		// return '
		// 	<div class="xdebug ocultaDebug" title="X-Debug" onClick="'.JQUERY.'(this).toggleClass(\'xdebugOpen\')">
		// 		<CENTER><a target="xpainelSite" href="http://xpainel.com/site"><img src="//xpainel.com/site/images/main-logo.png"/></a></CENTER>
		// 		<pre>
		// 			<h1>DEBUG SITE: '.$_SERVER['HTTP_HOST'].'</h1><hr />
		// 			<h2>CONSTANTES:</h2>'.print_r($constantes['user'],true).'<hr />
		// 			<h2>POST:</h2>'.print_r($_POST,true).'<hr />
		// 			<h2>GET:</h2>'.print_r($_GET,true).'<hr />
		// 			<h2>REQUEST:</h2>'.print_r($_REQUEST,true).'<hr />
		// 			<h2>SESSION:</h2>'.print_r($_SESSION,true).'
		// 			<hr />
		// 			<h2>COOKIE:</h2>'.print_r($_POST,true).'<hr />
		// 			<h2>ERROS:</h2>'.print_r(error_get_last(),true).'
		// 		</pre>
		// 	</div>';
	}
	static function dieBug($str)
	{
	    if(DEBUG)
	    {
		  die('<div class="dieBug ocultaDebug" onClick="'.JQUERY.'(this).toggleClass(\'dieBugOpen\')"  ondblclick="this.style.display=\'none\'"><pre><h2>Die Debug X-Painel</h2><hr />'.$str.'</pre></div>');
	    }
	}



	function _die($str)
	{
		echo '<div style="'.BACKTRACE_CSS.'">
				<h2 style="padding: 10px 15px;color: #2087AC;">
						X-Painel Erro Fatal :(
				</h2>
				<hr />
				<h1 style="font-size: 23px;line-height: 30px;padding: 0 15px;color: red;padding: 0 15px; color: red">'.$str.'</h1>
				<div class="debug_print_backtrace" style="color: black;
				    font-weight: bold;
				    font-size: 19px;
				    padding: 0 15px;">';
						debug_print_backtrace();
		echo '		</div>
				</pre>
			  </div>';

			  die();
	}




	static function echoBug($str)
	{
	    echo '<div style="'.BACKTRACE_CSS.'">
				<h2 style="padding: 10px 15px;color: #2087AC;">
						X-Painel Erro Fatal :(
				</h2>
				<hr />
				<h1 style="font-size: 23px;line-height: 30px;padding: 0 15px;color: red;padding: 0 15px; color: red">'.$str.'</h1>
				<div class="debug_print_backtrace" style="color: black;
				    font-weight: bold;
				    font-size: 19px;
				    padding: 0 15px;">';
						debug_print_backtrace();
		echo '		</div>
				</pre>
			  </div>';
			  die();
	}
	static function print_r($array)
	{
		echo '<fieldset style="border: solid rgb(32, 135, 172) 5px; display: block; padding: 5px"><legend>X.Print</legend><pre>'.print_r($array,true).'</pre></fieldset>';
	}

	static function sendErrors($errno, $errstr, $errfile = '', $errline = '', $errcontex = '')
	{
		 echo '<div style="'.BACKTRACE_CSS.'" ondblclick="this.style.display=\'none\'">
				<h2 style="padding: 10px 15px;color: #2087AC;">
						X-Painel Erro Fatal :(
				</h2>
				<hr />
				<h1 style="font-size: 30px;
				    line-height: 72px;
				    padding: 0 15px; color: red">'.("<strong>Message:</strong><br /><strong>Erro:</strong>$errno, $errstr, $errfile, $errline, $errcontex").'</h1>
				<div class="debug_print_backtrace" style="color: black;
				    font-weight: bold;
				    font-size: 19px;
				    padding: 0 15px;"><pre>';
						debug_print_backtrace();
		echo '		</pre></div>
				</pre>
			  </div>';
			  die();
	}
}
