const express = require('express');
const { SerialPort } = require('serialport');
const mysql = require('mysql');
const { ReadlineParser } = require('@serialport/parser-readline');
const fs = require('fs');
const app = express();

// Configuração da porta serial para se comunicar com o Arduino
const port = new SerialPort('/dev/ttyACM0', { baudRate: 9600 });
const parser = port.pipe(new ReadlineParser({ delimiter: '\r\n' }));

// Configuração do banco de dados MySQL
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'estufa',
});

db.connect((err) => {
  if (err) {
    console.error('Erro ao conectar ao banco de dados: ' + err);
  } else {
    console.log('Conectado ao banco de dados');
  }
});

// Comandos API
app.post('/EnviarDados', (req, res) => {
  // Recebe o número de série da estufa como parâmetro
  const { nSerie } = req.query;

  // Verifica se o número de série foi recebido
  if (!nSerie) {
    return res.status(400).send('Número de série da estufa não especificado');
  }

  // Lê o conteúdo do arquivo correspondente à estufa especificada
  const conteudo = fs.readFileSync(`dadosEstufas/estufa - ${nSerie}.txt`, 'utf-8');
  const id_planta = parseInt(conteudo.trim());

  // Consulta SQL para obter os dados ideais
  const query = 'SELECT umidade_ideal, temperatura_ideal FROM planta WHERE id_planta = ?';

  db.query(query, [id_planta], (err, results) => {
    if (err) {
      console.error('Erro ao consultar o banco de dados: ' + err);
      res.status(500).send('Erro ao consultar o banco de dados');
    } else if (results.length > 0) {
      const UmidadeIdeal = results[0].umidade_ideal;
      const TempIdeal = results[0].temperatura_ideal;

      // Envie 'nome' e 'nivel_de_umidade' para o Arduino pela porta serial
      port.write(`UmidadeIdeal:${UmidadeIdeal}\n`);
      port.write(`TempIdeal:${TempIdeal}\n`);

      console.log(`UmidadeIdeal: ${UmidadeIdeal}`);
      console.log(`TempIdeal: ${TempIdeal}`);

      console.log("DADOS ENVIADOS COM SUCESSO");
      res.status(200).send('Dados enviados com sucesso');
    } else {
      console.error('Nenhum dado encontrado na tabela');
      res.status(404).send('Nenhum dado encontrado na tabela');
    }
  });
});

app.get('/ReceberDados', (req, res) => {
  parser.on('data', (data) => {
    const [temp, humid] = data.split(',');
    SalvarDados(req.query.nSerie, temp, humid); // Passa o número de série da estufa como parâmetro
  });
});

// Função para salvar dados no arquivo TXT
function SalvarDados(nSerie, temp, humid) {
  const timestamp = new Date();
  const ano = timestamp.getFullYear();
  const mes = timestamp.getMonth() + 1; // Meses são de 0 a 11
  const dia = timestamp.getDate();
  const hora = timestamp.getHours();
  const minutos = timestamp.getMinutes();
  const segundos = timestamp.getSeconds();

  const dados = `${ano}/${mes}/${dia} ${hora}:${minutos}:${segundos} ${temp} ${humid}\n`;

  // Escreve no arquivo correspondente à estufa
  fs.appendFile(`dadosEstufas/estufa - ${nSerie}.txt`, dados, (err) => {
    if (err) {
      console.error('ERRO AO SALVAR DADOS: ' + err);
    } else {
      console.log("DADOS SALVOS");
    }
  });
}

// Inicia o servidor na porta 3000
app.listen(3000, () => {
  console.log('API Node.js rodando na porta 3000');
});
