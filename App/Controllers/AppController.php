<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

/**
 * 
 */
class AppController extends Action{
	
	public function timeline(){
		#inicio timeline
		$this->validaAuth();

		$tweet = Container::getModel('Tweet');

		$tweet->__set('id_usuario',$_SESSION['id']);

			#recuperando os tweets
		$tweets = $tweet->getAll();

		$this->view->tweets = $tweets;

		#instanciando usuarios ara recuperar suas informações
		$usuario = Container::getModel('Usuario');
		#setando o id com o valor do id da sessão do usuario logado
		$usuario->__set('id',$_SESSION['id']);

		#dessa forma poderemos utilizar esses atributos dentro da view timeline
		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweet = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();


		#caso de sucesso
		$this->render('timeline');

	}


	public function tweet(){

		$this->validaAuth();

		$acao = isset($_GET['apagar']) ? $_GET['apagar'] : '';
		$id_tweet = isset($_GET['id_tweet']) ? $_GET['id_tweet'] : '';

			#instanciando model
		$tweet = Container::getModel('Tweet');

		$tweet->__set('tweet', $_POST['tweet']);
			#recupera id do usuario atraves da global SESSION
		$tweet->__set('id_usuario', $_SESSION['id']);

		$tweet->salvar();

		if($acao == 'apagar'){
			$tweet->apagarTweet($id_tweet);
		}

		header('Location:	 /timeline');


		###################################
	}

	#valida se usuario está logado
	public function validaAuth(){

		session_start();

		if (!isset($_SESSION['id']) || $_SESSION['id'] == ''|| !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location:	 /?login=erro');
		}else {
			return true;
		}
	}

	#quem seguir
	public function quemSeguir(){
		#verifica se usuário está conectado
		$this->validaAuth();
		#se o indice da globar get estiver setado vamos atribuir o valor a variavel, caso contrario atribui vazio
		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

		$usuarios = array();

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id',$_SESSION['id']);

		#dessa forma poderemos utilizar esses atributos dentro da view timeline
		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweet = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();



		if ($pesquisarPor != '') {
			$usuario->__set('nome', $pesquisarPor);
			$usuarios = $usuario->getAll();

		}

		$this->view->usuarios = $usuarios;

		$this->render('quemSeguir');
	}

	public function acao(){

		$this->validaAuth();

		$seguir = Container::getModel('Seguir');

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : ''; #usuario que iremos seguir
		$seguir->__set('id_usuario_seguindo',$id_usuario_seguindo);
		$seguir->__set('id',$_SESSION['id']);

		if ($acao == 'seguir') {
			$seguir->seguirUsuario();
		}else if ($acao == 'deixar_de_seguir') {
			$seguir->deixarSeguirUsuario();
		}

		header('Location:	 /quem_seguir');
	}
}



?>