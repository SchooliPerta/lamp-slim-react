<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlunniController
{
  public function index(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM alunni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }
  
  public function show(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM alunni WHERE id = $id");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    if(count($results) > 0){
      $response->getBody()->write(json_encode($results[0]));
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(404);
    }
  }

  public function create(Request $request, Response $response, $args){
    $data = $request->getParsedBody();
    $nome = $data['nome'];
    $cognome = $data['cognome'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("INSERT INTO alunni (nome, cognome) VALUES ('$nome', '$cognome')");
    
    if($result){
      return $response->withHeader("Content-type", "application/json")->withStatus(201);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }

  public function update(Request $request, Response $response, $args){
    $id = $args['id'];
    $data = $request->getParsedBody();
    $nome = $data['nome'];
    $cognome = $data['cognome'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("UPDATE alunni SET nome='$nome', cognome='$cognome' WHERE id=$id");
    
    if($result){
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }

  public function destroy(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("DELETE FROM alunni WHERE id=$id");
    
    if($result){
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }
}