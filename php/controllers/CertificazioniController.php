<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CertificazioniController
{
  public function index(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM certificazione");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }
  
  public function show(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM certificazione WHERE id = $id");
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
  
 
      if (empty($data)) {
        $raw = (string)$request->getBody();
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
          $data = $decoded;
        } else {
          $response->getBody()->write(json_encode(['error' => 'Invalid JSON']));
          return $response->withHeader('Content-type', 'application/json')->withStatus(400);
        }
      }
  
      $nome = isset($data['nome']) ? $data['nome'] : '';
      $cognome = isset($data['cognome']) ? $data['cognome'] : '';
  
      $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
  

      $stmt = $mysqli_connection->prepare("INSERT INTO certificazione (nome, cognome) VALUES (?, ?)");
      $stmt->bind_param('ss', $nome, $cognome);
      $result = $stmt->execute();
  
      if($result){
        $insertId = $stmt->insert_id;
        $stmt->close();
        $mysqli_connection->close();
        $response->getBody()->write(json_encode(['id' => $insertId, 'nome' => $nome, 'cognome' => $cognome]));
        return $response->withHeader("Content-type", "application/json")->withStatus(201);
      } else {
        $err = $mysqli_connection->error;
        $stmt->close();
        $mysqli_connection->close();
        $response->getBody()->write(json_encode(['error' => $err]));
        return $response->withHeader("Content-type", "application/json")->withStatus(500);
      }
    }


  public function update(Request $request, Response $response, $args){
    $id = isset($args['id']) ? (int)$args['id'] : 0;
    if ($id <= 0) {
      $response->getBody()->write(json_encode(['error' => 'Invalid id']));
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }

    $data = $request->getParsedBody();
    if (empty($data)) {
      $raw = (string)$request->getBody();
      $decoded = json_decode($raw, true);
      if (json_last_error() === JSON_ERROR_NONE) {
        $data = $decoded;
      } else {
        $response->getBody()->write(json_encode(['error' => 'Invalid JSON']));
        return $response->withHeader('Content-type', 'application/json')->withStatus(400);
      }
    }

    $fields = [];
    $params = [];
    $types = '';

    if (isset($data['nome'])) {
      $fields[] = 'nome = ?';
      $params[] = $data['nome'];
      $types .= 's';
    }
    if (isset($data['cognome'])) {
      $fields[] = 'cognome = ?';
      $params[] = $data['cognome'];
      $types .= 's';
    }

    if (empty($fields)) {
      $response->getBody()->write(json_encode(['error' => 'No fields to update']));
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');

    $sql = "UPDATE certificazione SET " . implode(', ', $fields) . " WHERE id = ?";
    $types .= 'i';
    $params[] = $id;

    $stmt = $mysqli_connection->prepare($sql);
    if (!$stmt) {
      $err = $mysqli_connection->error;
      $mysqli_connection->close();
      $response->getBody()->write(json_encode(['error' => $err]));
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }


    $bind_names[] = $types;
    for ($i = 0; $i < count($params); $i++) {
      $bind_names[] = & $params[$i];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    $exec = $stmt->execute();
    if ($exec) {
      $affected = $stmt->affected_rows;
      $stmt->close();
      $mysqli_connection->close();
      if ($affected > 0) {
        $response->getBody()->write(json_encode(['message' => 'Updated', 'id' => $id]));
        return $response->withHeader("Content-type", "application/json")->withStatus(200);
      } else {

        $response->getBody()->write(json_encode(['message' => 'No changes made or id not found']));
        return $response->withHeader("Content-type", "application/json")->withStatus(200);
      }
    } else {
      $err = $stmt->error ?: $mysqli_connection->error;
      $stmt->close();
      $mysqli_connection->close();
      $response->getBody()->write(json_encode(['error' => $err]));
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }

  public function destroy(Request $request, Response $response, $args){
    $id = isset($args['id']) ? (int)$args['id'] : 0;
    if ($id <= 0) {
      $response->getBody()->write(json_encode(['error' => 'Invalid id']));
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $stmt = $mysqli_connection->prepare("DELETE FROM certificazione WHERE id = ?");
    if (!$stmt) {
      $err = $mysqli_connection->error;
      $mysqli_connection->close();
      $response->getBody()->write(json_encode(['error' => $err]));
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }

    $stmt->bind_param('i', $id);
    $exec = $stmt->execute();
    if ($exec) {
      $affected = $stmt->affected_rows;
      $stmt->close();
      $mysqli_connection->close();
      if ($affected > 0) {
        $response->getBody()->write(json_encode(['message' => 'Deleted', 'id' => $id]));
        return $response->withHeader("Content-type", "application/json")->withStatus(200);
      } else {
        $response->getBody()->write(json_encode(['error' => 'Not found']));
        return $response->withHeader("Content-type", "application/json")->withStatus(404);
      }
    } else {
      $err = $stmt->error ?: $mysqli_connection->error;
      $stmt->close();
      $mysqli_connection->close();
      $response->getBody()->write(json_encode(['error' => $err]));
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }
}