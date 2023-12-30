//Sensor de Temperatura DHT11
#include "DHT.h"
 
//Variaveis de recebimento de dados da Porta Serial
static String umid_Recebida;
static float umid_Ideal;
 
String temp_Recebido;
static float tempe_Ideal;
 
//Sensor de UMIDADE
int sensor_Umid = A0;
 
//Sensor de Temperatura
#define DHTPIN 7
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);
 
 
int lamp = 4;
int valvula =  9;
int vent = 5;
 
void setup() {
  //Iniciando a porta Serial
  Serial.begin(9600);
 
  //Iniando os Sensores
  pinMode(sensor_Umid, INPUT);
  pinMode(lamp, OUTPUT);
  dht.begin();
 
  //Definindo Componentes
  pinMode(valvula, OUTPUT);
  pinMode(vent, OUTPUT);
  //Ligando a Lampada
  digitalWrite(lamp, HIGH);
 
  while (!Serial) {
    ; // Aguardar pela conexão
  }
}
 
void loop() {
 
  ReceberDados();
  Controle();
  ControleLampada();
}
 
void ReceberDados(){

 
    while (Serial.available()) {
    String dados = Serial.readStringUntil('\n');
 
    if (dados.startsWith("UmidadeIdeal:")) {
      umid_Recebida = dados.substring(13);
        umid_Ideal = umid_Recebida.toFloat();
 
    }
 
       if (dados.startsWith("TempIdeal:")) {
      temp_Recebido = dados.substring(10);
        tempe_Ideal = temp_Recebido.toFloat();
 
    }


  }

  if(umid_Recebida != "" && temp_Recebido != ""){
    Serial.println(umid_Ideal);
    Serial.println(tempe_Ideal);
    umid_Recebida = "";
    temp_Recebido = "";
  }

 
}
 
void Controle(){
  //Variaveis Medidoras
  float nv_Umidade = analogRead(sensor_Umid);
  float Umidade_Atual = map(nv_Umidade, 0, 1023, 0, 100);

 float tempe_Atual = dht.readTemperature();
  

  //Manda os niveis de humidade e temperatura para a porta serial
  Serial.print(tempe_Atual);
  Serial.print(",");
  Serial.println(Umidade_Atual);


  //Controle da Valvula
  if(Umidade_Atual < umid_Ideal){
    
    digitalWrite(valvula, HIGH);
  }
 
  if(Umidade_Atual >= umid_Ideal){
    digitalWrite(valvula, LOW);
 
  }
 
  //Controle da Ventuinha
  if(tempe_Atual > tempe_Ideal){
    digitalWrite(vent, LOW);
  }
 
  if(tempe_Atual <= tempe_Ideal){
    digitalWrite(vent, HIGH);
  }



 delay(2000);     
 
}
void ControleLampada(){
    digitalWrite(lamp, HIGH);
    delay(12 * 60 * 60 * 1000);
    digitalWrite(lamp, LOW);

}