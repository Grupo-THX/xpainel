<?php
class Traducao{

	static function idioma()
	{
		try
		{
			self::setIdioma();
			require_once($_SERVER['DOCUMENT_ROOT'].'/xpainel/class/connection'.$_SESSION[X]['idioma'].'.php');
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function setIdioma()
	{
		try
		{
			if(! isset($_SESSION[X]['idioma']))
			{
				$_SESSION[X]['idioma'] = '';
			}

			if(isset($_GET['idioma']))
			{
				$_SESSION[X]['idioma'] = $_GET['idioma'];
			}

			$arquivo_de_idioma = ROOT.'/xpainel/idiomas/'.$_SESSION[X]['idioma'].".lang";
			if(file_exists($arquivo_de_idioma))
			{
				$idioma = fopen($arquivo_de_idioma, "r") or die("Arquivo não encontrado!");
				$_SESSION[X]['texto_idioma'] = unserialize(fread($idioma,filesize($arquivo_de_idioma)));
				fclose($idioma);
			}

			Traducao::_setLocale();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function _setLocale()
	{
		try
		{
			switch ($_SESSION[X]['idioma'])
			{
				case 'alemao':
					setlocale(LC_TIME, "de_DE.UTF8" );
				break;

				case 'ingles':
					setlocale(LC_TIME, "en_US.UTF8" );
				break;

				default:
					setlocale(LC_TIME, 'pt_BR.UTF8' );
				break;
			}


		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function traduz($id)
	{
		try
		{
			return $_SESSION[X]['texto_idioma'][$id];
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


}