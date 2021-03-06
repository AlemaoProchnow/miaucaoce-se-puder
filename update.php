<?php

    session_start();

    require_once('settings/check.php');

?>

<!DOCTYPE html>

<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/login.css">
        <link rel="icon" href="images/logomarcaPreta.png" type="image/x-icon">
        <title>MiauCãoce se Puder</title>
    </head>

    <body>
        <div class="loginPage">
            <a href="profile.php">
                <div class="imageLogo">
                    <img id="headerImage" src="images/logomarcaPequena.png" title="Voltar ao Perfil">
                </div>
            </a>

            <?php
                require_once('settings/config.php');
                require_once('class/Update.php');
                require_once('class/Alertas.php');

                try {
                    if (isset($_POST['register'])) {
                        $update = new Update();
                        $alert = new Alertas();

                        $queryOfVerification = 'SELECT COUNT(usuario_nickname) AS qtd_usuario FROM usuario WHERE usuario_nickname =:nickname';
                        $verificationExecute = $connection -> prepare($queryOfVerification);

                        $verificationExecute -> bindValue(':nickname', $update -> higienizarDados($_POST['registerNickname']));

                        $verificationExecute -> execute();
                        $resultOfVerification = $verificationExecute -> fetch(PDO::FETCH_ASSOC);

                        if (empty($_POST['registerNickname']) || empty($_POST['registerPassword']) || empty($_POST['registerUsername']) || empty($_POST['registerEmail']) || empty($_POST['registerTelefone']) || empty($_POST['registerEstado']) || empty($_POST['registerCidade'])) {
                            print $alert -> errorMessage('Campos com * são de preenchimento obrigatório.');
                        } elseif ($resultOfVerification['qtd_usuario'] > 0) { 
                            print $alert -> errorMessage('Este nome de usuário já existe. Por favor, selecione outro.');
                        } elseif ($update -> contarCaracteresDaStringInserida($_POST['registerNickname']) > 80) {
                            print $alert -> errorMessage('Número inválido de caracteres. O campo Usuário pode conter no máximo 80 caracteres.');
                        } elseif ($update -> contarCaracteresDaStringInserida($_POST['registerTelefone']) > 80) {
                            print $alert -> errorMessage('Número inválido de caracteres. O campo Telefone pode conter no máximo 80 caracteres.'); 
                        } elseif ($update -> contarCaracteresDaStringInserida($_POST['registerEstado']) > 200) {
                            print $alert -> errorMessage('Número inválido de caracteres. O campo Estado pode conter no máximo 200 caracteres.'); 
                        } elseif ($update -> contarCaracteresDaStringInserida($_POST['registerCidade']) > 200) {
                            print $alert -> errorMessage('Número inválido de caracteres. O campo Cidade pode conter no máximo 200 caracteres.'); 
                        } elseif ($update -> contarCaracteresDaStringInserida($_POST['registerPassword']) > 20) {
                            print $alert -> errorMessage('Número inválido de caracteres. O campo Senha pode conter no máximo 20 caracteres.');
                        } elseif ($update -> higienizarDados($_POST['registerPassword']) != $update -> higienizarDados($_POST['registerPasswordConfirmation'])) {
                            print $alert -> errorMessage('As senhas que foram inseridas são diferentes. Insira as mesmas senhas nos campos Senha e Confirme sua Senha');
                        } elseif ($update -> contarCaracteresDaStringInserida($_POST['registerUsername']) > 200) {
                            print $alert -> errorMessage('Número inválido de caracteres. O campo Nome Completo pode conter no máximo 200 caracteres.');
                        } elseif ($update -> contarCaracteresDaStringInserida($_POST['registerEmail']) > 200) {
                            print $alert -> errorMessage('Número inválido de caracteres. O campo E-mail pode conter no máximo 200 caracteres.');
                        } else {
                            $query = 'UPDATE usuario SET usuario_nickname=:nickname, usuario_senha=:senha, usuario_nome_completo=:nomeCompleto, usuario_email=:email, usuario_telefone=:telefone, usuario_estado=:estado, usuario_cidade=:cidade WHERE cod_usuario=:codUsuario';

                            $submitData = $connection -> prepare($query);

                            $submitData -> bindValue(':nickname', $update -> higienizarDados($_POST['registerNickname']));
                            $submitData -> bindValue(':senha', $update -> criptografarSenha($update -> higienizarDados($_POST['registerPassword'])));
                            $submitData -> bindValue(':nomeCompleto', $update -> higienizarDados($_POST['registerUsername']));
                            $submitData -> bindValue(':telefone', $update -> higienizarDados($_POST['registerTelefone']));
                            $submitData -> bindValue(':estado', $update -> higienizarDados($_POST['registerEstado']));
                            $submitData -> bindValue(':cidade', $update -> higienizarDados($_POST['registerCidade']));
                            $submitData -> bindValue(':email', $update -> higienizarDados($_POST['registerEmail']));
                            $submitData -> bindValue(':codUsuario', $_SESSION['codUsuario']);

                            if ($submitData -> execute()) {
                                print $alert -> successMessage('Perfil atualizado de forma bem-sucedida!');

                                print "<div class='link-login-from-register-page'><a href='profile.php'>Voltar</a></div>";
                            } else {
                                print $alert -> errorMessage('Não foi possível editar o perdil. Por favor, tente novamente mais tarde.');
                            }
                        }
                    }
                } catch (PDOException $error) {
                    print 'Conexão falhou! ' . $error -> getMessage();
                }
            ?>

            <form method="POST" role="form" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>>
                <fieldset id="formLogin">

                    <p class="UserLogin">
                        <label for="userLogin">Usuário*</label>
                        <input class="userLogin" id="userLogin" name="registerNickname" type="text" aria-label="Usuário" placeholder="Usuário*">
                    </p>

                    <p class="UserLogin">
                        <label for="userPassword">Senha*</label>
                        <input class="userLogin" id="userPassword" name="registerPassword"  type="password" aria-label="Senha" placeholder="Senha*">
                    </p>

                    <p class="UserLogin">
                        <label for="userPassword">Confirme sua Senha*</label>
                        <input class="userLogin" id="userPassword" name="registerPasswordConfirmation" type="password" aria-label="Senha" placeholder="Confirme sua Senha*">
                    </p>

                    <p class="UserLogin">
                        <label for="userName">Nome Completo*</label>
                        <input class="userLogin" id="userName" name="registerUsername" type="text" aria-label="Nome Completo" placeholder="Nome Completo*">
                    </p>

                    <p class="UserLogin">
                        <label for="userEmail">E-mail para contato*</label>
                        <input class="userLogin" id="userEmail" name="registerEmail" type="email" aria-label="E-mail" placeholder="E-mail*">
                    </p>

                    <p class="UserLogin">
                        <label for="userTelefone">Telefone/Celular*</label>
                        <input class="userLogin" id="userTelefone" name="registerTelefone" type="text" aria-label="Telefone/Celular" placeholder="Telefone/Celular*">
                    </p>

                    <p class="UserLogin">
                        <label for="userEstado">Estado*</label>
                        <input class="userLogin" id="userEstado" list="estados" name="registerEstado" type="text" aria-label="Estado" placeholder="Estado*">

                        <datalist id="estados">
                            <optgroup label="Região Norte">
                                <option>Acre</option>
                                <option>Amapá</option>
                                <option>Amazonas</option>
                                <option>Pará</option>
                                <option>Rondônia</option>
                                <option>Roraima</option>
                                <option>Tocantins</option>
                            </optgroup>

                            <optgroup label="Região Nordeste">
                                <option>Alagoas</option>
                                <option>Bahia</option>
                                <option>Ceará</option>
                                <option>Maranhão</option>
                                <option>Paraíba</option>
                                <option>Pernambuco</option>
                                <option>Piauí</option>
                                <option>Rio Grande do Norte</option>
                                <option>Sergipe</option>
                            </optgroup>

                            <optgroup label="Região Centro-Oeste">
                                <option>Goiás</option>
                                <option>Mato Grosso</option>
                                <option>Mato Grosso do Sul</option>
                                <option>Distrito Federal</option>
                            </optgroup>

                            <optgroup label="Região Sudeste">
                                <option>Espírito Santo</option>
                                <option>Minas Gerais</option>
                                <option>São Paulo</option>
                                <option>Rio de Janeiro</option>
                            </optgroup>

                            <optgroup label="Região Nordeste">
                                <option>Paraná</option>
                                <option>Rio Grande do Sul</option>
                                <option>Santa Catarina</option>
                            </optgroup>
                        </datalist>
                    </p>

                    <p class="UserLogin">
                        <label for="userCidade">Cidade*</label>
                        <input class="userLogin" id="userCidade" name="registerCidade" type="text" aria-label="Cidade" placeholder="Cidade*" list="cidades">

                        <datalist id="cidades">

                            <?php
                            
                                try {

                                    $query = 'SELECT usuario_cidade FROM usuario';
                                    $selectData = $connection -> prepare($query);
                                    $selectData -> execute();
                                    
                                    while ($cities = $selectData -> fetch(PDO::FETCH_ASSOC)) {

                                        extract($cities);


                                        print "<option>{$usuario_cidade}</option>";

                                    }


                                } catch (PDOException $error) {

                                    print 'Conexão falhou!' . $error -> getMessage();

                                }
                            
                            ?>
                            
                        </datalist>
                    </p>

                    <input type="submit" value="Atualizar Perfil" name="register" class="enterButton">
                </fieldset>
            </form>
            <div id="rodapePicture"></div>
        </div>
    </body>
</html>